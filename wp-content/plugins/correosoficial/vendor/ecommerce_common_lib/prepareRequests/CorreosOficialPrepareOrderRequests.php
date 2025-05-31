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
 * Clase de uso general
 * Prepara los datos de order para mandar al ws
 */

require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/CorreosOficialSmarty.php';

require_once dirname(__FILE__) . '/../../../classes/CorreosOficialOrders.php';
require_once dirname(__FILE__) . '/../../../classes/CorreosOficialOrder.php';
use CorreosOficialCommonLib\Commons\CorreosOficialCrypto;

class CorreosOficialPrepareOrderRequests
{

    public static function prepareRequestCorreosShippingUtilities($shipping_data)
    {

        $smarty = CorreosOficialSmarty::loadSmartyInstance();
        $shipping_data_request = array();

        $bultos = $shipping_data['bultos'];

        // Datos del pedido de prestashop
        $order = new CorreosOficialOrder((int) $shipping_data['order']['id_order']);
        // Observaciones del customer del checkout
        $customer_message = substr($order->getFirstMessage(), 0, 80);

        // Configuración de usuario -> Observaciones del pedido a la etiqueta (máx. 80 caracteres)
        $label_observations = CorreosOficialConfigDao::getConfigValue('LabelObservations');
        // Configuración de usuario -> Peso por defecto
        $default_weight = CorreosOficialConfigDao::getConfigValue('WeightByDefault');

        // Peso del carrito
        $total_weight = $order->getTotalWeight();

        // Calculamos peso para único bulto / Para multibulto -> $default_weight
        if ($total_weight == 0) {
            $order_weight = $default_weight;
        } else {
            $order_weight = $total_weight;
        }

        // Canal de Origen Bulto/Multibulto
        $shipping_data_request['source_channel'] = $shipping_data['source_channel'];

        // Sender
        $label_alternative_text = CorreosOficialConfigDao::getLabelAlternartiveText();
        if ($label_alternative_text != false) {
            $shipping_data_request['sender_name'] = $label_alternative_text;
        } else {
            $shipping_data_request['sender_name'] = $shipping_data['default_sender']['sender_name'];
        }
        $shipping_data_request['sender_address'] = $shipping_data['default_sender']['sender_address'];
        $shipping_data_request['sender_cp'] = CorreosOficialUtils::getSenderPostalCode($shipping_data['default_sender']['sender_iso_code_pais'], $shipping_data['default_sender']['sender_cp']);
        $shipping_data_request['sender_nif_cif'] = $shipping_data['default_sender']['sender_nif_cif'];
        $shipping_data_request['sender_city'] = $shipping_data['default_sender']['sender_city'];
        $shipping_data_request['sender_contact'] = $shipping_data['default_sender']['sender_contact'];
        $shipping_data_request['sender_phone'] = $shipping_data['default_sender']['sender_phone'];
        $shipping_data_request['sender_email'] = $shipping_data['default_sender']['sender_email'];
        $shipping_data_request['sender_iso_code_pais'] = $shipping_data['default_sender']['sender_iso_code_pais'];

        // Customer
        if ($shipping_data['customer']['delivery_country_iso'] != 'ES' && $shipping_data['customer']['delivery_country_iso'] != 'AD') {
            $shipping_data_request['delivery_postcode'] = "";
            $shipping_data_request['delivery_zip'] = $shipping_data['customer']['delivery_postcode'];
        } else {
            $shipping_data_request['delivery_postcode'] = $shipping_data['customer']['delivery_postcode'];
            $shipping_data_request['delivery_zip'] = "";
        }
        $shipping_data_request['customer_company'] = "";
        $shipping_data_request['customer_firstname'] = $shipping_data['customer']['customer_firstname'];
        $shipping_data_request['customer_lastname1'] = $shipping_data['customer']['customer_lastname'];
        $shipping_data_request['customer_lastname2'] = "";
        $shipping_data_request['delivery_address'] = $shipping_data['customer']['delivery_address'] . " " . $shipping_data['customer']['delivery_address2'];
        $shipping_data_request['delivery_address2'] = $shipping_data['customer']['delivery_address2'];
        $shipping_data_request['delivery_city'] = $shipping_data['customer']['delivery_city'];
        $shipping_data_request['delivery_state'] = "";
        $shipping_data_request['delivery_country_iso'] = $shipping_data['customer']['delivery_country_iso'];
        $shipping_data_request['phone'] = $shipping_data['customer']['phone'];
        $shipping_data_request['customer_email'] = $shipping_data['customer']['customer_email'];
        $shipping_data_request['customer_dni'] = CorreosOficialUtils::nifIsAnString($shipping_data['customer']['customer_dni']);

        $shipping_data_request['phone_mobile'] = CorreosOficialUtils::getMobilePhone($shipping_data['customer']['phone'], $shipping_data['customer']['delivery_country_iso']);
        $shipping_data_request['phone_mobile_sms'] = CorreosOficialUtils::getMobilePhone($shipping_data['customer']['phone'], $shipping_data['customer']['delivery_country_iso'], $shipping_data['product']['codigoProducto']);
        $shipping_data_request['mobile_lang'] = "";
        $shipping_data_request['partial_delivery'] = 'N';

        // Datos pedido
        $shipping_data_request['CorreosKey'] = $shipping_data['client'][0]['CorreosKey'];
        $shipping_data_request['carrier_code'] = $shipping_data['product']['codigoProducto'];

        switch ($shipping_data['product']['product_type']) {
            case 'homedelivery':
            case 'international':
                $shipping_data_request['delivery_mode'] = 'ST';
                break;
            case 'office':
                $shipping_data_request['delivery_mode'] = 'LS';
                break;
            case 'citypaq':
                $shipping_data_request['delivery_mode'] = 'CP';
                break;
            default:
                $shipping_data_request['delivery_mode'] = 'ST';
                break;
        }

        // Contrareembolso
        $shipping_data_request['contra_reembolso'] = 0;

        // Contrareembolso
        if ($order->isCashOnDeliveryMethodType()) {
            $cash_on_delivery = 1;
        } else {
            $cash_on_delivery = 0;
        }
        $cash_on_delivery_value = number_format($order->getTotalPaid(), 2);

        $bank_acc_number = CorreosOficialConfigDao::getConfigValue('BankAccNumberAndIBAN');
        $bank_acc_number = CorreosOficialCrypto::decrypt($bank_acc_number);

        $shipping_data_request['contra_reembolso'] = $cash_on_delivery;
        if ($shipping_data_request['contra_reembolso'] == 1) {
            $shipping_data_request['cashondelivery_type'] = 'RC';
            $shipping_data_request['cashondelivery_value'] = self::getFloatValue($cash_on_delivery_value) * 100;
            $shipping_data_request['cashondelivery_bankac'] = $bank_acc_number;
        }

        // Seguro
        $shipping_data_request['seguro'] = 0;

        // Office/Citypaq
        if ($shipping_data['product']['product_type'] == "office") {
            $shipping_data_request['id_office'] = $shipping_data['order']['office'];
            $shipping_data_request['id_citypaq'] = "";
        } elseif ($shipping_data['product']['product_type'] == "citypaq") {
            $shipping_data_request['id_office'] = "";
            $shipping_data_request['id_citypaq'] = $shipping_data['order']['office'];
        } else {
            $shipping_data_request['id_office'] = "";
            $shipping_data_request['id_citypaq'] = "";
        }

        // Descripción aduanera por defecto
        $customs_desc_selected = CorreosOficialConfigDao::getConfigValue('DefaultCustomsDescription');
        // Número tarifario por defecto
        $customs_tariff_selected = CorreosOficialConfigDao::getConfigValue('Tariff');
        // Descripción del número tarifario
        $customs_tariff_description = CorreosOficialConfigDao::getConfigValue('TariffDescription');
        // Referencia aduanera del expedidor
        $customs_reference = CorreosOficialConfigDao::getConfigValue('ShippCustomsReference');
        $DescriptionRadio = CorreosOficialConfigDao::getConfigValue('DescriptionRadio');

        if ($DescriptionRadio == 'on') {
            $default_customs_descriptions = 1;
        } else {
            $default_customs_descriptions = 0;
        }

        // Requerimiento de doc aduanera
        $shipping_data_request['require_customs_doc'] = $shipping_data['require_customs_doc'];

        // BULTO ÚNICO
        if ($bultos == 1) {
            //$shipping_data_request['total_bultos'] = 1; // No se usa ya que en la plantilla está forzado a 1
            $shipping_data_request['weight'] = self::getFloatValue($order_weight) * 1000;
            $shipping_data_request['long'] = $shipping_data['large'];
            $shipping_data_request['height'] = $shipping_data['height'];
            $shipping_data_request['width'] = $shipping_data['width'];

            //$shipping_data_request['has_size'] = false;
            $shipping_data_request['has_size'] = self::hasSize($shipping_data_request['long'], $shipping_data_request['width'], $shipping_data_request['height']);
            if ($shipping_data_request['has_size']) {
                $shipping_data_request['v_weight'] = static::calculateVWeight($shipping_data_request['long'], $shipping_data_request['width'], $shipping_data_request['height']);
            } else {
                $shipping_data_request['v_weight'] = 0;
            }

            // Descripción aduanera y número tarifario
            if ($shipping_data_request['require_customs_doc'] == 1) {
                $shipping_data_request['customs_consignor_reference'] = $customs_reference;
                $shipping_data_request['valor_neto'] = self::getFloatValue($order->getSubTotal()) * 100;
                if ($default_customs_descriptions == 1) {
                    $shipping_data_request['descripcion_aduanera'] = $customs_desc_selected;
                    $shipping_data_request['numero_tarifario'] = "";
                } else {
                    $shipping_data_request['descripcion_aduanera'] = $customs_tariff_description;
                    $shipping_data_request['numero_tarifario'] = $customs_tariff_selected;
                }

                $customs_desc_arrray[1][0]['descripcion_aduanera'] = $shipping_data_request['descripcion_aduanera'];
                $customs_desc_arrray[1][0]['numero_tarifario'] = $shipping_data_request['numero_tarifario'];
                $customs_desc_arrray[1][0]['valor_neto'] = $shipping_data_request['valor_neto'];
            }

            $customs_desc_arrray[1][0]['unidades'] = '1';
            $customs_desc_arrray[1][0]['weight'] = $shipping_data_request['weight'];

            $shipping_data_request['customs_descs'] = $customs_desc_arrray;

            // Observaciones
            if ($label_observations == 'on') {
                $shipping_data_request['observaciones1'] = substr($customer_message, 0, 40);
                $shipping_data_request['observaciones2'] = substr($customer_message, 40, 80);
            } else {
                $shipping_data_request['observaciones1'] = "";
                $shipping_data_request['observaciones2'] = "";
            }

        } else { // MULTIBULTO
            $shipping_data_request['total_bultos'] = $bultos;
            $parcel_info = array();

            // Observaciones
            if ($label_observations == 'on') {
                $observaciones = substr($customer_message, 0, 40);
                $observaciones2 = substr($customer_message, 40, 80);
            } else {
                $observaciones = "";
                $observaciones2 = "";
            }

            for ($i = 1; $i <= $bultos; $i++) {
                $has_size = self::hasSize($shipping_data['large'], $shipping_data['width'], $shipping_data['height']);
                if ($has_size) {
                    $v_weight = static::calculateVWeight($shipping_data['large'], $shipping_data['width'], $shipping_data['height']);
                } else {
                    $v_weight = 0;
                }

                // Se añade el $shipping_data['width']... para todos los bultos igual al ser las dimensiones por defecto
                $parcel_info[$i] = array(
                    'reference' => "",
                    'order_number' => "",
                    'weight' => self::getFloatValue($default_weight) * 1000,
                    'height' =>  $shipping_data['height'],
                    'width' =>  $shipping_data['width'],
                    'long' => $shipping_data['large'],
                    'v_weight' => $v_weight,
                    'observations' => $observaciones,
                    'observations2' => $observaciones2,
                    'has_size' => $has_size
                );

                if ($shipping_data_request['require_customs_doc'] == 1) {
                    $shipping_data_request['customs_consignor_reference'] = $customs_reference;
                    $parcel_info[$i] = $parcel_info[$i] + array('valor_neto' => self::getFloatValue($order->getSubTotal()) * 100);
                }

                if ($default_customs_descriptions == 1) {
                    $parcel_info[$i] = $parcel_info[$i] + array(
                        'descripcion_aduanera' => $customs_desc_selected[0],
                        'numero_tarifario' => ""
                    );
                } else {
                    $parcel_info[$i] = $parcel_info[$i] + array(
                        'descripcion_aduanera' => $customs_tariff_description,
                        'numero_tarifario' => $customs_tariff_selected
                    );
                }

            }

            $shipping_data_request['parcel_info'] = $parcel_info;
        }

        $shipping_data_request['order_number'] = $shipping_data['order']['id_order'];
        $shipping_data_request['order_reference'] = $shipping_data['order']['id_order']." ".$shipping_data['order']['reference'];
        $shipping_data_request['texto_adicional'] = "";

        $smarty->assign("shipping_data", $shipping_data_request);
        if ($bultos == 1) {
            $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/preregistro/preregistro-envio.tpl');
        } else {
            $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/preregistro/preregistro-envio_multibulto.tpl');
        }

        return $request;
    }

