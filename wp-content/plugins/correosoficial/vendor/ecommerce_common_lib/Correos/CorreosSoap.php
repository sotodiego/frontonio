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
 * @uses Método de WebServices de Correos
 * @version 1
 * @source
 */

use CorreosOficialCommonLib\Commons\CorreosOficialCrypto;
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Commons/CorreosOficialCrypto.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/WSValidationResponse.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialCustomerDataDao.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/CorreosOficialUtils.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/prepareRequests/CorreosOficialPrepareOrderRequests.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/prepareRequests/CorreosOficialPrepareRequestsDocAduanera.php';

require_once dirname(__FILE__) . '/../../../classes/CorreosOficialErrorManager.php';

class CorreosSoap
{

    private $operation;

    /**
     * Constructor. Realiza las operaciones necesarias según la plataforma.
     * @return void
     *
     */
    public function __construct()
    {
        if (DetectPlatform::isWordPress()) {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'alta_cliente_Correos') {
                add_action('wp_ajax_alta_clientes_soap', $this->altaClienteCorreosOp($_REQUEST['codes_id']));
            }
        }

        if (DetectPlatform::isPrestashop()) {
            if (Tools::getValue('action') == 'alta_cliente_Correos') {
                $this->altaClienteCorreosOp($_REQUEST['codes_id']);
            }
        }
    }

    /* **********************************************************************************************************
     *                                  ALTA DE CLIENTES
     *
     ********************************************************************************************************* */
    
     /**
     * Comprueba las credenciales de Correos con una llamada al servicio de Prereregistro
     * operacion DocumentacionAduaneraOp
     * @return validation resultado de la llamada.
     */
     public function altaClienteCorreosOpCall($id_code = false)
     {
        $correos_url_preregistro = Config::getCorreosURL();

        $client = $this->soapClient(false, $id_code);

        $request = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/vendor/ecommerce_common_lib/services/preregistro/documentacion_aduanera.xml');

        $client->__doRequest($request, $correos_url_preregistro, 'DocumentacionAduaneraOp', 1, false);

        CorreosOficialUtils::varDump('LLAMADA A ALTA CLIENTES', $request);

        $headers = $client->__getLastResponseHeaders();
        $retorno['status'] = substr($headers, 9, 3);
        $validation = WSValidationResponse::validateRestRequest($retorno);
        return $validation;
     }
    /**
     * Comprueba las credenciales de Correos desde llamadas AJAXS
     * @return void este método imprime la información  y no devuelve nada.
     */
    public function altaClienteCorreosOp($id_code = false)
    {
        die($this->altaClienteCorreosOpCall($id_code));
    }

    /* *********************************************************************************************************
     * PREREGISTRO PEDIDO
     ********************************************************************************************************* */
    public function registrarEnvio($shipping_data, $origin = null, $id_sender = false)
    {
        $correos_url_preregistro = Config::getCorreosURL();
        $client = $this->soapClient($id_sender);

        $prepare = new CorreosOficialPrepareOrderRequests();
        if ($origin == 'utilities') {
            $request = $prepare->prepareRequestCorreosShippingUtilities($shipping_data);
        } else {
            $request = $prepare->prepareRequestCorreosShipping($shipping_data);
        }

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO PREREGISTRO CORREOS', $request);

        $bultos = $shipping_data['bultos'];

        if ($bultos == 1) {
            $this->operation = 'PreRegistro';
        } else {
            $this->operation = 'PreRegistroMultibulto';
        }

        try {
            $xml = $client->__doRequest($request, $correos_url_preregistro, $this->operation, 1, false);
        } catch (Exception $e) {
            $errores[] = array(
                'id_order' => "N/A",
                'reference' => "N/A",
                'error' => "ERROR 14504: " . $e
            );
            die(json_encode($errores));
        }

        $resultado = '';

        $status_code = $this->getStatusFromLastResponseFromHeaders($client);

        if (!$status_code) {
            return self::checkForTimeOutAndDie();
        }

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);

        if (CorreosOficialUtils::isValidXml($response)) {

            $xml = new SimpleXMLElement($response);

            CorreosOficialUtils::varDump('RESPUESTA DE SERVICIO PREREGISTRO CORREOS', $xml);

            if (isset($xml->soapBody->soapFault->faultstring[0]) && $xml->soapBody->soapFault->faultstring[0]) {
                $result = array(
                    'codigoRetorno' => $resultado,
                    'mensajeRetorno' => mb_convert_encoding($xml->soapBody->soapFault->faultstring[0],'UTF-8','ISO-8859-1'),
                    'xml_retorno' => $xml,
                    'status_code' => $status_code
                );
            } elseif ($bultos == 1) {
                $resultado = $xml->soapenvBody->RespuestaPreregistroEnvio->Resultado;

                $motivo_error = '';
                
                if ($xml->soapenvBody->RespuestaPreregistroEnvio->BultoError->DescError) {
                    $motivo_error = $xml->soapenvBody->RespuestaPreregistroEnvio->BultoError->DescError;
                }
                
                $result = array(
                    'codigoRetorno' => $resultado,
                    'mensajeRetorno' => mb_convert_encoding($motivo_error,'UTF-8','ISO-8859-1'),
                    'xml_retorno' => $xml,
                    'status_code' => $status_code
                );
            } else {
                $resultado = $xml->soapenvBody->RespuestaPreregistroEnvioMultibulto->Resultado;
                $bultosError = get_object_vars($xml->soapenvBody->RespuestaPreregistroEnvioMultibulto->BultosError);
                $motivo_error = array();
                foreach ($bultosError as $bultoError) {
                    foreach ($bultoError as $bulto) {
                        array_push($motivo_error, $bulto->DescError);
                    }
                }
                $result = array(
                    'codigoRetorno' => $resultado,
                    'mensajeRetorno' => $motivo_error,
                    'xml_retorno' => $xml,
                    'status_code' => $status_code
                );
            }
        } else {
            $result = array('codigoRetorno' => '',
                'mensajeRetorno' => $this->getLastResponseFromHeaders($client),
                'codSolicitud' => '',
                'xml_retorno' => '',
                'status_code' => $status_code);
        }

        CorreosOficialUtils::varDump("RESPUESTA PREREGISTRO CORREOS", $result);

        return $result;
    }

    /* *********************************************************************************************************
     * CANCELAR PREREGISTRO PEDIDO
     ********************************************************************************************************* */
    public function cancelarPreRegistroEnvio($idioma, $codCertificado, $id_sender = false)
    {
        $resultado = '';
        $correos_url_preregistro = Config::getCorreosURL();
        $client = $this->soapClient($id_sender);
        $prepare = new CorreosOficialPrepareOrderRequests();
        $request = $prepare->prepareRequestCorreosCancelShipping($idioma, $codCertificado);
        $this->operation = 'AnularOp';

        try {
            $xml = $client->__doRequest($request, $correos_url_preregistro, $this->operation, 1, false);
        } catch (Exception $e) {
            $errores[] = array('id_order' => "N/A",
                'reference' => "N/A",
                'error' => "ERROR 14504: " . $e);
            die(json_encode($errores));
        }

        $response_from_server = $this->checkSoapConnection($client);
        $status_code = $this->getSoapStatusCodeFromHeaders($response_from_server->__getLastResponseHeaders());

        if (!$status_code) {
            return array('codigoRetorno' => '',
                'mensajeRetorno' => CO_TIMEOUT_MSG,
                'xml_retorno' => '',
                'status_code' => $status_code);
        }

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);

        if (CorreosOficialUtils::isValidXml($response)) {

            $xml = new SimpleXMLElement($response);

            $resultado = $xml->soapenvBody->RespuestaAnular->Resultado;

            if ($resultado[0] == 0) {
                $motivo_error = 'Su solicitud de envio ha sido cancelada (Correos)';
                $codigo_error = 0;
            } else {
                $motivo_error = $xml->soapenvBody->RespuestaAnular->ErroresValidacion->ErrorVal->DescError;
                $codigo_error = $xml->soapenvBody->RespuestaAnular->ErroresValidacion->ErrorVal->Error;
            }

            return array('codigoRetorno' => $resultado,
                'mensajeRetorno' => $motivo_error,
                'xml_retorno' => $xml,
                'status_code' => $status_code,
                'codigoError' => $codigo_error
            );
        } else {
            return array('codigoRetorno' => '',
            'mensajeRetorno' => $this->getLastResponseFromHeaders($client),
            'xml_retorno' => '',
            'status_code' => $status_code
             );
        }
    }

    /* *********************************************************************************************************
     * RECOGIDA PEDIDO
     ********************************************************************************************************* */
    public function registrarRecogida($pickup_data, $id_sender = false)
    {
        $resultado = '';
        $correos_url_recogidas = SERVICIO_RECOGIDAS_CORREOS;
        $prepare = new CorreosOficialPrepareOrderRequests();
        $request = $prepare->prepareRequestCorreosPickup($pickup_data);
        CorreosOficialUtils::varDump("LLAMADA A SERIVCIO RECOGIDA CORREOS", $request);

        $response = $this->doCurlSoapRequest($correos_url_recogidas, $request, true, false, $id_sender);
        CorreosOficialUtils::varDump("RESPUESTA RECOGIDA CORREOS", $response);

        $status_code = $response;

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

        if (CorreosOficialUtils::isValidXml($response)) {

            $xmlResponse = new SimpleXMLElement($response);

            $resultado = $xmlResponse->soapenvBody->dlwminSolicitudRegistroRecogidaResult->ns2RespuestaSolicitudRegistroRecogida->CodigoError;
            $motivo_error = $xmlResponse->soapenvBody->dlwminSolicitudRegistroRecogidaResult->ns2RespuestaSolicitudRegistroRecogida->DescripcionError;
            $cod_solicitud = $xmlResponse->soapenvBody->dlwminSolicitudRegistroRecogidaResult->ns2RespuestaSolicitudRegistroRecogida->CodSolicitud;

            if ($resultado == '') {
                $resultado = 0;
                $mensaje_retorno = 'Su solicitud de recogida ha sido grabada';
            } else {
                $mensaje_retorno = mb_convert_encoding($motivo_error,'UTF-8','ISO-8859-1');
                $msgErrorSizePickup = "No ha sido posible dar de alta la solicitud de recogida. El peso volumetrico estimado del lote de la recogida no esta informado.";
                if ($mensaje_retorno == $msgErrorSizePickup) {
                    $mensaje_retorno = mb_convert_encoding("Por favor, seleccione un tamaño de paquete antes de continuar.",'UTF-8','ISO-8859-1');
                }
            }

            $resultado = array('codigoRetorno' => $resultado,
                'mensajeRetorno' => $mensaje_retorno,
                'codSolicitud' => $cod_solicitud,
                'xml_retorno' => $xmlResponse,
                'status_code' => $status_code);
        } else {
            $resultado = array('codigoRetorno' => '',
                'mensajeRetorno' => $response,
                'codSolicitud' => '',
                'xml_retorno' => '',
                'status_code' => $status_code);
        }
        return $resultado;
    }

    /* *********************************************************************************************************
     * CANCELAR RECOGIDA PEDIDO
     ********************************************************************************************************* */
    public function cancelarRecogida($pickup_data, $id_sender = false)
    {

        $resultado = '';
        $correos_url_recogidas = SERVICIO_RECOGIDAS_CORREOS;

        $prepare = new CorreosOficialPrepareOrderRequests();
        $request = $prepare->prepareRequestCorreosCancelPickup($pickup_data);
        CorreosOficialUtils::varDump('LLAMADA A SERVICIO CANCELARRECOGIDA CORREOS', $request);

        $response = $this->doCurlSoapRequest($correos_url_recogidas, $request, true, false, $id_sender);

        $status_code = $response;
        CorreosOficialUtils::varDump('RESPUESTA A SERVICIO CANCELAR RECOGIDA CORREOS', $response);
        
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

        if (CorreosOficialUtils::isValidXml($response)) {

            $xmlResponse = new SimpleXMLElement($response);

            $resultado = $xmlResponse->soapenvBody->ns2AnulacionRecogidaPaPResponse->AnulacionRecogidaPaPResult->CodigoResultado;
            $motivo_error = $xmlResponse->soapenvBody->ns2AnulacionRecogidaPaPResponse->AnulacionRecogidaPaPResult->DetalleResultado;
            $cod_solicitud = $xmlResponse->soapenvBody->ns2AnulacionRecogidaPaPResponse->AnulacionRecogidaPaPResult->CodigoSRE;
            $resultado = array('codigoRetorno' => $resultado,
                'mensajeRetorno' => mb_convert_encoding($motivo_error,'UTF-8','ISO-8859-1'),
                'codSolicitud' => $cod_solicitud,
                'xml_retorno' => $xmlResponse,
                'status_code' => $status_code);
        } else {
            $resultado = array('codigoRetorno' => '',
                'mensajeRetorno' => $response,
                'codSolicitud' => '',
                'xml_retorno' => '',
                'status_code' => $status_code);
        }
        return ($resultado);
    }

    /* *********************************************************************************************************
     * ESTADO RECOGIDA PEDIDO
     ********************************************************************************************************* */
    public function ConsultaSRE($pickup_data, $id_sender = false)
    {

        $resultado = '';
        $correos_url_recogidas = SERVICIO_RECOGIDAS_CORREOS;

        $prepare = new CorreosOficialPrepareOrderRequests();
        $request = $prepare->prepareRequestCorreosConsultaSRE($pickup_data);

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO DE RECOGIDAS DE DEVOLUCIÓN CORREOS', $request);
        $response = $this->doCurlSoapRequest($correos_url_recogidas, $request, true, false, $id_sender);
        $status_code = $response;
    
       $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

        if (CorreosOficialUtils::isValidXml($response)) {

            $xmlResponse = new SimpleXMLElement($response);

            CorreosOficialUtils::varDump('RESPUESTA DE SERVICIO DE RECOGIDAS DE DEVOLUCIÓN CORREOS', $request);

            $resultado = $xmlResponse->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->CodigoResultado;
            $motivo_error = $xmlResponse->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->DetalleResultado;
            //$cod_solicitud = $xmlResponse->soapenvBody->ns2AnulacionRecogidaPaPResponse->AnulacionRecogidaPaPResult->CodigoSRE;

            if ($resultado[0] == '3') {
                $resultado = array('codigoRetorno' => $resultado,
                    'mensajeRetorno' => mb_convert_encoding($motivo_error,'UTF-8','ISO-8859-1'),
                    'xml_retorno' => null,
                    'status_code' => $status_code);
            } else {
                $resultado = array('codigoRetorno' => $resultado,
                    'mensajeRetorno' => mb_convert_encoding($motivo_error,'UTF-8','ISO-8859-1'),
                    'xml_retorno' => $xmlResponse,
                    'status_code' => $status_code);
            }
        } else {
            $resultado = array('codigoRetorno' => '',
                'mensajeRetorno' => $response,
                'xml_retorno' => '',
                'status_code' => $status_code);
        }
        return ($resultado);
    }

    /* *********************************************************************************************************
     * PREREGISTRO DEVOLUCIÓN
     ********************************************************************************************************* */
    public function registrarDevolucion($shipping_return_data, $id_sender = false)
    {
        $resultado = '';
        $correos_url_preregistro = Config::getCorreosURL();
        $client = $this->soapClient($id_sender);
        $prepare = new CorreosOficialPrepareOrderRequests();
        $request = $prepare->prepareRequestCorreosReturn($shipping_return_data);

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO DEVOLUCIÓN CORREOS', $request);

        $bultos = $shipping_return_data['order_form']['correos-num-parcels-return'];
        $this->operation = 'PreRegistro';

        try {
            $xml = $client->__doRequest($request, $correos_url_preregistro, $this->operation, 1, null);
        } catch (Exception $e) {
            $errores[] = array(
                'id_order' => "N/A",
                'reference' => "N/A",
                'error' => "ERROR 14504: " . $e
            );
            die(json_encode($errores));
        }

        $response_from_server = $this->checkSoapConnection($client);
        $status_code = $this->getSoapStatusCodeFromHeaders($response_from_server->__getLastResponseHeaders());
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);

        if (!$status_code) {
            return self::checkForTimeOutAndDie();
        }

        $xml = new SimpleXMLElement($response);

        CorreosOficialUtils::varDump('RESPUESTA DE SERVICIO DEVOLUCIÓN CORREOS', $xml);

        $resultado = $xml->soapenvBody->RespuestaPreregistroEnvio->Resultado;
        $motivo_error = $xml->soapenvBody->RespuestaPreregistroEnvio->BultoError->DescError;
        $result = array(
            'codigoRetorno' => $resultado,
            'mensajeRetorno' => mb_convert_encoding($motivo_error,'UTF-8','ISO-8859-1'),
            'xml_retorno' => $xml,
            'status_code' => $status_code
        );

        return $result;
    }

    /* **********************************************************************************************************
     *                                  DOCUMENTACION ADUANERA
     *
     ********************************************************************************************************* */
    public function documentacionAduaneraOp($option_button, $shipping_number, $costumer_country, $customer_name, $id_sender = false)
    {
        $correos_url_preregistro = Config::getCorreosURL();
        $status_code = '';

        $client = $this->soapClient($id_sender);

        $prepare = new CorreosOficialPrepareRequestsDocAduanera($option_button, $shipping_number, $costumer_country, $customer_name);
        $request = $prepare->prepareRequest();

        CorreosOficialUtils::varDump('LLAMADA A SERVICIO DOCUMENTACIÓN ADUANERA CORREOS', $request);

        switch ($option_button) {
            case 'ImprimirCN23Button':
                $operationAduanas = 'DocumentacionAduaneraCN23CP71Op';
                break;
            case 'ImprimirDUAButton':
            case 'ImprimirDDPButton':
                $operationAduanas = 'DocumentacionAduaneraOp';
                break;
        }

        $xml = $client->__doRequest($request, $correos_url_preregistro, $operationAduanas, 1, 0);
        $this->checkSoapConnection($client);
        $status_code = $this->getStatusFromLastResponseFromHeaders($client);

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);

        if (!$status_code) {
            return self::checkForTimeOutAndDie();
        }

        $xml = new SimpleXMLElement($response);

        switch ($option_button) {
            case 'ImprimirCN23Button':
                $resultado = $xml->soapenvBody->RespuestaSolicitudDocumentacionAduaneraCN23CP71->Resultado;
                $motivo_error = $xml->soapenvBody->RespuestaSolicitudDocumentacionAduaneraCN23CP71->MotivoError;
                break;
            case 'ImprimirDUAButton':
            case 'ImprimirDDPButton':
                $resultado = $xml->soapenvBody->RespuestaSolicitudDocumentacionAduanera->Resultado;
                $motivo_error = $xml->soapenvBody->RespuestaSolicitudDocumentacionAduanera->MotivoError;
                break;
        }

        return array('codigoRetorno' => mb_convert_encoding($resultado,'UTF-8','ISO-8859-1'),
            'mensajeRetorno' => mb_convert_encoding($motivo_error,'UTF-8','ISO-8859-1'),
            'xml_retorno' => $xml,
            'status_code' => $status_code);
    }

    /**
     * Consigue la etiqueta desde el webservice de PS2C
     * @param string $shipping_number (nº de envío de Correos de 23 dígitos)
     * @param string $id_sender (id del remitente para credenciales de Correos)
     * @param return $label Etiqueta decodificada como base64
     */
    public function SolicitudEtiquetaOp($expNumber, $id_sender = false) {

        $correos_url_preregistro = Config::getCorreosURL();
        $client = $this->soapClient($id_sender);

        $request =
            '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
            xmlns:prer="http://www.correos.es/iris6/services/preregistroetiquetas">
                <soapenv:Header/>
                <soapenv:Body>
                    <prer:SolicitudEtiqueta>
                    <prer:CodEnvio>' . $expNumber . '</prer:CodEnvio>
                    </prer:SolicitudEtiqueta>
                </soapenv:Body>
            </soapenv:Envelope>';

        CorreosOficialUtils::varDump("LLAMADA A SOLICITUD ETIQUETA PS2C: ", $request);
        $this->operation = 'SolicitudEtiquetaOp';

        try {
            $xml = $client->__doRequest($request, $correos_url_preregistro, $this->operation, 1, false);
        } catch (Exception $e) {
            $errores[] = array('id_order' => "N/A",
            'reference' => $expNumber,
            'error' => "ERROR 14505: " . $e);
            die(json_encode($errores));
        }

        $response_from_server = $this->checkSoapConnection($client);
        $status_code = $this->getSoapStatusCodeFromHeaders($response_from_server->__getLastResponseHeaders());
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml); 
        CorreosOficialUtils::varDump("RESPUESTA A SOLICITUD ETIQUETA PS2C: ", $response);

        if (!$status_code) {
            return self::checkForTimeOutAndDie();
        }

        $xml = new SimpleXMLElement($response);
        $label = ''; 

        if (isset($xml->soapenvBody->RespuestaSolicitudEtiqueta->Bulto->Etiqueta->Etiqueta_pdf->Fichero)) {
            $label = (string) $xml->soapenvBody->RespuestaSolicitudEtiqueta->Bulto->Etiqueta->Etiqueta_pdf->Fichero;
        } else {
            // Comprobar si existe un nodo Resultado en la respuesta
            if (isset($xml->soapenvBody->RespuestaSolicitudEtiqueta->Resultado)) {
                $resultado = (string) $xml->soapenvBody->RespuestaSolicitudEtiqueta->Resultado;

                if ($resultado == '1') {
                    throw new LogicException("Error 18006: Posibles causas:\r\nEl usuario actual de Correos no es el mismo que el que generó esta etiqueta.
                    O no se ha informado correctamente la llamada de SolicitudEtiquetaOp");
                } else {
                    // Otros códigos de resultado o mensajes de error
                }
            }
        }

        return $label;
    }

    public function homePaqConsultaCP1($postcode, $return = false)
    {

        $resultado = '';

        $correos_url_citypaq = LOCALIZADOR_OFICINAS;

        $request =
            '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:ejb="http://ejb.mauo.correos.es">
            <soapenv:Header/>
              <soapenv:Body>
               <ejb:homePaqConsultaCP1>
                  <ejb:codigoPostal>' . $postcode . '</ejb:codigoPostal>
               </ejb:homePaqConsultaCP1>
               </soapenv:Body>
           </soapenv:Envelope>';

        CorreosOficialUtils::varDump("LLAMADA A SERVICIO SERVICIO DE CITYPAQ", $request);
        $response = $this->doCurlSoapRequest($correos_url_citypaq, $request, true);
        CorreosOficialUtils::varDump("RESPUESTA SERVICIO DE CITYPAQ", $response);

        $isValidXML = CorreosOficialUtils::isValidXml($response);
        $status_code = $response;

        if ($isValidXML) {
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

            $xmlResponse = new SimpleXMLElement($response);

            CorreosOficialUtils::varDump("RESPUESTA OBJETO SERVICIO DE CITYPAQ ", $xmlResponse);
            //die(json_encode($xmlResponse->soapenvBody->homePaqRespuesta1->listaHomePaq));
            $resultado = $xmlResponse->soapenvBody->homePaqRespuesta1->listaHomePaq->error;

            $retorno = $xmlResponse->soapenvBody->homePaqRespuesta1->listaHomePaq->homePaq;
            $resultado = array('codigoRetorno' => $resultado,
                'mensajeRetorno' => ($retorno),
                'codSolicitud' => "N/A",
                'json_retorno' => $xmlResponse,
                'status_code' => $status_code
            );
        } else {
            $resultado = array('codigoRetorno' => '',
                'mensajeRetorno' => $response,
                'codSolicitud' => '',
                'json_retorno' => '',
                'status_code' => $status_code
            );
        }
        if ($return) {
            return json_encode($resultado);
        }
        die(json_encode($resultado));
    }

    public function localizadorConsulta($postcode, $return = false)
    {
        $resultado = '';

        $correos_url_oficina = LOCALIZADOR_OFICINAS;

        $request =
            '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
             xmlns:ejb="http://ejb.mauo.correos.es">
            <soapenv:Header/>
            <soapenv:Body>
                <ejb:localizadorConsulta>
                    <ejb:codigoPostal>' . $postcode . '</ejb:codigoPostal>
                </ejb:localizadorConsulta>
            </soapenv:Body>
        </soapenv:Envelope>';

        CorreosOficialUtils::varDump("LLAMADA A SERVICIO SERVICIO DE OFICINA", $request);
        $response = $this->doCurlSoapRequest($correos_url_oficina, $request, true);
        CorreosOficialUtils::varDump("RESPUESTA SERVICIO DE OFICINA", $response);

        $isValidXML = CorreosOficialUtils::isValidXml($response);
        $status_code = $response;

        if ($isValidXML) {
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

            $response = preg_replace('/ns-980841924:/', '', $response);

            $xmlResponse = new SimpleXMLElement($response);
            CorreosOficialUtils::varDump("RESPUESTA OBJETO SERVICIO DE OFICINA", $xmlResponse);
            $resultado = $xmlResponse->soapenvBody->localizadorRespuesta->error;

            $retorno = $xmlResponse->soapenvBody->localizadorRespuesta->arrayOficina;
            $resultado = array('codigoRetorno' => $resultado,
                'mensajeRetorno' => ($retorno),
                'codSolicitud' => "N/A",
                'json_retorno' => $xmlResponse,
                'status_code' => $status_code
            );
        } else {
            $resultado = array('codigoRetorno' => '',
                'mensajeRetorno' => $response,
                'codSolicitud' => '',
                'json_retorno' => '',
                'status_code' => $status_code
            );
        }
        if ($return) {
            return json_encode($resultado);
        }
        die(json_encode($resultado));
    }
    
    /**
     * Establece la conexión con un recurso SOAP.
     * @return objetc Devuelve un recurso objeto cliente soap
     */
    public function soapClient($id_sender = false, $id_code = false)
    {
        $customer = new CorreosOficialCustomerDataDao();

        if($id_code){
            $id = $id_code;
        } else {
            if (!$id_sender) {
                $id = $customer->getIdByCompany('Correos');
            } else {
                $id = $customer->getIdCodeFromSenderByDao($id_sender);
            }
        }

        $correos_user_password = $customer->getUserPassword($id);
        $correos_user_password['password'] = stripslashes(CorreosOficialCrypto::decrypt($correos_user_password['password']));

        try {
            return new SoapClient(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/vendor/ecommerce_common_lib/services/preregistro/preregistro.wsdl',
                array('login' => $correos_user_password['login'],
                    'password' => $correos_user_password['password'], 'trace' => 1, 'connection_timeout' => 10));
        } catch (Exception $e) {
            $error = new CorreosOficialErrorManager();
            if (strstr($e->getMessage(), "login")) {
                $extra_error_msg = $error->UserErrorLoginError;
            } else {
                $extra_error_msg = $error->userErrorAnErrorHasOCurred;
            }

            $errores[] = array('id_order' => "N/A",
                'reference' => "N/A",
                'error' => "ERROR 18001 CORREOS: " . $extra_error_msg,
                'technical_error' => $e->getMessage());
            die(json_encode($errores));
        }
    }


    public function getStatusFromLastResponseFromHeaders($client)
    {
        $response_from_server = $this->checkSoapConnection($client);

        if (version_compare(phpversion(), '8', '>=')) {
            $status_code = $this->getSoapStatusCodeFromHeaders($response_from_server->__getLastResponseHeaders());
        } else {
            $status_code = $this->getSoapStatusCodeFromHeaders($response_from_server->__last_response_headers);
        }

        return $status_code;
    }

    public function getLastResponseFromHeaders($client)
    {
        $response_from_server = $this->checkSoapConnection($client);

        return strstr($response_from_server->__getLastResponseHeaders(), "Date: ", true);
    }

    /**
     * Devuelve un recurso SOAP con la conexión establecida si no ha habido error.
     * Si ha habido error se informa debidamente.
     * @return objetc Devuelve un recurso objeto cliente soap
     */
    public function checkSoapConnection($client)
    {
        if (isset($client->__soap_fault->faultstring) && !empty($client->__soap_fault->faultstring)) {
            CorreosOficialErrorManager::checkStateConnection($client->__soap_fault->faultstring);
            return false;
        } else {
            return $client;
        }
    }

    /**
     * HTTP/1.1 200 OK Date: Wed,...
     */
    public function getSoapStatusCodeFromHeaders($headers)
    {
        if (empty($headers)) {
            return false;
        }
        preg_match("/HTTP\/\d\.\d\s*\K[\d]+/", $headers, $matches);
        return $matches[0];
    }

    /**
     * Hace una llamada SOAP con Curl.
     * @param string $url: a donde hacemos la llamada
     * @param string $soapXML : petición de tipo soap
     * @param string $userpwd Si viene informado informamos el usuario/contraseña en la llamada
     */
    public function doCurlSoapRequest($url, $soapXML, $userpwd = false, $certificate = true, $id_sender = false)
    {
        $customer = new CorreosOficialCustomerDataDao();

        if (!$id_sender) {
            $id = $customer->getIdByCompany('Correos');
        } else {
            $id = $customer->getIdCodeFromSenderByDao($id_sender);
        }

        $correos_user_password = $customer->getUserPassword($id);

        $correos_user_password['password'] = stripslashes(CorreosOficialCrypto::decrypt($correos_user_password['password']));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $soapXML);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8'));
        if ($userpwd) {
            curl_setopt($ch, CURLOPT_USERPWD, $correos_user_password['login'] . ':' . $correos_user_password['password']);
        }

        if ($certificate) {
            $environment = Config::getEnvironment();
            $certificate_path = ($environment == 'PRO' ?
                'correos_y_telegrafos.cer' : 'correos_y_telegrafos_pre.cer');

            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/' . $certificate_path);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $dao = new CorreosOficialDao();

        $ssl_alternative = $dao->readSettings('SSLAlternative');

        if ($ssl_alternative->value == 'on') {
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'AES256-SHA');
        }

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($info['http_code'] == 0) {
            return $error;
        } elseif ($info['http_code'] != 200) {
            return " Status Code: " . $info['http_code'] . "<br>" . strip_tags($result);
        } else {
            return $result;
        }
    }

    public static function checkForTimeOutAndDie()
    {
        $result =  array(
            'status_code' => 404,
            'mensajeRetorno' => CO_TIMEOUT_MSG);
        die(json_encode($result));
    }

}
