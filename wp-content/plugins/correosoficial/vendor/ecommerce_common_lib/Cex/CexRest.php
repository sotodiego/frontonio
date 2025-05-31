<?php
/**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 */

/**
 * @uses: provide methods to request Rest calls to CEX WebServices
 * @version: 1
 *
 */
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/WSValidationResponse.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialCustomerDataDao.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/CorreosOficialUtils.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Commons/BridgeCEXAdapter.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/DetectPlatform.php';

use CorreosOficialCommonLib\Commons\CorreosOficialCrypto;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class CexRest
{

    /**
     * Constructor. Realiza las operaciones necesarias según la plataforma.
     * @return void
     */
    public function __construct()
    {
        if (DetectPlatform::isWordPress()) {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'alta_cliente_CEX') {
                add_action('wp_ajax_alta_recogida_CEX', $this->altaClienteCEX($_REQUEST['codes_id']));
            }
        }
        if (DetectPlatform::isPrestashop()) {
            if (Tools::getValue('action') == 'alta_cliente_CEX') {
                $this->altaClienteCEX($_REQUEST['codes_id']);
            }
        }
    }

    /* *********************************************************************************************************
     * PREREGISTRO UTILIDADES
     ********************************************************************************************************* */
    public function registrarEnvioUtilidades($shipping_data, $id_sender = false)
    {
        // Entrega en Oficina
        $id_office = "";
        if ($shipping_data['product']['product_type'] == 'office') {
            $id_office = $shipping_data['order']['office'];
        }

        $url_grabar_envio_CEX = CEX_GRABAR_ENVIO;

        // Bultos
        $bultos = $shipping_data['bultos'];

        /*
         * Código postal nacional/internacional SENDERS.
         * Para Andorra se trata como nacional
         * Para Portugal se cogen los 4 primeros dígitos
         */
        if ($shipping_data['default_sender']['sender_iso_code_pais'] == 'ES') {
            $codPosNacRte = $shipping_data['default_sender']['sender_cp'];
            $codPosIntRte = "";
        } else if ($shipping_data['default_sender']['sender_iso_code_pais'] == 'PT') {
            $codPosNacRte = "";
            $codPosIntRte = substr($shipping_data['default_sender']['sender_cp'], 0, 4);
        } else {
            $codPosNacRte = "";
            $codPosIntRte = $shipping_data['default_sender']['sender_cp'];
        }

        /*
         * Código postal nacional/internacional CUSTOMERS.
         * Para Andorra se trata como nacional
         * Para Portugal se cogen los 4 primeros dígitos
         */
        if ($shipping_data['customer']['delivery_country_iso'] == 'ES') {
            $codPosNacDest = $shipping_data['customer']['delivery_postcode'];
            $codPosIntDest = "";
        } else if ($shipping_data['customer']['delivery_country_iso'] == 'PT') {
            $codPosNacDest = "";
            $codPosIntDest = substr($shipping_data['customer']['delivery_postcode'], 0, 4);
        } else {
            $codPosNacDest = "";
            $codPosIntDest = $shipping_data['customer']['delivery_postcode'];
        }

        // // Contrareembolso
        $reembolso = "";
        if (isset($shipping_data['order']['payment_method']) && $shipping_data['order']['payment_method'] == "cod") {
            $reembolso = $shipping_data['order']['cash_on_delivery_value'];
        }

        // Seguro
        $seguro = "";

        // Establecemos texto alternativo del remitente configurado en Ajustes
        $label_alternative_text = CorreosOficialConfigDao::getLabelAlternartiveText();

        if ($label_alternative_text != false) {
            $nomRte = $label_alternative_text;
        } else {
            $nomRte = $shipping_data['default_sender']['sender_name'];
        }

        $data = array(
            "solicitante" => "P" . $shipping_data['client'][0]['CEXCustomer'],
            "canalEntrada" => "",
            "numEnvio" => '',
            "ref" => $shipping_data['order']['id_order'] . " " . $shipping_data['order']['reference'],
            "refCliente" => "MODULO_" . PLATFORM . "_" . VERSION . "/" . CORREOS_OFICIAL_VERSION,
            "fecha" => date('dmY'),

            "codRte" => $shipping_data['client'][0]['CEXCustomer'],
            "nomRte" => $nomRte,
            "nifRte" => $shipping_data['default_sender']['sender_nif_cif'],
            "dirRte" => $shipping_data['default_sender']['sender_address'],
            "pobRte" => $shipping_data['default_sender']['sender_city'],
            "codPosNacRte" => $codPosNacRte,
            "paisISORte" => $shipping_data['default_sender']['sender_iso_code_pais'],
            "codPosIntRte" => $codPosIntRte,
            "contacRte" => $shipping_data['default_sender']['sender_contact'],
            "telefRte" => $shipping_data['default_sender']['sender_phone'],
            "emailRte" => $shipping_data['default_sender']['sender_email'],

            "codDest" => $id_office,
            "nomDest" => $shipping_data['customer']['customer_firstname'] . " " . $shipping_data['customer']['customer_lastname'],
            "nifDest" => $shipping_data['customer']['customer_dni'],
            "dirDest" => $shipping_data['customer']['delivery_address'] . " " . $shipping_data['customer']['delivery_address2'],
            "pobDest" => $shipping_data['customer']['delivery_city'],
            "codPosNacDest" => $codPosNacDest,
            "paisISODest" => $shipping_data['customer']['delivery_country_iso'],
            "codPosIntDest" => $codPosIntDest,
            "contacDest" => $shipping_data['customer']['delivery_country_iso'],
            "telefDest" => $shipping_data['customer']['phone'],
            "emailDest" => $shipping_data['customer']['customer_email'],

            "contacOtrs" => "",
            "telefOtrs" => "",
            "emailOtrs" => "",
            "observac" => "",
            "numBultos" => $bultos,
            "kilos" => "",
            "volumen" => "",
            "alto" => "",
            "largo" => "",
            "ancho" => "",
            "producto" => $shipping_data['order']['id_product'],
            "portes" => "P",
            "reembolso" => $reembolso,
            "entrSabado" => 'N',
            "seguro" => $seguro,
            "numEnvioVuelta" => "",
            "listaBultos" => [],
            "codDirecDestino" => $id_office,
            "password" => "",
            "listaInformacionAdicional" => []
        );

        $total_weight = 0;

        for ($i = 1; $i <= $bultos; $i++) {
            $interior = array();
            $interior['alto'] = "";
            $interior['ancho'] = "";
            $interior['codBultoCli'] = $i;
            $interior['codUnico'] = "";
            $interior['descripcion'] = "";
            $interior['kilos'] = "";
            $interior['largo'] = "";
            $interior['observaciones'] = "";
            $interior['orden'] = $i;
            $interior['referencia'] = "";
            $interior['volumen'] = "";
            $data["listaBultos"][] = $interior;
            $total_weight = $total_weight + floatval(1);
        }

        $data["kilos"] = $total_weight;

        $lista = new stdClass();
        $lista->tipoEtiqueta = "5";
        $lista->etiquetaPDF = "N";
        $lista->posicionEtiqueta = "";
        $lista->hideSender = "0";

        $lista->logoCliente = "";

        $lista->codificacionUnicaB64 = "1";
        $lista->textoRemiAlternativo = "";
        $lista->idioma = "ES";
        $lista->creaRecogida = $shipping_data['pickup'];
        $lista->fechaRecogida = date('dmY', strtotime($shipping_data['PickupDate']));
        $lista->horaDesdeRecogida = substr($shipping_data['PickupFrom'], 0, 5);
        $lista->horaHastaRecogida = substr($shipping_data['PickupTo'], 0, 5);
        $lista->referenciaRecogida = $shipping_data['order']['reference'];

        // Codigo AT opcional para los envíos PORTUGAL-PORTUGAL
        if ($shipping_data['default_sender']['sender_iso_code_pais'] == 'PT' && $shipping_data['customer']['delivery_country_iso'] == 'PT') {
            $lista->codigoAT = $shipping_data['AT_code'];
        }

        $data["listaInformacionAdicional"][] = $lista;

        $response = $this->requestRestCall($url_grabar_envio_CEX, $data, $id_sender);

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO CEX grabarEnvio - Utilidades', $data);
        $response_decoded = json_decode($response['output'], true);
        $validation = WSValidationResponse::validateRestRequest($response);
        $result = array(
            'codigoRetorno' => $response_decoded['codigoRetorno'],
            'mensajeRetorno' => mb_convert_encoding($response_decoded['mensajeRetorno'],'UTF-8','ISO-8859-1'),
            'json_retorno' => $response['output'],
            'status_code' => $response['status']
        );

        CorreosOficialUtils::varDump('RESPUESTA CEX grabarEnvio - Utilidades', $result);
        
        return $result;
    }

    /* *********************************************************************************************************
     * PREREGISTRO PEDIDO
     ********************************************************************************************************* */
    public function registrarEnvio($shipping_data, $id_sender = false)
    {        
        // Entrega en Oficina
        $id_office = "";
        if ($shipping_data['delivery_mode'] == 'office') {
            $id_office = $shipping_data['order_form']['cod_office'];
        }

        $url_grabar_envio_CEX = CEX_GRABAR_ENVIO;

        // Bultos
        $bultos = $shipping_data['bultos'];

        //Código postal nacional/internacional SENDERS.
        if ($shipping_data['order_form']['sender_country'] == 'ES') {
            $codPosNacRte = $shipping_data['order_form']['sender_cp'];
            $codPosIntRte = "";
        } elseif ($shipping_data['order_form']['sender_country'] == 'PT') {
            $codPosNacRte = "";
            $codPosIntRte = substr($shipping_data['order_form']['sender_cp'], 0, 4);
        } else {    
            $codPosNacRte = "";
            $codPosIntRte = $shipping_data['order_form']['sender_cp'];
        }

        /*
         * Código postal nacional/internacional CUSTOMERS.
         * Para Andorra se trata como nacional
         * Para Portugal se cogen los 4 primeros dígitos
         */
        if ($shipping_data['order_form']['customer_country'] == 'ES') {
            $codPosNacDest = $shipping_data['order_form']['customer_cp'];
            $codPosIntDest = "";
        } else if ($shipping_data['order_form']['customer_country'] == 'PT') {
            $codPosNacDest = "";
            $codPosIntDest = substr($shipping_data['order_form']['customer_cp'], 0, 4);
        } else {
            $codPosNacDest = "";
            $codPosIntDest = $shipping_data['order_form']['customer_cp'];
        }

        // Contrareembolso
        $check_reembolso = $shipping_data['order_form']['contrareembolsoCheckbox'];
        if ($check_reembolso == 1) {
            $reembolso = $shipping_data['order_form']['cash_on_delivery_value'];
        } else {
            $reembolso = "";
        }

        // Seguro
        $check_seguro = $shipping_data['order_form']['seguroCheckbox'];
        if ($check_seguro == 1) {
            $seguro = $shipping_data['order_form']['insurance_value'];
        } else {
            $seguro = "";
        }

        // Establecemos texto alternativo del remitente configurado en Ajustes
        $label_alternative_text = CorreosOficialConfigDao::getLabelAlternartiveText();

        if ($label_alternative_text != false) {
            $nomRte = $label_alternative_text;
        } else {
            $nomRte = $shipping_data['order_form']['sender_name'];
        }

        if (isset($shipping_data['order_form']['customer_company'])) {
            $nomDestAndCompany = $shipping_data['order_form']['customer_firstname'] . " " . $shipping_data['order_form']['customer_lastname'] . " " . $shipping_data['order_form']['customer_company'];
        } else {
            $nomDestAndCompany = $shipping_data['order_form']['customer_firstname'] . " " . $shipping_data['order_form']['customer_lastname'];
        }

        if ($shipping_data['order_form']['customer_contact'] != '') {
            $contactDest = $shipping_data['order_form']['customer_contact'];
        } else {
            $contactDest = $shipping_data['order_form']['customer_firstname'] . " " . $shipping_data['order_form']['customer_lastname'];
        }

        $data = array(
            "solicitante" => "P" . $shipping_data['client'][0]['CEXCustomer'],
            "canalEntrada" => "",
            "numEnvio" => '',
            "ref" => $shipping_data['order_form']['order_number'] . " " . $shipping_data['order_form']['order_reference'],
            "refCliente" => "MODULO_" . PLATFORM . "_" . VERSION . "/" . CORREOS_OFICIAL_VERSION,
            "fecha" => date('dmY'),
            
            "codRte" => $shipping_data['client'][0]['CEXCustomer'],
            "nomRte" => $nomRte,
            "nifRte" => $shipping_data['order_form']['sender_nif_cif'],
            "dirRte" => $shipping_data['order_form']['sender_address'],
            "pobRte" => $shipping_data['order_form']['sender_city'],
            "codPosNacRte" => $codPosNacRte,
            "paisISORte" => $shipping_data['order_form']['sender_country'],
            "codPosIntRte" => $codPosIntRte,
            "contacRte" => $shipping_data['order_form']['sender_contact'],
            "telefRte" => $shipping_data['order_form']['sender_phone'],
            "emailRte" => $shipping_data['order_form']['sender_email'],

            "codDest" => "",
            "nomDest" => $nomDestAndCompany,
            "nifDest" => $shipping_data['order_form']['customer_dni'],
            "dirDest" => $shipping_data['order_form']['customer_address'],
            "pobDest" => $shipping_data['order_form']['customer_city'],
            "codPosNacDest" => $codPosNacDest,
            "paisISODest" => $shipping_data['order_form']['customer_country'],
            "codPosIntDest" => $codPosIntDest,
            "contacDest" => $contactDest,
            //"telefDest" => str_replace(['0034', '0034 ', '+34', '+34 '], '', $shipping_data['order_form']['customer_phone']),
            "telefDest" => CorreosOficialUtils::cleanTelephoneNumber($shipping_data['order_form']['customer_phone']),
            "emailDest" => $shipping_data['order_form']['customer_email'],

            "contacOtrs" => "",
            "telefOtrs" => "",
            "emailOtrs" => "",
            "observac" => "",
            "numBultos" => $shipping_data['order_form']['correos-num-parcels'],
            "kilos" => "",
            "volumen" => "",
            "alto" => "",
            "largo" => "",
            "ancho" => "",
            "producto" => $shipping_data['order_form']['input_select_carrier'],
            "portes" => "P",
            "reembolso" => $reembolso,
            "entrSabado" => $shipping_data['order_form']['delivery_saturday'] == 0 ? 'N' : 'S',
            "seguro" => $seguro,
            "numEnvioVuelta" => "",
            "listaBultos" => [],
            "codDirecDestino" => $id_office,
            "password" => "",
            "listaInformacionAdicional" => []
        );

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO CEX grabarEnvio', $data);

        $all_packages_equal = $shipping_data['order_form']['all_packages_equal'];
        $total_weight = 0;
        if ($all_packages_equal == 1) {
            for ($i = 1; $i <= $bultos; $i++) {
                $interior = array();
                $interior['alto'] = self::parseMeters($shipping_data['order_form']['packageHeight_1']);
                $interior['ancho'] = self::parseMeters($shipping_data['order_form']['packageWidth_1']);
                $interior['codBultoCli'] = $i;
                $interior['codUnico'] = "";
                $interior['descripcion'] = "";
                $interior['kilos'] = $shipping_data['order_form']['packageWeight_1'];
                $interior['largo'] = self::parseMeters($shipping_data['order_form']['packageLarge_1']);
                $interior['observaciones'] = $shipping_data['order_form']['deliveryRemarks_1'];
                $interior['orden'] = $i;
                $interior['referencia'] = "";
                $interior['volumen'] = "";
                $data["listaBultos"][] = $interior;
                $total_weight = $total_weight + floatval($shipping_data['order_form']['packageWeight_1']);
            }
        } else {
            for ($i = 1; $i <= $bultos; $i++) {
                $interior = array();
                $interior['alto'] = self::parseMeters($shipping_data['order_form']['packageHeight_' . $i]);
                $interior['ancho'] = self::parseMeters($shipping_data['order_form']['packageWidth_' . $i]);
                $interior['codBultoCli'] = $i;
                $interior['codUnico'] = "";
                $interior['descripcion'] = "";
                $interior['kilos'] = $shipping_data['order_form']['packageWeight_' . $i];
                $interior['largo'] = self::parseMeters($shipping_data['order_form']['packageLarge_' . $i]);
                $interior['observaciones'] = $shipping_data['order_form']['deliveryRemarks_' . $i];
                $interior['orden'] = $i;
                $interior['referencia'] = "";
                $interior['volumen'] = "";
                $data["listaBultos"][] = $interior;
                $total_weight = $total_weight + floatval($shipping_data['order_form']['packageWeight_' . $i]);
            }
        }

        $data["kilos"] = number_format($total_weight, 2);

        $lista = new stdClass();
        $lista->tipoEtiqueta = "5";
        $lista->etiquetaPDF = "N";
        $lista->posicionEtiqueta = "";
        $lista->hideSender = "0";

        if ($shipping_data['ChangeLogoOnLabel'] == 'on') {
            if (DetectPlatform::isWordpress()) {
                $imagedata = CorreosOficialConfigDao::getConfigValue('UploadLogoLabels');
            } elseif (DetectPlatform::isPrestashop()){
                $imagedata = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/media/logo_label/" . CorreosOficialConfigDao::getConfigValue('UploadLogoLabels'));
            }
            $base64 = base64_encode($imagedata);
            $lista->logoCliente = $base64;
        } else {
            $lista->logoCliente = "";
        }

        $lista->codificacionUnicaB64 = "1";
        $lista->textoRemiAlternativo = "";
        $lista->idioma = "ES";
        $lista->creaRecogida = $shipping_data['needPickup'];
        $lista->fechaRecogida = date('dmY',strtotime($shipping_data['pickupDateRegister']));
        $lista->horaDesdeRecogida = date('H:i',strtotime($shipping_data['pickupFromRegister']));
        $lista->horaHastaRecogida = date('H:i',strtotime($shipping_data['pickupToRegister']));
        $lista->referenciaRecogida = "";
        if ($shipping_data['needPickup'] === 'S') {
            $lista->referenciaRecogida = $shipping_data['order_form']['order_number'] . " " . $shipping_data['order_form']['order_reference'] . date('dmY');
        }

        // Codigo AT opcional para los envíos PORTUGAL-PORTUGAL
        if($shipping_data['order_form']['sender_country'] == 'PT' && $shipping_data['order_form']['customer_country'] == 'PT') {
            $lista->codigoAT = $shipping_data['order_form']['AT_code'];
        }

        $data["listaInformacionAdicional"][] = $lista;
        CorreosOficialUtils::varDump('LLAMADA A SERVICIO CEX grabarEnvio', $data);
        $response = $this->requestRestCall($url_grabar_envio_CEX, $data, $id_sender);            
        $response_decoded = json_decode($response['output'], true);
        $validation = WSValidationResponse::validateRestRequest($response);

        $result = array(
            'codigoRetorno' => isset($response_decoded['codigoRetorno']) ? $response_decoded['codigoRetorno'] : '18005',
            'mensajeRetorno' => mb_convert_encoding($response_decoded['mensajeRetorno'],'UTF-8','ISO-8859-1'),
            'json_retorno' => $response['output'],
            'status_code' => $response['status']
        );

        CorreosOficialUtils::varDump('RESPUESTA CEX grabarEnvio', $result);

        return $result;
    }

    /* *********************************************************************************************************
     * RECOGIDA PEDIDO
     ********************************************************************************************************* */
    public function registrarRecogida($pickup_data, $id_sender = false)
    {
        $url_grabar_recogida = CEX_GRABAR_RECOGIDA;

        /* Portugal 4 primeros dígitos */
        if ($pickup_data['sender_country'] == 'PT') {
            $pickup_data['sender_cp'] = substr($pickup_data['sender_cp'], 0, 4);
        }

        $data = array(
            "solicitante" => "P" . $pickup_data['client'][0]['CEXCustomer'],
            "password" => "",
            "canalEntrada" => "",
            "refRecogida" => $pickup_data['id_order'] . " " . $pickup_data['order_reference'] . date('dmY'),
            "fechaRecogida" => date('dmY', strtotime($pickup_data['pickup_date'])),
            "horaDesde1" => substr($pickup_data['sender_from_time'], 0, 5),
            "horaDesde2" => "",
            "horaHasta1" => substr($pickup_data['sender_to_time'], 0, 5),
            "horaHasta2" => "",
            "clienteRecogida" => $pickup_data['client'][0]['CEXCustomer'],
            "codRemit" => "",
            "nomRemit" => $pickup_data['sender_name'],
            "nifRemit" => $pickup_data['sender_nif_cif'],
            "dirRecog" => $pickup_data['sender_address'],
            "poblRecog" => $pickup_data['sender_city'],
            "cpRecog" => $pickup_data['sender_cp'],
            "contRecog" => $pickup_data['sender_contact'],
            "tlfnoRecog" => $pickup_data['sender_phone'],
            "oTlfnRecog" => "",
            "emailRecog" => "",
            "observ" => "",
            "tipoServ" => "",
            "codDest" => "",
            "nomDest" => "",
            "nifDest" => "",
            "dirDest" => "",
            "pobDest" => "",
            "cpDest" => "",
            "paisDest" => "",
            "cpiDest" => "",
            "contactoDest" => "",
            "tlfnoDest" => "",
            "emailDest" => "",
            "nEnvio" => "",
            "refEnvio" => "",
            "producto" => $pickup_data['producto'],
            "kilos" => "",
            "bultos" => "",
            "volumen" => "",
            "tipoPortes" => "",
            "importReembol" => "",
            "valDeclMerc" => "",
            "infTec" => "",
            "nSerie" => "",
            "modelo" => "",
            "latente" => ""
        );

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO CEX grabarRecogida', $data);

        $response = $this->requestRestCall($url_grabar_recogida, $data, $id_sender);
        $response_decoded = json_decode($response['output'], true);
        $validation = WSValidationResponse::validateRestRequest($response);
        $result = array(
            'resultado' => $response_decoded['resultado'],
            'codigoRetorno' => $response_decoded['codigoRetorno'],
            'mensajeRetorno' => mb_convert_encoding($response_decoded['mensajeRetorno'],'UTF-8','ISO-8859-1'),
            'json_retorno' => $response['output'],
            'status_code' => $response['status']
        );

        return $result;
    }

    /* *********************************************************************************************************
     * CANCELAR RECOGIDA PEDIDO
     ********************************************************************************************************* */
    public function cancelarRecogida($pickup_data, $id_sender = false)
    {
        $url_anular_recogida = CEX_ANULAR_RECOGIDA;

        $data = array(
            "solicitante" => "P" . $pickup_data['client'][0]['CEXCustomer'],
            "password" => "",
            "keyRecogida" => $pickup_data['codSolicitud'],
            "strTextoAnulacion" => "anulacion",
            "strUsuario" => "",
            "strReferencia" => "",
            "strCodCliente" => "",
            "strFRecogida" => ""
        );

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO CEX cancelarRecogida', $data);
        $response = $this->requestRestCall($url_anular_recogida, $data, $id_sender);
        $response_decoded = json_decode($response['output'], true);
        $validation = WSValidationResponse::validateRestRequest($response);

        if ($response_decoded['codError'] == 0) {
            $mensaje_retorno = 'Su solicitud de recogida ha sido anulada (CEX)';
        } else {
            $mensaje_retorno = $response_decoded['mensError'];
        }

        $result = array(
            'codigoRetorno' => $response_decoded['codError'],
            'mensajeRetorno' => $mensaje_retorno,
            'json_retorno' => $response['output'],
            'status_code' => $response['status']
        );
        CorreosOficialUtils::varDump('RESPUESTA CEX cancelarRecogida', $result);
        return $result;
    }

    /* *********************************************************************************************************
     * CONSULTAR RECOGIDA PEDIDO
     ********************************************************************************************************* */
    public function consultarRecogida($pickup_data, $id_sender = false)
    {
        $url_consultar_recogida = CEX_CONSULTAR_RECOGIDA;

        $data = array(
            "recogida" => $pickup_data['recogida'],
            "codigoCliente" => $pickup_data['codigoCliente'],
            "fecRecogida" => $pickup_data['fecRecogida'],
            "idioma" => $pickup_data['idioma']
        );

        $response = $this->requestRestCall($url_consultar_recogida, $data, $id_sender);
        $response_decoded = json_decode($response['output'], true);
        WSValidationResponse::validateRestRequest($response);

        if (isset($response_decoded['codigoRetorno'])) {
            $codRetorno = $response_decoded['codigoRetorno'];
            $mensaje_retorno = $response_decoded['mensajeRetorno'];
        } elseif (isset($response_decoded['codError']) && $response_decoded['codError'] == 0) {
            $mensaje_retorno = 'Consulta recogida (CEX)';
            $codRetorno = $response_decoded['codError'];
        } elseif (isset($mensaje_retorno)) {
            $mensaje_retorno = $response_decoded['mensError'];
            $codRetorno = $response_decoded['codError'];
        }

        return array(
            'codigoRetorno' => $codRetorno,
            'mensajeRetorno' => $mensaje_retorno,
            'json_retorno' => $response['output'],
            'status_code' => $response['status']
        );
    }

    /* *********************************************************************************************************
     * DEVOLUCIÓN PEDIDO
     ********************************************************************************************************* */
    public function registrarDevolucion($shipping_data, $id_sender = false)
    {

        $url_grabar_envio_CEX = CEX_GRABAR_ENVIO;

        // Bultos
        $bultos = $shipping_data['bultos'];

        // Código postal nacional/internacional Sender
        if ($shipping_data['order_form']['customer_country'] == 'ES') {
            $codPosNacDest = $shipping_data['order_form']['customer_cp'];
            $codPosIntDest = "";
        } elseif ($shipping_data['order_form']['customer_country'] == 'PT') {
            $codPosNacDest = "";
            $codPosIntDest = substr($shipping_data['order_form']['customer_cp'], 0, 4);
        } else {
            $codPosNacDest = "";
            $codPosIntDest = $shipping_data['order_form']['customer_cp'];
        }

        // Código postal nacional/internacional Customer
        if ($shipping_data['order_form']['sender_country'] == 'ES') {
            $codPosNacRte = $shipping_data['order_form']['sender_cp'];
            $codPosIntRte = "";
        } elseif ($shipping_data['order_form']['sender_country'] == 'PT') {
            $codPosNacRte = "";
            $codPosIntRte = substr($shipping_data['order_form']['sender_cp'], 0, 4);
        } else {
            $codPosNacRte = "";
            $codPosIntRte = $shipping_data['order_form']['sender_cp'];
        }

        // Contrareembolso
        $check_reembolso = $shipping_data['order_form']['contrareembolsoCheckbox'];
        if ($check_reembolso == 1) {
            $reembolso = $shipping_data['order_form']['cash_on_delivery_value'];
        } else {
            $reembolso = "";
        }

        // Seguro
        $check_seguro = $shipping_data['order_form']['seguroCheckbox'];
        if ($check_seguro == 1) {
            $seguro = $shipping_data['order_form']['insurance_value'];
        } else {
            $seguro = "";
        }

        // Establecemos texto alternativo del remitente configurado en Ajustes
        $label_alternative_text = CorreosOficialConfigDao::getLabelAlternartiveText();
        if ($label_alternative_text != false) {
            $nomRte = $label_alternative_text;
        } else {
            $nomRte = $shipping_data['order_form']['sender_name'];
        }

        $data = array(
            "solicitante" => "P" . $shipping_data['client'][0]['CEXCustomer'],
            "canalEntrada" => "",
            "numEnvio" => '',
            "ref" => "DEV_" . $shipping_data['order_form']['packageRef_1'],
            "refCliente" => "MODULO_" . PLATFORM . "_" . VERSION . "/" . CORREOS_OFICIAL_VERSION,
            "fecha" => date('dmY'),

            // Remitente es el antiguo destinatario
            "codRte" => $shipping_data['client'][0]['CEXCustomer'],
            "nomRte" => $shipping_data['order_form']['customer_firstname'] . " " . $shipping_data['order_form']['customer_lastname'],
            "nifRte" => $shipping_data['order_form']['customer_dni'],
            "dirRte" => $shipping_data['order_form']['customer_address'],
            "pobRte" => $shipping_data['order_form']['customer_city'],
            "codPosNacRte" => $codPosNacDest,
            "paisISORte" => $shipping_data['order_form']['customer_country'],
            "codPosIntRte" => $codPosIntDest,
            "contacRte" => $shipping_data['order_form']['customer_country'],
            "telefRte" => $shipping_data['order_form']['customer_phone'],
            "emailRte" => $shipping_data['order_form']['customer_email'],

            // Destinatario es el antiguo remitente
            "codDest" => "",
            "nomDest" => $nomRte,
            "nifDest" => $shipping_data['order_form']['sender_nif_cif'],
            "dirDest" => $shipping_data['order_form']['sender_address'],
            "pobDest" => $shipping_data['order_form']['sender_city'],
            "codPosNacDest" => $codPosNacRte,
            "paisISODest" => $shipping_data['order_form']['sender_country'],
            "codPosIntDest" => $codPosIntRte,
            "contacDest" => $shipping_data['order_form']['sender_contact'],
            "telefDest" => $shipping_data['order_form']['sender_phone'],
            "emailDest" => $shipping_data['order_form']['sender_email'],

            "contacOtrs" => "",
            "telefOtrs" => "",
            "emailOtrs" => "",
            "observac" => "",
            "numBultos" => $bultos,
            "kilos" => "",
            "volumen" => "",
            "alto" => "",
            "largo" => "",
            "ancho" => "",
            "producto" => "63", // También realizable con producto 54 Entrega Plus
            "portes" => "P",
            "reembolso" => $reembolso,
            "entrSabado" => $shipping_data['order_form']['delivery_saturday'] == 0 ? 'N' : 'S',
            "seguro" => $seguro,
            "numEnvioVuelta" => "",
            "listaBultos" => [],
            "codDirecDestino" => "",
            "password" => "",
            "listaInformacionAdicional" => []
        );

        $total_weight = 0;

        for ($i = 1; $i <= $bultos; $i++) {
            $interior = array();
            $interior['codBultoCli'] = $i;
            $interior['codUnico'] = "";
            $interior['descripcion'] = "";
            $interior['kilos'] = $shipping_data['order_form']['packageWeightReturn_' . $i];
            $interior['largo'] = self::parseMeters($shipping_data['order_form']['packageLargeReturn_' . $i]);
            $interior['alto'] = self::parseMeters($shipping_data['order_form']['packageHeightReturn_' . $i]);
            $interior['ancho'] = self::parseMeters($shipping_data['order_form']['packageWidthReturn_' . $i]);
            $interior['observaciones'] = "";
            $interior['orden'] = $i;
            $interior['referencia'] = "";
            $interior['volumen'] = "";
            $data["listaBultos"][] = $interior;
            $total_weight = $total_weight + floatval($shipping_data['order_form']['packageWeightReturn_' . $i]);
        }
        $data["kilos"] = $total_weight;

        $lista = new stdClass();
        $lista->tipoEtiqueta = "5";
        $lista->etiquetaPDF = "N";
        $lista->posicionEtiqueta = "";
        $lista->hideSender = "0";
        $lista->logoCliente = "";
        $lista->codificacionUnicaB64 = "1";
        $lista->textoRemiAlternativo = "";
        $lista->idioma = "ES";
        $lista->creaRecogida = $shipping_data['needPickup'];
        $lista->fechaRecogida = date('dmY',strtotime($shipping_data['pickup_date']));
        $lista->horaDesdeRecogida = date('H:i',strtotime($shipping_data['sender_from_time']));
        $lista->horaHastaRecogida = date('H:i',strtotime($shipping_data['sender_to_time']));
        $lista->referenciaRecogida = "";
        if ($shipping_data['needPickup'] === 'S') {
            $lista->referenciaRecogida = $shipping_data['order_form']['order_number'] . " " . $shipping_data['order_form']['order_reference'] . date('dmY');
        }

        // Excepto los envíos PORTUGAL-PORTUGAL
        if (!($shipping_data['order_form']['customer_country'] == 'PT' && $shipping_data['order_form']['sender_country'] == 'PT')) {
            // Si origen o destino es PORTUGAL se informa el codigo AT
            if ($shipping_data['order_form']['customer_country'] == 'PT' || $shipping_data['order_form']['sender_country'] == 'PT') {
                $lista->codigoAT = $shipping_data['order_form']['code_at'];
            }
        }
        $data["listaInformacionAdicional"][] = $lista;

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO CEX registrarDevolución', $data);

        $response = $this->requestRestCall($url_grabar_envio_CEX, $data, $id_sender);
        $response_decoded = json_decode($response['output'], true);
        WSValidationResponse::validateRestRequest($response);
        $result = array(
            'codigoRetorno' => $response_decoded['codigoRetorno'],
            'mensajeRetorno' => mb_convert_encoding($response_decoded['mensajeRetorno'],'UTF-8','ISO-8859-1'),
            'json_retorno' => $response['output'],
            'status_code' => $response['status']
        );

        CorreosOficialUtils::varDump('RESPUESTA CEX registrarDevolucion', $result);

        return $result;
    }

    /**
     * Realiza la operación de alta de cliente en CEX
     * @return void
     */
    public function altaClienteCEXCall($id_code = false)
    {
        $url_alta_cliente_CEX = CEX_GRABAR_ENVIO;

        $data = array(
            "solicitante" => "",
            "canalEntrada" => "",
            "numEnvio" => "",
            "ref" => "",
            "refCliente" => "",
            "fecha" => "",
            "codRte" => "",
            "nomRte" => "",
            "nifRte" => "",
            "dirRte" => "",
            "pobRte" => "",
            "codPosNacRte" => "",
            "paisISORte" => "",
            "codPosIntRte" => "",
            "contacRte" => "",
            "telefRte" => "",
            "emailRte" => "",
            "codDest" => "",
            "nomDest" => "",
            "nifDest" => "",
            "dirDest" => "",
            "pobDest" => "",
            "codPosNacDest" => "",
            "paisISODest" => "",
            "codPosIntDest" => "",
            "contacDest" => "",
            "telefDest" => "",
            "emailDest" => "",
            "contacOtrs" => "",
            "telefOtrs" => "",
            "emailOtrs" => "",
            "observac" => "",
            "numBultos" => "",
            "kilos" => "",
            "volumen" => "",
            "alto" => "",
            "largo" => "",
            "ancho" => "",
            "producto" => "",
            "portes" => "",
            "reembolso" => "",
            "entrSabado" => "",
            "seguro" => "",
            "numEnvioVuelta" => "",
            "listaBultos" => [],
            "codDirecDestino" => "",
            "password" => "",
            "listaInformacionAdicional" => []
        );

        $interior = array();
        $interior['alto'] = "";
        $interior['ancho'] = "";
        $interior['codBultoCli'] = "1";
        $interior['codUnico'] = "";
        $interior['descripcion'] = "";
        $interior['kilos'] = "";
        $interior['largo'] = "";
        $interior['observaciones'] = "";
        $interior['orden'] = "";
        $interior['referencia'] = "";
        $interior['volumen'] = "";
        $data["listaBultos"][] = $interior;

        $lista = new stdClass();
        $lista->tipoEtiqueta = "";
        $lista->etiquetaPDF = "N";
        $lista->posicionEtiqueta = '';
        $lista->hideSender = "1";
        $lista->logoCliente = "";
        $lista->codificacionUnicaB64 = "1";
        $lista->textoRemiAlternativo = "";
        $lista->idioma = "ES";
        $lista->creaRecogida = "0";
        $lista->fechaRecogida = "";
        $lista->horaDesdeRecogida = "";
        $lista->horaHastaRecogida = "";
        $lista->referenciaRecogida = "";
        $data["listaInformacionAdicional"][] = $lista;

        $retorno = $this->requestRestCall($url_alta_cliente_CEX, $data, false, $id_code);
        $validation = WSValidationResponse::validateRestRequest($retorno);
        return $validation; 
    }

    /**
     * Realiza la operación de alta de cliente en CEX
     * @return void
     */
    public function altaClienteCEX($id_code = false)
    {
        die($this->altaClienteCEXCall($id_code));
    }

    public function requestRestCall($url, $data = null, $id_sender = false, $id_code = false)
    {
        $customer = new CorreosOficialCustomerDataDao();

        if($id_code){
            $id = $id_code;
        } else {
            if (!$id_sender) {
                $id = $customer->getIdByCompany('CEX');
            } else {
                $id = $customer->getIdCodeFromSenderByDao($id_sender, 'cex');
            }
        }

        $cex_user_password = $customer->getUserPassword($id);

        // Si no tenemos user password return con error
        if (!isset($cex_user_password['login'])) {
            return [
                'output' => json_encode(array(
                    'mensajeRetorno' => '',
                    'codigoRetorno' => '401'
                )),
                'status' => 0
            ];
        }

        $user = $cex_user_password['login'];
        $password = CorreosOficialCrypto::decrypt($cex_user_password['password']);

        $postdata = json_encode($data);
        CorreosOficialUtils::varDump("LLAMADA A SERVICIO CEX JSON", $postdata);
        /* $search  = array (":{", "},", "}}");
        $replace = array(":[{", "}],", "}]}");
        $postdata = str_replace ($search, $replace, $postdata); */

        if (is_null($postdata)) {
            throw new \Exception('decoding params');
        }

        $rest = $postdata;

        // iniciamos y componemos la peticion curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($rest)
        ));

        $output = curl_exec($ch);
        $error = curl_error($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get status code
        $info = curl_getinfo($ch);
        $codigo_error = curl_errno($ch);
        curl_close($ch);

        CorreosOficialUtils::varDump("LLAMADA A RESPUESTA CEX JSON", $output);

        $ret = [
            'output' => $output,
            'status' => $status_code
        ];

        if ($status_code != 200) {

            $result = array(
                'status_code' => 0,
                'mensajeRetorno' => CO_TIMEOUT_MSG
            );

            $json = json_encode($result);

            $ret = [
                'output' => $json,
                'status' => $status_code
            ];
        }

        return $ret;
    }

    /**
     * Función de seguimiento de CEX
     * @param array $orders array con los pedidos.
     * @param int Nº de pedidos. False si no hay pedidos
     */
    public function cronTrackingCEX($orders)
    {
        // Se retorna si no hay pedidos 
        if (count($orders) == 0) {
            return false;
        }
        // coger la url
        $url_tracking_CEX = CEX_BASE_LOCATION_LISTA;

        $response = [];
        $cex_codes = [];

        $customer = new CorreosOficialCustomerDataDao();

        foreach ($orders as $order) {
            $customer_code = substr($order['customer_code'], 0, 5);
            $cex_codes[] = $customer->getIdCodeFromOrder($order['id_order'], 'cex');
            $response[] = json_encode([
                "codigoCliente" => $customer_code,
                "nEnvios" => [$order['exp_number']],
                "idioma" => ""
            ]);
        }

        $ret = array(
            'peticion' => $response,//json_encode($rest_request),
            'url' => $url_tracking_CEX,
            'cex_codes' => $cex_codes,
        );
        return $ret;
    }

    public function CronSGATrackingCEX($orders) {
        if (count($orders) == 0) {
            return false;
        }

        $url_tracking_CEX = CEX_BASE_LOCATION_LISTA;
    
        $response = [];
        $cex_codes = [];
    
        $customer = new CorreosOficialCustomerDataDao();
    
        foreach ($orders as $order) {
            $customer_code = substr($customer->getCodeFromSGAOrder('CEX'), 0, 5);
            $cex_codes[] = $customer->getIdCodeFromSGAOrder('CEX');

            if (DetectPlatform::isPrestashop()) {
                $order_number = $order->getWsShippingNumber();
            } else if (DetectPlatform::isWordPress()) {
                $order_number = $this->getWcTrackingNumber($order);
            }

            // Aseguramos que nEnvios sea siempre un array
            $nEnvios = is_array($order_number) ? $order_number : [$order_number];

            $response[] = json_encode([
                "codigoCliente" => $customer_code,
                "nEnvios"       => $nEnvios,
                "idioma"        => ""
            ]);
        }
    
        $ret = array(
            'peticion'   => $response,
            'url'        => $url_tracking_CEX,
            'cex_codes'  => $cex_codes,
        );

        return $ret;
    }

    private function getWcTrackingNumber($order) {
        if (!wc_get_container()->get(CustomOrdersTableController::class)->custom_orders_table_usage_is_enabled()) {
            $order_number = $order->get_meta('correosecom_sga_tracking_number', true);
        } else {
            $order_number = get_post_meta($order->get_id(), 'correosecom_sga_tracking_number', true);
        }

        return $order_number;
    }
    
    /**
     * Función de seguimiento de CEX de PRO (K8s)
     * @param array $shipping_numbers_array array con los pedidos.
     */
    public function TrackingCEXK8s($shipping_number, $json = false)
    {
        $url_tracking_CEX = CEX_BASE_LOCATION;

        $customer = new CorreosOficialCustomerDataDao();
        $cex_user = $customer->getCustomerCodeByCompany('CEX');
        $customer_code = substr($cex_user[0]->customer_code, 0, 5);

        $rest_request = array(
            "codigoCliente" => $customer_code,
            "dato" => $shipping_number,
            "idioma" => ""
        );

        $req = array('peticion' => json_encode($rest_request), 'url' => $url_tracking_CEX);
        $response = $this->trackingCEXRequestCall($req);

        if ($json == true) {
            return $response;
        } else {
            $response = json_decode($response);
            return $response;
        }
    }

    /**
     * Llamada al servicio de seguimiento de CEX.
     * @param array petición: array con los parámetros de la petición al servicio.
     */
    public function trackingCEXRequestCall($peticion)
    {
        if (!is_array($peticion['peticion'])) {
            $customer = new CorreosOficialCustomerDataDao();
            $cex_user_password = $customer->getUserPassword($customer->getIdByCompany('CEX'));

            $user = $cex_user_password['login'];
            $password = CorreosOficialCrypto::decrypt($cex_user_password['password']);

            $url = $peticion['url'];
            $rest = $peticion['peticion'];

            $output = $this->apiTracking($url, $rest, $user, $password);
            return $output;
        } else {
            return $this->trackingCEXRequestCallMultiClient($peticion);
        }
    }

    public function trackingSGACEXRequestCall ($peticion) {

        if (!is_array($peticion['peticion'])) {
            $customer = new CorreosOficialCustomerDataDao();
            $cex_user_password = $customer->getUserPassword($customer->getIdCodeFromSGAOrder('CEX'));

            $user = $cex_user_password['login'];
            $password = CorreosOficialCrypto::decrypt($cex_user_password['password']);

            $url = $peticion['url'];
            $rest = $peticion['peticion'];

            $output = $this->apiTracking($url, $rest, $user, $password);
            return $output;
        } else {
            return $this->trackingCEXRequestCallMultiClient($peticion);
        }
    }

    /**
     * Llamada al servicio de etiquetas de CEX.
     * @param string $shippingNumber número de envío
     * @param string $type tipo de etiqueta (Api Correos)
     * @param string position posición de la etiqueta
     *
     * @return object etiquetas en base64
     */
    public function getLabelFromWS($shippingNumber, $type, $logoBase64, $position = 1, $origin = 'order')
    {
        $customer = new CorreosOficialCustomerDataDao();
        $idCode = $customer->getCodeFromShipping($shippingNumber, 'cex', $origin);
        $cexCustomer = $customer->getAllCode($idCode);
        $password = CorreosOficialCrypto::decrypt($cexCustomer[0]->CEXPassword);

        // Cuerpo de la petición
        $restRequest = array(
            "keyCli" => $cexCustomer[0]->customer_code,
            "nenvio" => $shippingNumber,
            "posicionEtiqueta" => ($position - 1),
            "tipo" => $type,
            "logoCliente" => $logoBase64
        );

        // iniciamos y componemos la peticion curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, CEX_BASE_LABELS);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($restRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $cexCustomer[0]->CEXUser . ":" . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($restRequest))
        ));

        $output = curl_exec($ch);
        $error = curl_error($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get status code
        $info = curl_getinfo($ch);
        $codigo_error = curl_errno($ch);
        curl_close($ch);

        if($status_code == '404') {
            $this->checkForTimeOutAndDie();
        }

        return json_decode($output);
    }

    protected function trackingCEXRequestCallMultiClient($petitions)
    {
        $output = [];
        $customer = new CorreosOficialCustomerDataDao();
        $url = $petitions['url'];
        foreach ($petitions['peticion'] as $key => $petition) {
            $cex_user_password = $customer->getUserPassword($petitions['cex_codes'][$key]);
            $user = $cex_user_password['login'];
            $password = CorreosOficialCrypto::decrypt($cex_user_password['password']);
            $rest = $petition;

            $output[] = $this->apiTracking($url, $rest, $user, $password);
        }
        return $output;
    }

    protected function apiTracking($url, $rest, $user, $password)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($rest)
        ));

        $output = curl_exec($ch);
        $error = curl_error($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get status code
        $info = curl_getinfo($ch);
        $codigo_error = curl_errno($ch);

        curl_close($ch);
        return $output;
    }

    /**
     * Función que transforma centímetros a metros
     * @param string $value son los centímetros que queremos convertir a metros
     * 
     * @return string $metros son los metros
     */ 

    public static function parseMeters($value) {

        if (empty($value)) {
            $metros = 0;
        }  else {
            $metros = intval($value) / 100;
        }

        return (string) $metros;
        
    }

    public static function checkForTimeOutAndDie()
    {
        $result =  array(
            'status_code' => 404,
            'mensajeRetorno' => CO_TIMEOUT_MSG);
        die(json_encode($result));
    }

}