    public static function prepareRequestCorreosShipping($shipping_data)
    {

        $smarty = CorreosOficialSmarty::loadSmartyInstance();

        $shipping_data_request = array();
        $bultos = $shipping_data['bultos'];

        // Canal de Origen Bulto/Multibulto
        $shipping_data_request['source_channel'] = $shipping_data['source_channel'];

        // Sender
        $label_alternative_text = CorreosOficialConfigDao::getLabelAlternartiveText();
        if ($label_alternative_text != false) {
            $shipping_data_request['sender_name'] = $label_alternative_text;
        } else {
            $shipping_data_request['sender_name'] = $shipping_data['order_form']['sender_name'];
        }
        $shipping_data_request['sender_address'] = $shipping_data['order_form']['sender_address'];
        //$shipping_data_request['sender_cp'] = $shipping_data['order_form']['sender_cp'];
        $shipping_data_request['sender_cp'] = CorreosOficialUtils::getSenderPostalCode($shipping_data['order_form']['sender_country'], $shipping_data['order_form']['sender_cp']);
        $shipping_data_request['sender_nif_cif'] = $shipping_data['order_form']['sender_nif_cif'];
        $shipping_data_request['sender_city'] = $shipping_data['order_form']['sender_city'];
        $shipping_data_request['sender_contact'] = $shipping_data['order_form']['sender_contact'];
        $shipping_data_request['sender_phone'] = $shipping_data['order_form']['sender_phone'];
        $shipping_data_request['sender_email'] = $shipping_data['order_form']['sender_email'];
        $shipping_data_request['sender_iso_code_pais'] = $shipping_data['order_form']['sender_country'];

        // Customer
        $shipping_data_request['customer_company'] = $shipping_data['order_form']['customer_company'];
        $shipping_data_request['customer_firstname'] = $shipping_data['order_form']['customer_firstname'];
        $shipping_data_request['customer_lastname1'] = $shipping_data['order_form']['customer_lastname'];
        $shipping_data_request['customer_lastname2'] = "";
        $shipping_data_request['delivery_address'] = $shipping_data['order_form']['customer_address'];
        $shipping_data_request['delivery_city'] = $shipping_data['order_form']['customer_city'];
        $shipping_data_request['delivery_state'] = "";
        if ($shipping_data['order_form']['customer_country'] != 'ES' && $shipping_data['order_form']['customer_country'] != 'AD') {
            $shipping_data_request['delivery_postcode'] = "";
            $shipping_data_request['delivery_zip'] = $shipping_data['order_form']['customer_cp'];
        } else {
            $shipping_data_request['delivery_postcode'] = $shipping_data['order_form']['customer_cp'];
            $shipping_data_request['delivery_zip'] = "";
        }
        $shipping_data_request['delivery_country_iso'] = $shipping_data['order_form']['customer_country'];
        //$shipping_data_request['phone'] = str_replace(['0034', '0034 ', '+34', '+34 '], '', $shipping_data['order_form']['customer_phone']);
        $shipping_data_request['phone'] = CorreosOficialUtils::cleanTelephoneNumber($shipping_data['order_form']['customer_phone']);
        $shipping_data_request['customer_email'] = $shipping_data['order_form']['customer_email'];
        $shipping_data_request['customer_dni'] = CorreosOficialUtils::nifIsAnString($shipping_data['order_form']['customer_dni']);

        $shipping_data_request['phone_mobile'] = CorreosOficialUtils::getMobilePhone($shipping_data['order_form']['customer_phone'], $shipping_data_request['delivery_country_iso']);
        $shipping_data_request['phone_mobile_sms'] = CorreosOficialUtils::getMobilePhone($shipping_data['order_form']['customer_phone'], $shipping_data_request['delivery_country_iso'], $shipping_data['order_form']['input_select_carrier']);
        
        $shipping_data_request['mobile_lang'] = "";

        // Datos pedido
        $shipping_data_request['CorreosKey'] = $shipping_data['client'][0]['CorreosKey'];
        $shipping_data_request['carrier_code'] = $shipping_data['order_form']['input_select_carrier'];

        $shipping_data_request['require_customs_doc'] = $shipping_data['order_form']['require_customs_doc'];

        switch ($shipping_data['delivery_mode']) {
            case 'homedelivery':
            case 'international':
                $shipping_data_request['delivery_mode'] = 'ST';
                break;
            case 'office':
                $shipping_data_request['delivery_mode'] = 'LS';
                break;
            case 'citypaq':
                $shipping_data_request['delivery_mode'] = 'CP';
                break;
            default:
                $shipping_data_request['delivery_mode'] = 'ST';
                break;
        }

        // Contrareembolso
        $shipping_data_request['contra_reembolso'] = $shipping_data['order_form']['contrareembolsoCheckbox'];
        if ($shipping_data_request['contra_reembolso'] == 1) {
            $shipping_data_request['cashondelivery_type'] = 'RC';
            $shipping_data_request['cashondelivery_value'] = self::getFloatValue($shipping_data['order_form']['cash_on_delivery_value']) * 100;

            if (substr($shipping_data['order_form']['bank_acc_number'], 0, 4) == '****') {
                $bank_acc_number = CorreosOficialConfigDao::getConfigValue('BankAccNumberAndIBAN');
                $bank_acc_number = CorreosOficialCrypto::decrypt($bank_acc_number);
                $shipping_data_request['cashondelivery_bankac'] = $bank_acc_number;
            } else {
                $shipping_data_request['cashondelivery_bankac'] = $shipping_data['order_form']['bank_acc_number'];
            }
        }

        // Seguro
        $shipping_data_request['seguro'] = $shipping_data['order_form']['seguroCheckbox'];
        if ($shipping_data_request['seguro'] == 1) {
            $shipping_data_request['insurance_value'] = self::getFloatValue($shipping_data['order_form']['insurance_value']) *
                100;
        }

        // Office/Citypaq
        if ($shipping_data['delivery_mode'] == 'office') {
            $shipping_data_request['id_office'] = $shipping_data['order_form']['cod_office'];
        }
        elseif ($shipping_data['delivery_mode'] == 'citypaq') {
            $shipping_data_request['id_citypaq'] = $shipping_data['order_form']['cod_homepaq'];
        }

        // Todos los paquetes iguales
        $all_packages_equal = $shipping_data['order_form']['all_packages_equal'];

        $shipping_data_request['customs_descs'] = $shipping_data['customs_desc_array'];

        // BULTO ÚNICO
        if ($bultos == 1) {
            //$shipping_data_request['total_bultos'] = 1; // No se usa ya que en la plantilla está forzado a 1
            $shipping_data_request['weight'] = self::getFloatValue($shipping_data['order_form']['packageWeight_1']) * 1000;
            $shipping_data_request['long'] = $shipping_data['order_form']['packageLarge_1'];
            $shipping_data_request['height'] = $shipping_data['order_form']['packageHeight_1'];
            $shipping_data_request['width'] = $shipping_data['order_form']['packageWidth_1'];

            $shipping_data_request['has_size'] = self::hasSize($shipping_data_request['long'], $shipping_data_request['width'], $shipping_data_request['height']);
            if ($shipping_data_request['has_size']) {
                $shipping_data_request['v_weight'] = static::calculateVWeight($shipping_data_request['long'], $shipping_data_request['width'], $shipping_data_request['height']);
            } else {
                $shipping_data_request['v_weight'] = 0;
            }

            // Descripción aduanera y número tarifario
            if ($shipping_data_request['require_customs_doc'] == 1) {
                $shipping_data_request['customs_consignor_reference'] = $shipping_data['order_form']['custom_ref_exp'];
                $default_customs_descriptions = $shipping_data['order_form']['DescriptionRadio_1'];
                if ($default_customs_descriptions == 0) {
                    $shipping_data_request['descripcion_aduanera'] = $shipping_data['order_form']['packageCustomDesc_1'];
                    $shipping_data_request['numero_tarifario'] = "";
                } else {
                    $shipping_data_request['descripcion_aduanera'] = $shipping_data['order_form']['packageTariffDesc_1'];
                    $shipping_data_request['numero_tarifario'] = $shipping_data['order_form']['packageTariffCode_1'];
                }
            }

            // Observaciones
            $shipping_data_request['observaciones1'] = substr($shipping_data['order_form']['deliveryRemarks_1'], 0, 40);
            $shipping_data_request['observaciones2'] = substr($shipping_data['order_form']['deliveryRemarks_1'], 40, 80);

        } else { // MULTIBULTO
            $shipping_data_request['total_bultos'] = $bultos;
            $shipping_data_request['partial_delivery'] = $shipping_data['order_form']['partial_delivery'] == 0 ? 'N' : 'S';
            $parcel_info = array();
            if ($all_packages_equal == 1) {
                for ($i = 1; $i <= $bultos; $i++) {
                    $has_size = self::hasSize($shipping_data['order_form']['packageLarge_1'], $shipping_data['order_form']['packageWidth_1'], $shipping_data['order_form']['packageHeight_1']);
                    if ($has_size) {
                        $v_weight = static::calculateVWeight($shipping_data['order_form']['packageLarge_1'], $shipping_data['order_form']['packageWidth_1'], $shipping_data['order_form']['packageHeight_1']);
                    } else {
                        $v_weight = 0;
                    }

                    $parcel_info[$i] = array(
                        'reference' => $shipping_data['order_form']['packageRef_1'],
                        'order_number' => $shipping_data['order_form']['order_number'],
                        'weight' => self::getFloatValue($shipping_data['order_form']['packageWeight_1']) * 1000,
                        'height' => $shipping_data['order_form']['packageHeight_1'],
                        'width' => $shipping_data['order_form']['packageWidth_1'],
                        'long' => $shipping_data['order_form']['packageLarge_1'],
                        'v_weight' => $v_weight,
                        'observations' => substr($shipping_data['order_form']['deliveryRemarks_1'], 0, 40),
                        'observations2' => substr($shipping_data['order_form']['deliveryRemarks_1'], 40, 80),
                        'has_size' => $has_size
                    );

                    if ($shipping_data_request['require_customs_doc'] == 1) {
                        $shipping_data_request['customs_consignor_reference'] = $shipping_data['order_form']['custom_ref_exp'];
                        $parcel_info[$i] = $parcel_info[$i] + array('valor_neto' => self::getFloatValue($shipping_data['order_form']['packageAmount_1']) * 100);
                        $default_customs_descriptions = $shipping_data['order_form']['DescriptionRadio_1'];
                        if ($default_customs_descriptions == 0) {
                            $parcel_info[$i] = $parcel_info[$i] + array('descripcion_aduanera' => $shipping_data['order_form']['packageCustomDesc_1'], 'numero_tarifario' => "");
                        } else {
                            $parcel_info[$i] = $parcel_info[$i] + array('descripcion_aduanera' => $shipping_data['order_form']['packageTariffDesc_1'], 'numero_tarifario' => $shipping_data['order_form']['packageTariffCode_1']);
                        }
                    }

                }
            } else {

                for ($i = 1; $i <= $bultos; $i++) {
                    $has_size = self::hasSize($shipping_data['order_form']['packageLarge_' . $i], $shipping_data['order_form']['packageWidth_' . $i], $shipping_data['order_form']['packageHeight_' . $i]);
                    if ($has_size) {
                        $v_weight = static::calculateVWeight($shipping_data['order_form']['packageLarge_' . $i], $shipping_data['order_form']['packageWidth_' . $i], $shipping_data['order_form']['packageHeight_' . $i]);
                    } else {
                        $v_weight = 0;
                    }

                    $parcel_info[$i] = array(
                        'reference' => $shipping_data['order_form']['packageRef_' . $i],
                        'order_number' => $shipping_data['order_form']['order_number'],
                        'weight' => self::getFloatValue($shipping_data['order_form']['packageWeight_' . $i]) * 1000,
                        'height' => $shipping_data['order_form']['packageHeight_' . $i],
                        'width' => $shipping_data['order_form']['packageWidth_' . $i],
                        'long' => $shipping_data['order_form']['packageLarge_' . $i],
                        'v_weight' => $v_weight,
                        'observations' => substr($shipping_data['order_form']['deliveryRemarks_' . $i], 0, 40),
                        'observations2' => substr($shipping_data['order_form']['deliveryRemarks_' . $i], 40, 80),
                        'has_size' => $has_size
                    );

                    if ($shipping_data_request['require_customs_doc'] == 1) {
                        $shipping_data_request['customs_consignor_reference'] = $shipping_data['order_form']['custom_ref_exp'];
                        $parcel_info[$i] = $parcel_info[$i] + array('valor_neto' => self::getFloatValue($shipping_data['order_form']['packageAmount_' . $i]) * 100);
                        $default_customs_descriptions = $shipping_data['order_form']['DescriptionRadio_' . $i];
                        if ($default_customs_descriptions == 0) {
                            $parcel_info[$i] = $parcel_info[$i] + array('descripcion_aduanera' => $shipping_data['order_form']['packageCustomDesc_' . $i], 'numero_tarifario' => "");
                        } else {
                            $parcel_info[$i] = $parcel_info[$i] + array('descripcion_aduanera' => $shipping_data['order_form']['packageTariffDesc_' . $i], 'numero_tarifario' => $shipping_data['order_form']['packageTariffCode_' . $i]);
                        }
                    }

                }
            }
            $shipping_data_request['parcel_info'] = $parcel_info;
        }

        $shipping_data_request['order_reference'] = $shipping_data['order_form']['packageRef_1'];
        $shipping_data_request['order_number'] = $shipping_data['order_form']['order_number'];

        $shipping_data_request['texto_adicional'] = $shipping_data['order_form']['deliveryRemarks_1'];
        $shipping_data_request['company'] = $shipping_data['company'];
        $shipping_data_request['customer_contact'] = $shipping_data['order_form']['customer_contact'];

        $smarty->assign("shipping_data", $shipping_data_request);

        if ($bultos == 1) {
            $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/preregistro/preregistro-envio.tpl');
        } else {
            $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/preregistro/preregistro-envio_multibulto.tpl');
        }
        //CorreosOficialUtils::varDump("request=", $request, false);
        return $request;
    }

    public static function prepareRequestCorreosCancelShipping($idioma, $codCertificado)
    {

        $smarty = CorreosOficialSmarty::loadSmartyInstance();

        $shipping_data_request = array();
        $shipping_data_request['idioma'] = $idioma;
        $shipping_data_request['codCertificado'] = $codCertificado;

        $smarty->assign("shipping_data", $shipping_data_request);
        $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/preregistro/cancelar-envio.tpl');

        return $request;
    }

    public static function prepareRequestCorreosPickup($pickup_data)
    {

        $smarty = CorreosOficialSmarty::loadSmartyInstance();

        $pickup_data_request = array();

        $pickup_data_request['contract_number'] = $pickup_data['client'][0]['CorreosContract'];
        $pickup_data_request['client_number'] = $pickup_data['client'][0]['CorreosCustomer'];
        $pickup_data_request['CorreosOv2Code'] = $pickup_data['client'][0]['CorreosOv2Code'];

        $pickup_data_request['order_reference'] = $pickup_data['order_reference'];
        $pickup_data_request['pickup_date'] = date('d/m/Y', strtotime($pickup_data['pickup_date']));

        $pickup_data_request['sender_from_time'] = date('H:i', strtotime($pickup_data['sender_from_time']));
        $pickup_data_request['sender_address'] = $pickup_data['sender_address'];
        $pickup_data_request['sender_city'] = $pickup_data['sender_city'];
        $pickup_data_request['sender_cp'] = $pickup_data['sender_cp'];
        $pickup_data_request['sender_name'] = $pickup_data['sender_name'];
        $pickup_data_request['sender_phone'] = $pickup_data['sender_phone'];
        $pickup_data_request['sender_email'] = $pickup_data['sender_email'];

        $pickup_data_request['observations'] = "";
        $pickup_data_request['bultos'] = $pickup_data['bultos'];

        $collection_weight = array(
            '10' => 0.5,
            '20' => 2,
            '30' => 5,
            '40' => 30,
            '50' => 100,
            '60' => 100
        );

        $pickup_data_request['type_weight_vol'] = $pickup_data['package_type'];
        $pickup_data_request['weight'] = array_key_exists($pickup_data['package_type'], $collection_weight) ? self::getFloatValue($collection_weight[$pickup_data['package_type']]) * 1000 : 1000;

        $pickup_data_request['label_print'] = $pickup_data['print_label'] == 'S' ? 'S' : 'N';
        $pickup_data_request['shipping_numbers'] = $pickup_data['shipping_numbers'];

        $smarty->assign("pickup_data", $pickup_data_request);
        $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/recogida/ordenar_recogida.tpl');

        return $request;
    }

    public static function prepareRequestCorreosCancelPickup($pickup_data)
    {

        $smarty = CorreosOficialSmarty::loadSmartyInstance();

        $pickup_data_request = array();
        $pickup_data_request['confirmation_code'] = $pickup_data['codSolicitud'];
        $pickup_data_request['contract_number'] = $pickup_data['client'][0]['CorreosContract'];
        $pickup_data_request['client_number'] = $pickup_data['client'][0]['CorreosCustomer'];
        $pickup_data_request['CorreosOv2Code'] = $pickup_data['client'][0]['CorreosOv2Code'];

        $smarty->assign("pickup_data", $pickup_data_request);
        $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/recogida/cancelar_recogida.tpl');

        return $request;
    }

    public static function prepareRequestCorreosConsultaSRE($pickup_data)
    {

        $smarty = CorreosOficialSmarty::loadSmartyInstance();

        $pickup_data_request = array();
        $pickup_data_request['CodigoSRE'] = $pickup_data['CodigoSRE'];
        $pickup_data_request['contract_number'] = $pickup_data['CorreosContract'];
        $pickup_data_request['client_number'] = $pickup_data['CorreosCustomer'];
        $pickup_data_request['CorreosOv2Code'] = $pickup_data['CorreosOv2Code'];
        $pickup_data_request['ModoOperacion'] = $pickup_data['ModoOperacion'];

        $smarty->assign("pickup_data", $pickup_data_request);
        $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/recogida/consultar_recogida.tpl');

        return $request;
    }

    public static function prepareRequestCorreosReturn($shipping_data)
    {

        $smarty = CorreosOficialSmarty::loadSmartyInstance();
        $shipping_data_request = array();
        $num_bulto = $shipping_data['bulto'];

        // Canal de Origen Bulto/Multibulto
        $shipping_data_request['source_channel'] = $shipping_data['source_channel'];

        // Remitente es ahora el antiguo destinatario
        $shipping_data_request['sender_name'] = $shipping_data['order_form']['customer_firstname'] . " " . $shipping_data['order_form']['customer_lastname'];
        $shipping_data_request['sender_address'] = $shipping_data['order_form']['customer_address'];
        $shipping_data_request['sender_cp'] = CorreosOficialUtils::getSenderPostalCode($shipping_data['order_form']['customer_country'], $shipping_data['order_form']['customer_cp']);
        $shipping_data_request['sender_nif_cif'] = $shipping_data['order_form']['customer_dni'];
        $shipping_data_request['sender_city'] = $shipping_data['order_form']['customer_city'];
        $shipping_data_request['sender_contact'] = $shipping_data['order_form']['customer_firstname'] . " " . $shipping_data['order_form']['customer_lastname'];
        $shipping_data_request['sender_phone'] = $shipping_data['order_form']['customer_phone'];
        $shipping_data_request['sender_email'] = $shipping_data['order_form']['customer_email'];
        $shipping_data_request['sender_iso_code_pais'] = $shipping_data['order_form']['customer_country'];

        // Destinatario es ahora el antiguo remitente
        $shipping_data_request['customer_company'] = "";
        $shipping_data_request['customer_firstname'] = $shipping_data['order_form']['sender_name'];
        $shipping_data_request['customer_lastname1'] = "";
        $shipping_data_request['customer_lastname2'] = "";
        $shipping_data_request['delivery_address'] = $shipping_data['order_form']['sender_address'];
        $shipping_data_request['delivery_address2'] = "";
        $shipping_data_request['delivery_city'] = $shipping_data['order_form']['sender_city'];
        $shipping_data_request['delivery_state'] = "";
        $shipping_data_request['delivery_postcode'] = $shipping_data['order_form']['sender_cp'];
        $shipping_data_request['delivery_zip'] = $shipping_data['order_form']['sender_cp'];
        $shipping_data_request['delivery_country_iso'] = $shipping_data['order_form']['sender_country'];
        $shipping_data_request['phone'] = $shipping_data['order_form']['sender_phone'];
        $shipping_data_request['customer_email'] = $shipping_data['order_form']['sender_email'];
        $shipping_data_request['customer_dni'] = CorreosOficialUtils::nifIsAnString($shipping_data['order_form']['sender_nif_cif']);
        $shipping_data_request['phone_mobile'] = CorreosOficialUtils::getMobilePhone($shipping_data['order_form']['sender_phone'], $shipping_data_request['delivery_country_iso']);
        $shipping_data_request['phone_mobile_sms'] = CorreosOficialUtils::getMobilePhone($shipping_data['order_form']['sender_phone'], $shipping_data_request['delivery_country_iso'], "S0148");
        $shipping_data_request['mobile_lang'] = "";

        // Datos pedido
        $shipping_data_request['CorreosKey'] = $shipping_data['client'][0]['CorreosKey'];
        $shipping_data_request['carrier_code'] = "S0148";

        $shipping_data_request['delivery_mode'] = 'ST';
        $shipping_data_request['contra_reembolso'] = 0;
        $shipping_data_request['seguro'] = 0;
        $shipping_data_request['id_office'] = "";
        $shipping_data_request['id_citypaq'] = "";

        // BULTO ÚNICO
        //$shipping_data_request['total_bultos'] = 1; // No se usa ya que en la plantilla está forzado a 1
        $shipping_data_request['weight'] = self::getFloatValue($shipping_data['order_form']['packageWeightReturn_' . $num_bulto]) * 1000;
        $shipping_data_request['long'] = $shipping_data['order_form']['packageLargeReturn_1'];
        $shipping_data_request['height'] = $shipping_data['order_form']['packageHeightReturn_1'];
        $shipping_data_request['width'] = $shipping_data['order_form']['packageWidthReturn_1'];

        $shipping_data_request['has_size'] = self::hasSize($shipping_data_request['long'], $shipping_data_request['width'], $shipping_data_request['height']);
        if ($shipping_data_request['has_size']) {
            $shipping_data_request['v_weight'] = static::calculateVWeight($shipping_data_request['long'], $shipping_data_request['width'], $shipping_data_request['height']);
        } else {
            $shipping_data_request['v_weight'] = 0;
        }

        // Descripción aduanera y número tarifario
        $shipping_data_request['require_customs_doc'] = $shipping_data['order_form']['require_customs_doc'];

        if ($shipping_data_request['require_customs_doc'] == 1) {
            $shipping_data_request['customs_consignor_reference'] = "";
            $shipping_data_request['customs_descs'] = $shipping_data['customs_desc_array'];
        }

        $shipping_data_request['order_reference'] = $shipping_data['order_form']['packageRef_1'];
        $shipping_data_request['order_number'] = $shipping_data['order_form']['order_number'];

        $shipping_data_request['texto_adicional'] = "";
        $shipping_data_request['observaciones1'] = "";
        $shipping_data_request['observaciones2'] = "";

        $smarty->assign("shipping_data", $shipping_data_request);

        return $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/preregistro/preregistro-envio.tpl');
    }

    public static function calculateVWeight($long, $height, $width)
    {
        return round($long * $height * $width / 6);
    }

    public static function hasSize($long, $height, $width)
    {
        return $long > 0 && $height > 0 && $width > 0;
    }

    public static function getFloatValue($value)
    {
        return floatval(str_replace(',', '.', $value));
    }
}
