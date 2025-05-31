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

if (!defined('WC_VERSION')) {
	die;
}

require_once __DIR__ . '/../../classes/CorreosOficialOrder.php';
require_once __DIR__ . '/../../classes/CorreosOficialOrders.php';
require_once __DIR__ . '/../../classes/CorreosOficialReturnsMail.php';
require_once __DIR__ . '/../../classes/CorreosOficialErrorManager.php';
require_once __DIR__ . '/../../classes/CorreosOficialSenders.php';
require_once __DIR__ . '/../../classes/CorreosOficialCheckout.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialUtilitiesDao.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Correos/CorreosSoap.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Correos/CorreosRest.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Cex/CexRest.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/NeedCustoms.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/CorreosOficialUtils.php';

require_once __DIR__ . '/../../vendor/pdfmerger.php';

class CorreosOficialAdminOrderModuleFrontController {

	public $context;
	public $db;
	public $correos_soap;
	public $correos_rest;
	public $cex_rest;
	public $horaActual;
	public $statusProcessActive;
	public $utilitiesDAO;

	public function __construct() {
		$this->utilitiesDAO = new CorreosOficialUtilitiesDao();
		$this->correos_soap = new CorreosSoap();
		$this->correos_rest = new CorreosRest();
		$this->cex_rest = new CexRest();
		$this->horaActual =gmdate('Y-m-d H:i:s', time());

		$this->initContent();
	}

	public function initContent() {

		$this->statusProcessActive = $this->utilitiesDAO->readSettings('ShowShippingStatusProcess')->value;

		$action = sanitize_text_field(isset($_REQUEST['action']) ? $_REQUEST['action'] : '');

		switch ($action) {
			case 'RequireCustom':
				$cp_source = Normalization::normalizeData('cp_source');
				$cp_dest = Normalization::normalizeData('cp_dest');
				$country_source = Normalization::normalizeData('country_source');
				$country_dest = Normalization::normalizeData('country_dest');
				$result['require_custom'] = NeedCustoms::isCustomsRequired($cp_source, $cp_dest, $country_source, $country_dest);
				die( json_encode($result) );
				break;
			case 'getSenderById':
				$sender_id = Normalization::normalizeData('sender_id');
				// $sender = CorreosOficialSendersDao::getSenderById($sender_id);
				$sender = CorreosOficialSenders::getSenderById($sender_id);
				die( json_encode($sender) );
				break;
			case 'getOrderStatus':
				$order_id = Normalization::normalizeData('order_id');
				$order_status = $this->getOrderStatus($order_id);
				die( json_encode($order_status) );
				break;
			case 'getSGAOrderStatus':
				$id_order = Normalization::normalizeData('id_order');
				$order_status = $this->getSGAOrderStatus($id_order);
				die( json_encode($order_status) );
				break;
			case 'printLabel':
				$labelType = Normalization::normalizeData('selectedTipoEtiquetaReimpresion');
				$labelFormat = Normalization::normalizeData('selectedFormatEtiquetaReimpresion');
				$labelPosition = Normalization::normalizeData('selectedPosicionEtiquetaReimpresion');
				$idOrder = Normalization::normalizeData('id_order');
				$company = Normalization::normalizeData('company');
				$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByIdOrderForSavedOrder($idOrder);
				$shipping_numbers = $this->mergeArraysIntoOne($shipping_numbers);
				$exp_number = Normalization::normalizeData('exp_number');
				$this->getEtiquetasByExpNumber(false, $company, 'order', $shipping_numbers, $exp_number, $labelType, $labelPosition, $labelFormat);
				break;
			case 'printLabelReturn':
				$selectedTipoEtiquetaReimpresionReturn = Normalization::normalizeData('selectedTipoEtiquetaReimpresionReturn');
				$selectedPosicionEtiquetaReimpresionReturn = Normalization::normalizeData('selectedPosicionEtiquetaReimpresionReturn');
				$company = Normalization::normalizeData('company');
				$idOrder = Normalization::normalizeData('order_id');
				$exp_number = $this->utilitiesDAO->getExpeditionNumberByIdOrderForReturn($idOrder);
				$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByIdOrderForReturns($idOrder);
				$shipping_numbers = $this->mergeArraysIntoOne($shipping_numbers);
				$labels = $this->getEtiquetasByExpNumber(false, $company, 'return', $shipping_numbers, $exp_number, $selectedTipoEtiquetaReimpresionReturn, $selectedPosicionEtiquetaReimpresionReturn);
				die( json_encode($labels) );
				break;
			case 'getCustomsDoc':
				$type = Normalization::normalizeData('type', 'no_uppercase');
				$exp_number = Normalization::normalizeData('exp_number');

				if ($type == 'order') {
					$destination_country = Normalization::normalizeData('customer_country');
					$destination_name = Normalization::normalizeData('customer_name') . ' ' . Normalization::normalizeData('customer_lastname');
				} else if ($type == 'return') {
					$destination_country = Normalization::normalizeData('sender_country');
					$destination_name = Normalization::normalizeData('sender_name');
				} else {
					throw new Exception('ERROR 19010: El tipo debe ser order o return');
				}

				$optionButton = sanitize_text_field(isset($_REQUEST['optionButton']) ? $_REQUEST['optionButton'] : '');

				switch ($optionButton) {
					case 'ImprimirCN23Button':
					case 'ImprimirCN23Button2':
						$optionButton = 'ImprimirCN23Button';
						break;
					case 'ImprimirDUAButton':
					case 'ImprimirDUAButton2':
						$optionButton = 'ImprimirDUAButton';
						break;
					case 'ImprimirDDPButton':
					case 'ImprimirDDPButton2':
						$optionButton = 'ImprimirDDPButton';
						break;
				}
				$this->getDocAduanera($type, $exp_number, $optionButton, $destination_country, $destination_name);
				break;
			case 'deleteFiles':
				CorreosOficialUtils::deleteFiles();
				break;
			case 'generateOrder':
				$result = $this->generateOrder();
				die( json_encode($result) );
				break;
			case 'cancelOrder':
				$result_cancel_order = $this->cancelOrder('order');
				die( json_encode($result_cancel_order) );
				break;
			case 'cancelReturn':
				$result_cancel_return = $this->cancelOrder('return');
				die( json_encode($result_cancel_return) );
				break;
			case 'generatePickup':
				$result_pickup = $this->generatePickup();
				die( json_encode($result_pickup) );
				break;
			case 'cancelPickup':
				$result_cancel_pickup = $this->cancelPickup();
				die( json_encode($result_cancel_pickup) );
				break;
			case 'generateReturn':
				$result_generate_return = $this->generateReturn();
				die( json_encode($result_generate_return) );
				break;
			case 'sendEmail':
				$resut_send_email = $this->sendEmail();
				die( json_encode($resut_send_email) );
				break;
		}
	}

	public function generateReturn() {
		$order_id = Normalization::normalizeData('order_id');
		$company = Normalization::normalizeData('company');
		$order_form = Normalization::normalizeData('order_form');
		$id_sender = Normalization::normalizeData('id_sender');

		$devolutionSucceded = false;

		$order_form['customer_dni'] = CorreosOficialOrder::getRealDnI($order_id);

		$bultos = $order_form['correos-num-parcels-return'];

		if ($company == 'Correos') {
			$customs_desc_array = self::getCustomsDesc($bultos);
		} else {
			$customs_desc_array = array();
		}

		$reference = $order_form['order_reference'];

		// Es posible que el remitente no tenga contrato para la compaía
		$client = $this->utilitiesDAO->getDataClient($company, false, $id_sender);


		// Si no tenemos contrato devolvemos error
		if ($client[0]['id'] == null) {

			$result_errors[] = array(
				'codigoRetorno' => null,
				'mensajeRetorno' => CorreosOficialErrorManager::checkStateConnection('401'),
			);

			return array(
				'aciertos' => array(),
				'errores' => $result_errors,
			);
		}

		$result_done = array();
		$result_errors = array();

		switch ($company) {
			// DEVOLUCIÓN CORREOS
			case 'Correos':
				$this->utilitiesDAO->deleteReturns($order_id);

				for ($i = 1; $i <= $bultos; $i++) {

					$shipping_return_data = array(
					'id_order' => $order_id,
					'company' => $company,
					'bulto' => $i,
					'order_form' => $order_form,
					'client' => $client,
					'customs_desc_array' => $customs_desc_array,
					'source_channel' => 'WOO',
					'needPickup' => $order_form['needPickup'],
					'order_reference' => $order_form['order_reference'],
					'pickup_date' => $order_form['pickup_date'],
					'sender_from_time' => $order_form['sender_from_time'],
					'sender_to_time' => $order_form['sender_to_time'],
					);

					$result_correos = $this->correos_soap->registrarDevolucion($shipping_return_data, $id_sender);

					if ($result_correos['codigoRetorno'] == '0') {

						$CodExpedicion = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->CodExpedicion;
						$CodEnvio = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;

						$return_done = array(
						'id_order' => $order_id,
						'shipping_number' => $CodEnvio,
						'exp_number' => $CodExpedicion,
						);

						$return_update = array(
						'id_order' => $order_id,
						'id_sender' => $id_sender,
						'reference' => $reference,
						'shipping_number' => $CodExpedicion,
						'carrier_type' => 'Correos',
						'date_add' => $this->horaActual,
						'id_carrier' => '0',
						'id_product' => '0',
						'bultos' => $i,
						'AT_code' => '',
						'last_status' => 'Prerregistrado',
						'status' => 'Grabado',
						'updated_at' => $this->horaActual,
						'pickup' => 0,
						'pickup_status' => 'None',
						'require_customs_doc' => 0,
						);

						$resultado = array(
						'codigoRetorno' => mb_convert_encoding($result_correos['codigoRetorno'], 'UTF-8'),
						'exp_number' => mb_convert_encoding($CodExpedicion, 'UTF-8'),
						'shipping_number' => mb_convert_encoding($CodEnvio, 'UTF-8'),
						'num_bulto' => $i,
						'changeStatus' => $this->getStatus('ShipmentReturned'),
						);

						$result_done[] = $resultado;

						$this->utilitiesDAO->insertDataOrder('correos_oficial_saved_returns', $return_done);
						$this->utilitiesDAO->insertReturn($return_update);
						$devolutionSucceded = true;
					} else {
						$mensaje_retorno = mb_convert_encoding($result_correos['mensajeRetorno'], 'UTF-8');

						if ($result_correos['status_code'] == 200) {
							$resultado = array(
							'codigoRetorno' => $result_correos['codigoRetorno'][0],
							'mensajeRetorno' => $mensaje_retorno,
							'num_bulto' => $i,
							);
							$result_errors[] = $resultado;
						} else if ($result_correos['status_code'] != 200) {

							if ($result_correos['status_code'] == 0) {
								$mensaje_retorno = CorreosOficialErrorManager::checkStateConnection($status_code);
							}

							$resultado = array(
							'codigoRetorno' => $result_correos['codigoRetorno'][0],
							'mensajeRetorno' => $mensaje_retorno,
							'num_bulto' => $i,
							);
							$result_errors[] = $resultado;
						}
					}

				}

				break;
			// PREREGISTRO CEX
			case 'CEX':
				$this->utilitiesDAO->deleteReturns($order_id);

				$shipping_return_data = array(
				'id_order' => $order_id,
				'company' => $company,
				'bultos' => $bultos,
				'order_form' => $order_form,
				'client' => $client,
				'needPickup' => $order_form['needPickup'],
				'order_reference' => $order_form['order_reference'],
				'pickup_date' => $order_form['pickup_date'],
				'sender_from_time' => $order_form['sender_from_time'],
				'sender_to_time' => $order_form['sender_to_time'],
				);

				$result_cex = $this->cex_rest->registrarDevolucion($shipping_return_data, $id_sender);
				$result_cex_decoded = json_decode($result_cex['json_retorno'], true);

				if ($result_cex_decoded['codigoRetorno'] == '0') {
					$bultos_reg = array();
					$bultos_reg_cex = $result_cex_decoded['listaBultos'];
					$num_bultos_reg = count($result_cex_decoded['listaBultos']);
					$CodExpedicion = $result_cex_decoded['datosResultado'];

					foreach ($bultos_reg_cex as $bulto => $field) {
						$CodEnvio = str_replace("'", '', $field['codUnico']);
						$orden = intval($field['orden']);

						$return_done = array(
						'id_order' => $order_id,
						'shipping_number' => $CodEnvio,
						'exp_number' => $CodExpedicion,
						);

						$resultado = array(
						'codigoRetorno' => mb_convert_encoding($result_cex_decoded['codigoRetorno'], 'UTF-8'),
						'exp_number' => mb_convert_encoding($CodExpedicion, 'UTF-8'),
						'shipping_number' => mb_convert_encoding($CodEnvio, 'UTF-8'),
						'num_bulto' => $orden,
						);
						$result_done[] = $resultado;

						$this->utilitiesDAO->insertDataOrder('correos_oficial_saved_returns', $return_done);
					}

					$return_update = array(
					'id_order' => $order_id,
					'id_sender' => $id_sender,
					'reference' => $reference,
					'shipping_number' => $CodExpedicion,
					'carrier_type' => 'CEX',
					'date_add' => $this->horaActual,
					'id_carrier' => '0',
					'id_product' => '0',
					'bultos' => $num_bultos_reg,
					'AT_code' => $order_form['code_at'],
					'last_status' => 'Prerregistrado',
					'status' => 'Grabado',
					'updated_at' => $this->horaActual,
					'pickup' => 0,
					'pickup_status' => 'None',
					'require_customs_doc' => 0,
					);

					$this->utilitiesDAO->insertReturn($return_update);

					// Para CEX pasamos a guardar la recogida ya que se hace en la misma llamada al WS
					$aux_date = date_create_from_format('dmY', $result_cex_decoded['fechaRecogida']);
					$pickup_date_format = date_format($aux_date, 'Y-m-d');

					$pickup_order_data = array(
						'id_order' => $order_id,
						'pickup_number' => $result_cex_decoded['numRecogida'],
						'pickup_date' => $pickup_date_format,
						'pickup_from_hour' => $result_cex_decoded['horaRecogidaDesde'],
						'pickup_to_hour' => $result_cex_decoded['horaRecogidaHasta'],
						'package_size' => 0,
						'print_label' => 'N',
						'pickup_status' => 'Grabado',
					);

					$this->utilitiesDAO->saveReturnPickup($pickup_order_data);

					$devolutionSucceded = true;
					CorreosOficialUtils::deleteFiles();
				} elseif ($result_cex['status_code'] == 200) {

						$resultado = array(
						'codigoRetorno' => $result_cex_decoded['codigoRetorno'],
						'mensajeRetorno' => $result_cex_decoded['mensajeRetorno'],
						'changeStatus' => $this->getStatus('ShipmentReturned'),
						);
						$result_errors[] = $resultado;
				} else if ($result_cex_decoded['status_code'] != 200) {
					if ($result_cex_decoded['status_code'] == 0) {
						$result_cex_decoded['mensajeRetorno'] = CorreosOficialErrorManager::checkStateConnection($result_cex_decoded['status_code']);
					}

					$resultado = array(
					'codigoRetorno' => $result_cex_decoded['codigoRetorno'],
					'mensajeRetorno' => $result_cex_decoded['mensajeRetorno'],
					'bultos' => 1,
					);
					$result_errors[] = $resultado;
				}

				break;
		}

		if ($devolutionSucceded && $this->utilitiesDAO->readSettings('ShowShippingStatusProcess')->value == 'on') {
			$config_status = $this->utilitiesDAO->readSettings('ShipmentReturned');
			CorreosOficialUtils::changeOrderStatus($order_id, $config_status->value);
		}

		return array(
			'aciertos' => $result_done,
			'errores' => $result_errors,
		);
	}

	public function generateOrder() {
		$order_id = Normalization::normalizeData('order_id');
		$company = Normalization::normalizeData('company');
		$delivery_mode = Normalization::normalizeData('delivery_mode', 'no_uppercase');
		$id_carrier = 0;
		$id_product = Normalization::normalizeData('id_product');
		$order_form = Normalization::normalizeData('order_form');

		$needPickup = Normalization::normalizeData('needPickup');
		$pickupDateRegister = Normalization::normalizeData('pickupDateRegister');
		$pickupFromRegister = Normalization::normalizeData('pickupFromRegister');
		$pickupToRegister = Normalization::normalizeData('pickupToRegister');
		$needPrintLablPickup = Normalization::normalizeData('needPrintLablPickup');
		$packetSize = Normalization::normalizeData('packetSize');

		$id_sender = Normalization::normalizeData('id_sender');

		$order_form['customer_dni'] = CorreosOficialOrder::getRealDnI($order_id);
		$id_zone = $order_form['id_zone'];
		$bultos = $order_form['correos-num-parcels'];

		// get added_values
		$added_values_cash_on_delivery = Normalization::normalizeData('added_values_cash_on_delivery');
		$added_values_insurance = Normalization::normalizeData('added_values_insurance');
		$added_values_partial_delivery = Normalization::normalizeData('added_values_partial_delivery');
		$added_values_delivery_saturday = Normalization::normalizeData('added_values_delivery_saturday');
		$added_values_cash_on_delivery_iban = Normalization::normalizeData('added_values_cash_on_delivery_iban');
		$added_values_cash_on_delivery_value = Normalization::normalizeData('added_values_cash_on_delivery_value');
		$added_values_insurance_value = Normalization::normalizeData('added_values_insurance_value');

		$added_values = array(
			'added_values_cash_on_delivery' => $added_values_cash_on_delivery == 'true' ? 1 : 0,
			'added_values_insurance' => $added_values_insurance == 'true' ? 1 : 0,
			'added_values_partial_delivery' => $added_values_partial_delivery == 'true' ? 1 : 0,
			'added_values_delivery_saturday' => $added_values_delivery_saturday == 'true' ? 1 : 0,
			'added_values_cash_on_delivery_iban' => $added_values_cash_on_delivery == 'true' ? $added_values_cash_on_delivery_iban : null,
			'added_values_cash_on_delivery_value' => $added_values_cash_on_delivery == 'true' ? $added_values_cash_on_delivery_value : null,
			'added_values_insurance_value' => $added_values_insurance == 'true' ? $added_values_insurance_value : null,
		);

		// get información de los bultos
		$info_bultos = isset($_REQUEST['info_bultos']) ?
			json_decode(stripslashes($_REQUEST['info_bultos']), true) : []; // phpcs:ignore
		$shippingSucceed = false;

		if ($company == 'Correos') {
			$customs_desc_array = self::getCustomsDesc($bultos);
		} else {
			$customs_desc_array = array();
		}

		$correos_order = new CorreosOficialOrder($order_id);

		$id_carrier = $correos_order->getIdCarrier($order_id, $id_product);

		if (!$id_carrier) {
			$id_carrier = 0;
		}

		$reference = $order_form['order_reference'];

		$client = $this->utilitiesDAO->getDataClient($company, false, $id_sender);

		$shipping_data = array(
			'id_order' => $order_id,
			'company' => $company,
			'bultos' => $bultos,
			'delivery_mode' => $delivery_mode,
			'order_form' => $order_form,
			'client' => $client,
			'customs_desc_array' => $customs_desc_array,
			'source_channel' => 'WOO',
			'needPickup' => $needPickup,
			'pickupDateRegister' => $pickupDateRegister,
			'pickupFromRegister' => $pickupFromRegister,
			'pickupToRegister' => $pickupToRegister,
			'needPrintLablPickup' => $needPrintLablPickup,
			'packetSize' => $packetSize,
		);

		$sender_postal_code = $order_form['sender_cp'];
		$customer_postal_code = $order_form['customer_cp'];
		$sender_country = $order_form['sender_country'];
		$customer_country = $order_form['customer_country'];

		$require_customs_doc = NeedCustoms::isCustomsRequired($sender_postal_code, $customer_postal_code, $sender_country, $customer_country);

		switch ($company) {
			// PREREGISTRO CORREOS
			case 'Correos':
				$result_correos = $this->correos_soap->registrarEnvio($shipping_data, null, $id_sender);
				// Comprobamos si tenemos que registrar recogida
				$updatePickup = false;
				$pickupError = false;

				$bultosDatas = array();

				if ($bultos > 1) {
					foreach ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->Bultos->Bulto as $bultoInside) {
						$bultosDatas[] = $bultoInside->CodEnvio;
					}
				// Si no hay error...
				} elseif ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto) {
					$bultosDatas = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;
				}
					/*
					$bultosDatas = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;
					*/
					// La linea de arriba da un error
					// Dependiendo de si ocurre un error o no, recibimos cosas diferentes en $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio
					

					// Si hay error...
					//if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->BultoError) {
						// no hacemos nada, por ahora
						// ya se está mostrando bien el error, pero dejamos esta condición para saber que
						// BultoError trae la información correspondiente al error que se ha producido.
					//}

				// FIX
				// $result_correos['codigoRetorno']  devuelve un SimpleXMLElement que puede ser:
				//      0 : "0"  si no hay errores
				//      0 : "1"  si hay algún error
				// La comparación $result_correos['codigoRetorno'] == '0'   es erronea
				//      antes hay que convertir el SimpleXMLElement en un string

				//if ($result_correos['codigoRetorno'] == '0') {
					$result_correos_string = (string) $result_correos['codigoRetorno'];
				

				if ($needPickup === 'S' && $result_correos_string == '0') {
					$pickup_details_array = array(
						'id_order' => $order_id,
						'bultos' => $bultos,
						'order_reference' => $reference,
						'pickup_date' => $pickupDateRegister,
						'sender_from_time' => $pickupFromRegister,
						'sender_to_time' =>$pickupFromRegister,
						'sender_address' => $order_form['sender_address'],
						'sender_city' => $order_form['sender_city'],
						'sender_cp' => $order_form['sender_cp'],
						'sender_name' => $order_form['sender_name'],
						'sender_contact' => $order_form['sender_contact'],
						'sender_phone' => $order_form['sender_phone'],
						'sender_email' => $order_form['sender_email'],
						'sender_nif_cif' => $order_form['sender_nif_cif'],
						'sender_country' => $order_form['sender_country'],
						'producto' => $id_product,
						'print_label' => $needPrintLablPickup,
						'package_type' => $packetSize,
						'shipping_numbers' => CorreosOficialUtils::transformArrayForPickups($bultosDatas),
						'client' => $client,
					);

					$result_correos_pickups = $this->correos_soap->registrarRecogida($pickup_details_array, $id_sender);
					if ($result_correos_pickups['codigoRetorno'] == '0') {

						$pickup_number = $result_correos_pickups['codSolicitud'];

						$updatePickup = array(
							'id_order' => $order_id,
							'pickup_number' => mb_convert_encoding($pickup_number, 'UTF-8'),
							'pickup_date' => $pickupDateRegister,
							'pickup_from_hour' => $pickupFromRegister,
							'pickup_to_hour' => $pickupToRegister,
							'package_size' => intval($packetSize),
							'print_label' => $needPrintLablPickup,
							'pickup_status' => 'Grabado',
							'pickup' => 1,
						);
					}
				}

				if ($result_correos_string == '0') {
					// Bulto único
					if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->TotalBultos == 1) {
						$CodExpedicion = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->CodExpedicion;
						$CodEnvio = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;

						$order_done = array(
						'id_order' => $order_id,
						'shipping_number' => $CodEnvio,
						'exp_number' => $CodExpedicion,
						);

						$order_update = array(
						'id_order' => $order_id,
						'id_sender' => $order_form['senderSelect'],
						'reference' => $reference,
						'shipping_number' => $CodExpedicion,
						'carrier_type' => 'Correos',
						'date_add' => $this->horaActual,
						'id_carrier' => $id_carrier,
						'id_product' => $id_product,
						'bultos' => 1,
						'AT_code' => '',
						'last_status' => 'Prerregistrado',
						'status' => 'Grabado',
						'updated_at' => $this->horaActual,
						'pickup' => 0,
						'pickup_status' => 'None',
						'require_customs_doc' => $require_customs_doc == true ? 1 : 0,
						);

						$bultos_reg[] = array(
						'package_number' => 1,
						'shipping_number' => mb_convert_encoding($CodEnvio, 'UTF-8'),
						);

						$codRet = mb_convert_encoding($result_correos['codigoRetorno'], 'UTF-8');
						$textRet = '';
						if ($needPickup == 'S' && $result_correos_pickups['codigoRetorno'] != 0) {
							$textRet .= __('Se genero el envio correctamente aunque la recogida ', 'correosoficial') . $result_correos_pickups['mensajeRetorno'];
							$codRet = 1111;
						}

						$result = array(
						'codigoRetorno' => $codRet,
						'num_bultos_reg' => 1,
						'bultos_reg' => $bultos_reg,
						'exp_number' => mb_convert_encoding($CodExpedicion, 'UTF-8'),
						'mensajeRetorno' => $textRet,
						'changeStatus' => $this->getStatus('ShipmentPreregistered'),
						);

						/* merge info bultos - Bulto único Correos */
						$order_done = array_merge($order_done, $info_bultos[1]);
						$this->utilitiesDAO->insertDataOrder('correos_oficial_saved_orders', $order_done);

						// Insertamos los datos de Oficina o Citypaq Monobulto
						$this->insertRequestData($order_id);

						// merge added_values para insertarlo en la db
						$order_update = array_merge($order_update, $added_values);
						$this->utilitiesDAO->insertOrder($order_update);
						$shippingSucceed = true;
						
						if ($updatePickup) {
							$this->utilitiesDAO->savePickup($updatePickup);
						}

						// Multibulto
					} elseif ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->TotalBultos > 1) {
							$bultos_reg = array();
							$bultos_reg_correos = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->Bultos;
							$num_bultos_reg = intval($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->TotalBultos);
							$CodExpedicion = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->CodExpedicion;

						foreach ($bultos_reg_correos->Bulto as $bulto => $field) {
							$CodEnvio = $field->CodEnvio;
							$NumBulto = $field->NumBulto;

							$order_done = array(
							'id_order' => $order_id,
							'shipping_number' => $CodEnvio,
							'exp_number' => $CodExpedicion,
							);

							$bultos_reg[] = array(
							'package_number' => mb_convert_encoding($NumBulto, 'UTF-8'),
							'shipping_number' => mb_convert_encoding($CodEnvio, 'UTF-8'),
							);

							/* merge info bultos -  multibulto Correos */
							$order_done = array_merge($order_done, $info_bultos[(int) $NumBulto]);
							$this->utilitiesDAO->insertDataOrder('correos_oficial_saved_orders', $order_done);

							// Insertamos los datos de Oficina o Citypaq Monobulto
							$this->insertRequestData($order_id);
						}

							$order_update = array(
							'id_order' => $order_id,
							'id_sender' => $order_form['senderSelect'],
							'reference' => $reference,
							'shipping_number' => $CodExpedicion,
							'carrier_type' => 'Correos',
							'date_add' => $this->horaActual,
							'id_carrier' => $id_carrier,
							'id_product' => $id_product,
							'bultos' => $num_bultos_reg,
							'AT_code' => '',
							'last_status' => 'Prerregistrado',
							'status' => 'Grabado',
							'updated_at' => $this->horaActual,
							'pickup' => 0,
							'pickup_status' => 'None',
							'require_customs_doc' => $require_customs_doc == true ? 1 : 0,
							);

							$codRet = mb_convert_encoding($result_correos['codigoRetorno'], 'UTF-8');
							$textRet = '';
							if ($needPickup == 'S' && $result_correos_pickups['codigoRetorno'] != 0) {
								$textRet .= __('Se genero el envio correctamente aunque la recogida ', 'correosoficial') . $result_correos_pickups['mensajeRetorno'];
								$codRet = 1111;
							}

							$result = array(
							'codigoRetorno' => $codRet,
							'num_bultos_reg' => $num_bultos_reg,
							'bultos_reg' => $bultos_reg,
							'exp_number' => mb_convert_encoding($CodExpedicion, 'UTF-8'),
							'mensajeRetorno' => $textRet,
							'changeStatus' => $this->getStatus('ShipmentPreregistered'),
							);

							// merge added_values para insertarlo en la db
							$order_update = array_merge($order_update, $added_values);
							$this->utilitiesDAO->insertOrder($order_update);
							$shippingSucceed = true;
							
							if ($updatePickup) {
								$this->utilitiesDAO->savePickup($updatePickup);
							}
					}
				} else {
					$mensaje_retorno = '';
					if ($bultos == 1) {
						$mensaje_retorno = mb_convert_encoding($result_correos['mensajeRetorno'], 'UTF-8');
					} else {
						$bultos_error = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->BultosError;
						for ($i = 0; $i < $bultos; $i++) {
							if (isset($bultos_error[0]->BultoError[$i]->NumBulto)) {
								$mensaje_retorno = $mensaje_retorno . 'Error bulto ' . $bultos_error[0]->BultoError[$i]->NumBulto . ': ' . $bultos_error[0]->BultoError[$i]->DescError . '</br>';
							}
						}
					}

					if ($result_correos['status_code'] != 200) {

						if ($result_correos['status_code'] == 0) {
							$mensaje_retorno = CorreosOficialErrorManager::checkStateConnection($result_correos['status_code']);
						}

						if (strstr($mensaje_retorno, 'DATOSADUANA')) {
							$mensaje_retorno = __('You must indicate at least one customs description in Customs related data', 'correosoficial');
						}

						$result = array(
						'codigoRetorno' => '',
						'mensajeRetorno' => 'ERROR 18004: ' . $mensaje_retorno,
						'bultos' => '',
						);
					} else {
						// Traducción de mensaje del webservice de Correos
						if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->BultoError->Error == 159) {
							$mensaje_retorno = __('The dimensions of the packages must be indicated', 'correosoficial');
						}
						$result = array(
						//'codigoRetorno' => $result_correos['codigoRetorno'][0],
						'codigoRetorno' => (string) $result_correos['codigoRetorno'],
						'mensajeRetorno' => $mensaje_retorno,
						'bultos' => $bultos,
						);
					}

				}
				break;
			// PREREGISTRO CEX
			case 'CEX':
				$shipping_data['ChangeLogoOnLabel'] = CorreosOficialConfigDao::getConfigValue('ChangeLogoOnLabel');
				$shipping_data['UploadLogoLabels'] = CorreosOficialConfigDao::getConfigValue('UploadLogoLabels');

				$result_cex = $this->cex_rest->registrarEnvio($shipping_data, $id_sender);
				$result_cex_decoded = json_decode($result_cex['json_retorno'], true);

				if (isset($result_cex_decoded['codigoRetorno']) && $result_cex_decoded['codigoRetorno'] == '0') {
					$bultos_reg = array();
					$bultos_reg_cex = $result_cex_decoded['listaBultos'];
					$num_bultos_reg = count($result_cex_decoded['listaBultos']);
					$CodExpedicion = $result_cex_decoded['datosResultado'];

					foreach ($bultos_reg_cex as $bulto => $field) {
						$CodEnvio = str_replace("'", '', $field['codUnico']);
						$orden = intval($field['orden']);

						$order_done = array(
						'id_order' => $order_id,
						'shipping_number' => $CodEnvio,
						'exp_number' => $CodExpedicion,
						);

						$bultos_reg[] = array(
						'package_number' => $orden,
						'shipping_number' => $CodEnvio,
						);

						/* merge info bultos - multibulto CEX */
						$order_done = array_merge($order_done, $info_bultos[(int) $orden]);
						$this->utilitiesDAO->insertDataOrder('correos_oficial_saved_orders', $order_done);
						$this->insertRequestData($order_id);
					}

					/**
					 * Aplica cuando es un transportista externo
					 */
					if (empty($id_carrier)) {
						$carrier_order = CorreosOficialCarrier::getCarrierByProductId($id_product, $id_zone);
						$id_carrier = $carrier_order['id_carrier'];
					}

					$order_update = array(
					'id_order' => $order_id,
					'id_sender' => $order_form['senderSelect'],
					'reference' => $reference,
					'shipping_number' => $CodExpedicion,
					'carrier_type' => 'CEX',
					'date_add' => $this->horaActual,
					'id_carrier' => $id_carrier,
					'id_product' => $id_product,
					'bultos' => $num_bultos_reg,
					'AT_code' => $order_form['AT_code'],
					'last_status' => 'Prerregistrado',
					'status' => 'Grabado',
					'updated_at' => $this->horaActual,
					'pickup' => 0,
					'pickup_status' => 'None',
					'require_customs_doc' => 0,
					);

					$result = array(
					'codigoRetorno' => mb_convert_encoding($result_cex_decoded['codigoRetorno'], 'UTF-8'),
					'num_bultos_reg' => $num_bultos_reg,
					'bultos_reg' => $bultos_reg,
					'exp_number' => mb_convert_encoding($CodExpedicion, 'UTF-8'),
					'changeStatus' => $this->getStatus('ShipmentPreregistered'),
					);

					// merge added_values para insertarlo en la db
					$order_update = array_merge($order_update, $added_values);
					$this->utilitiesDAO->insertOrder($order_update);
					$shippingSucceed = true;

					$pickupdateDay = substr($result_cex_decoded['fechaRecogida'], 0, 2);
					$pickupdateMonth = substr($result_cex_decoded['fechaRecogida'], 2, 2);
					$pickupdateYear = substr($result_cex_decoded['fechaRecogida'], 4, 4);

					$pickupdateValidFormat = $pickupdateYear . '-' . $pickupdateMonth . '-' . $pickupdateDay;

					if (!empty($result_cex_decoded['numRecogida'])) {
						$update = array(
							'id_order' => $order_id,
							'pickup' => 1,
							'pickup_number' => $result_cex_decoded['numRecogida'],
							'pickup_date' => $pickupdateValidFormat,
							'pickup_from_hour' => $result_cex_decoded['horaRecogidaDesde'],
							'pickup_to_hour' => $result_cex_decoded['horaRecogidaHasta'],
							'package_size' => $packetSize,
							'print_label' => $needPrintLablPickup,
							'pickup_status' => 'Grabado',
						);
						$this->utilitiesDAO->savePickup($update);
					}

					CorreosOficialUtils::deleteFiles();
				} elseif ($result_cex['status_code'] != 200) {
					if ($result_cex['status_code'] == 0) {
						$mensaje_retorno = CorreosOficialErrorManager::checkStateConnection($status_code);
					}
					
					$result = array(
						'codigoRetorno' => $result_cex['status_code'],
						'mensajeRetorno' => $result_cex['mensajeRetorno'],
						'bultos' => '',
					);
				} else {
					$result = array(
						'codigoRetorno' => $result_cex['status_code'],
						'mensajeRetorno' => $result_cex['mensajeRetorno'],
						'bultos' => 1,
					);
				}

				break;
		}
	
		if ($shippingSucceed && $this->utilitiesDAO->readSettings('ShowShippingStatusProcess')->value == 'on') {
			$config_status = $this->utilitiesDAO->readSettings('ShipmentPreregistered');
			CorreosOficialUtils::changeOrderStatus($order_id, $config_status->value);
		}

		return $result;
	}

	public function cancelOrder( $type ) {
		global $wpdb;
		$table_coo = $wpdb->prefix . 'correos_oficial_orders';
		$order_id = Normalization::normalizeData('order_id');
		$id_carrier = Normalization::normalizeData('id_carrier');
		$company = Normalization::normalizeData('company');
		$expedition_number = Normalization::normalizeData('expedition_number');
		$id_sender = Normalization::normalizeData('id_sender');
		$cancelOrderSucceeded = false;
		$pickup_number_return = Normalization::normalizeData('pickup_number_return');
		
		switch ($company) {
			case 'Correos':
				$lang = Normalization::normalizeData('lang');

				if ($type == 'order') {
					$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByExp($expedition_number);
				} else {
					$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByIdOrderForReturns($order_id);
				}

				if (empty($shipping_numbers)) {
					$result_operation = $this->correos_soap->cancelarPreRegistroEnvio($lang, '99999999999999X', $id_sender);
				} else {
					foreach ($shipping_numbers as $shipping_number) {
						$result_operation = $this->correos_soap->cancelarPreRegistroEnvio($lang, $shipping_number['shipping_number'], $id_sender);
					}
					$result_operation['codigoRetorno'] = intval($result_operation['codigoRetorno']);
					$result_operation['changeStatus'] = $this->getStatus('ShipmentCanceled');
					$cancelOrderSucceeded = true;
				}
				break;
			case 'CEX':
				$returnPickupCancelled = false;

				$hasPickup = $wpdb->get_results($wpdb->prepare(
					'SELECT pickup, pickup_number FROM %i WHERE id_order = %d',
					$table_coo,
					$order_id
				), ARRAY_A);

				$client = $this->utilitiesDAO->getDataClient('CEX', false, $id_sender);

				if ($type == 'order' && isset($hasPickup[0]['pickup_number']) && (int) $hasPickup[0]['pickup_number'] === 1) {

					$pickupDatas = array(
						'id_order' => $order_id,
						'codSolicitud' => $hasPickup[0]['pickup_number'],
						'client' => $client,
					);
					$result_operation = $this->cex_rest->cancelarRecogida($pickupDatas, $id_sender);
					$returnPickupCancelled = true;
				} elseif ($type == 'return' && $pickup_number_return) {
					$pickupReturnDatas = array(
						'id_order' => $order_id,
						'codSolicitud' => $pickup_number_return,
						'client' => $client,
					);
					$result_operation = $this->cex_rest->cancelarRecogida($pickupReturnDatas, $id_sender);
					$this->utilitiesDAO->cancelReturnPickup($order_id);
					$returnPickupCancelled = true;
				} else {
					//Su solicitud de envio ha sido cancelada (CEX)
					$result_operation = array(
						'codigoRetorno' => 0,
						'changeStatus' => $this->getStatus('ShipmentCanceled'),
						'status_code' => 200,
					);
				}

				if ($result_operation['codigoRetorno'] == 0) {

					$result_operation['mensajeRetorno'] = __('The shipment cancel request has been succeded (CEX)', 'correosoficial');

					if ($returnPickupCancelled) {
						$result_operation['mensajeRetorno'] = __('The pickup and shipment cancel request has been succeded (CEX)', 'correosoficial');
					}
				}

				//Su solicitud de envio ha sido cancelada (CEX)
				$result_operation['changeStatus'] = $this->getStatus('ShipmentCanceled');
				$cancelOrderSucceeded = true;
				break;
			default:
				throw new LogicException('ERROR 19012: Devolución no cancelable. Debe ser un producto de Correos o de CEX');
		}

		if ($result_operation['codigoRetorno'] == 0 || ( $result_operation['codigoRetorno'] == 1 && $result_operation['codigoError'] == 67 )) {
			$this->utilitiesDAO->cancelOrder($expedition_number);
			$this->utilitiesDAO->deleteReturns($order_id);

			if ($cancelOrderSucceeded && $this->utilitiesDAO->readSettings('ShowShippingStatusProcess')->value == 'on') {
				$config_status = $this->utilitiesDAO->readSettings('ShipmentCanceled');
				CorreosOficialUtils::changeOrderStatus($order_id, $config_status->value);
			}
		}

		return $result_operation;
	}

	public function generatePickup() {

		$order_id = Normalization::normalizeData('order_id');
		$company = Normalization::normalizeData('company');
		$expedition_number = Normalization::normalizeData('expedition_number');
		$id_sender = Normalization::normalizeData('id_sender');

		$client = $this->utilitiesDAO->getDataClient($company, false, $id_sender);

		$mode_pickup = sanitize_text_field(isset($_REQUEST['mode_pickup']) ? $_REQUEST['mode_pickup'] : '');

		$shipping_numbers = array();
		$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByExp($expedition_number, $mode_pickup);

		$customer_email = Normalization::normalizeData('sender_email', 'email');
		$default_sender_email = Normalization::normalizeData('default_sender_email', 'email');

		$customer_cp = Normalization::normalizeData('customer_cp');
		$customer_country = Normalization::normalizeData('customer_country');
		$sender_cp = Normalization::normalizeData('sender_cp');
		$sender_country = Normalization::normalizeData('sender_country');

		$rand = Normalization::normalizeData('rand');

		$pickup_data = array(
			'id_order' => $order_id,
			'bultos' => Normalization::normalizeData('bultos'),
			'order_reference' => Normalization::normalizeData('order_reference'),
			'pickup_date' => Normalization::normalizeData('pickup_date'),
			'sender_from_time' => Normalization::normalizeData('sender_from_time'),
			'sender_to_time' => Normalization::normalizeData('sender_to_time'),
			'sender_address' => Normalization::normalizeData('sender_address'),
			'sender_city' => Normalization::normalizeData('sender_city'),
			'sender_cp' => Normalization::normalizeData('sender_cp'),
			'sender_name' => Normalization::normalizeData('sender_name'),
			'sender_contact' => Normalization::normalizeData('sender_contact'),
			'sender_phone' => str_replace(' ', '', Normalization::normalizeData('sender_phone')),
			'sender_email' => $customer_email,
			'sender_nif_cif' => Normalization::normalizeData('sender_nif_cif'),
			'sender_country' => Normalization::normalizeData('sender_country'),
			'producto' => Normalization::normalizeData('producto'),
			'print_label' => Normalization::normalizeData('print_label') == 0 ? 'N' : 'S',
			'package_type' => Normalization::normalizeData('package_type'),
			'shipping_numbers' => $shipping_numbers,
			'client' => $client,
		);

		switch ($company) {
			case 'Correos':
				$result_operation = $this->correos_soap->registrarRecogida($pickup_data, $id_sender);
				$result_pickup = array(
				'codSolicitud' => mb_convert_encoding($result_operation['codSolicitud'], 'UTF-8'),
				'codigoRetorno' => $result_operation['codigoRetorno'],
				'mensajeRetorno' => mb_convert_encoding($result_operation['mensajeRetorno'], 'UTF-8'),
				);

				if ($result_operation['codigoRetorno'] == 0) {
					$pickup_from_hour = $pickup_data['sender_from_time'];
					$pickup_to_hour = $pickup_data['sender_to_time'];
					$pickup_order_data = array(
					'id_order' => $order_id,
					'pickup_number' => mb_convert_encoding($result_operation['codSolicitud'], 'UTF-8'),
					'pickup_date' => $pickup_data['pickup_date'],
					'pickup_from_hour' => $pickup_from_hour,
					'pickup_to_hour' => $pickup_to_hour,
					'package_size' => intval($pickup_data['package_type']),
					'print_label' => $pickup_data['print_label'],
					'pickup_status' => 'Grabado',
					);
					if ($mode_pickup == 'pickup') {
						$this->utilitiesDAO->savePickup($pickup_order_data);
					} else if ($mode_pickup == 'return') {
						$this->utilitiesDAO->saveReturnPickup($pickup_order_data);
					}
				}
				break;
			case 'CEX':
				$result_operation = $this->cex_rest->registrarRecogida($pickup_data, $id_sender);
				$result_cex_decoded = json_decode($result_operation['json_retorno'], true);

				$result_pickup = array(
				'codSolicitud' => $result_cex_decoded['numRecogida'],
				'codigoRetorno' => $result_operation['codigoRetorno'],
				'mensajeRetorno' => $result_operation['mensajeRetorno'],
				);

				if ($result_operation['resultado'] == 1 && $result_operation['codigoRetorno'] == 0 && $result_operation['mensajeRetorno'] != null) {

					$string_date = $result_operation['mensajeRetorno'];
					preg_match('/ fechaRecogida: (.*?),/is', $string_date, $pickup_date);
					$aux_date = date_create_from_format('dmY', $pickup_date[1]);
					$pickup_date_format = date_format($aux_date, 'Y-m-d');

					preg_match('/ horaDesde1: (.*?),/is', $string_date, $pickup_from_hour);
					preg_match('/ horaHasta1: (.*?)$/', $string_date, $pickup_to_hour);

					$pickup_order_data = array(
					'id_order' => $order_id,
					'pickup_number' => $result_cex_decoded['numRecogida'],
					'pickup_date' => $pickup_date_format,
					'pickup_from_hour' => $pickup_from_hour[1],
					'pickup_to_hour' => $pickup_to_hour[1],
					'package_size' => 0,
					'print_label' => 'N',
					'pickup_status' => 'Grabado',
					);
					if ($mode_pickup == 'pickup') {
						$this->utilitiesDAO->savePickup($pickup_order_data);
					} else if ($mode_pickup == 'return') {
						$this->utilitiesDAO->saveReturnPickup($pickup_order_data);
					}
				}
				break;
		}

		return $result_pickup;
	}

	public function getCN23ToEmail( $order_id, $iso_code ) {
		/* Se consigue ruta del CN23 */
		$json = file_get_contents(plugins_url('correosoficial') . '/dispatcher.php?controller=CorreosOficialAdminOrderModuleFrontController&ajax=true&action=getCustomsDoc&exp_number=' . $order_id . '&type=return&customer_country=' . $iso_code . '&optionButton=ImprimirCN23Button2');
		$result = json_decode($json);

		if (empty($result->errors)) { // phpcs:ignore
			$filename = $result->files[0];
			$path = WP_CONTENT_DIR . '/plugins/correosoficial/pdftmp/' . $filename;

			/**
			 * 
			 * Lectura del fichero de CN23 de devolución 
			 */
			$handle = fopen($path, 'rb');
			$contents = fread($handle, filesize($path));
			fclose($handle);

			// CN23 codificado en base64
			return base64_encode($contents);
		}
		return null;
	}

	public function cancelPickup() {
		$order_id = Normalization::normalizeData('order_id');
		$id_carrier = Normalization::normalizeData('id_carrier');
		$company = Normalization::normalizeData('company');
		$id_sender = Normalization::normalizeData('id_sender');

		$client = $this->utilitiesDAO->getDataClient($company, false, $id_sender);
		$mode_pickup = sanitize_text_field(isset($_REQUEST['mode_pickup']) ? $_REQUEST['mode_pickup'] : '');

		$pickup_data = array(
			'id_order' => $order_id,
			'codSolicitud' => Normalization::normalizeData('codSolicitud'),
			'client' => $client,
		);

		switch ($company) {
			case 'Correos':
				$result_operation = $this->correos_soap->cancelarRecogida($pickup_data);
				$result_operation['codigoRetorno'] = mb_convert_encoding($result_operation['codigoRetorno'], 'UTF-8');
				break;
			case 'CEX':
				$result_operation = $this->cex_rest->cancelarRecogida($pickup_data);
				break;
		}

		if ($result_operation['codigoRetorno'] == 0) {
			if ($mode_pickup == 'pickup') {
				$this->utilitiesDAO->cancelPickup($order_id);
			} else if ($mode_pickup == 'return') {
				$this->utilitiesDAO->cancelReturnPickup($order_id);
			}
		}

		return $result_operation;
	}

	//Obtiene etiquetas
	public function getEtiquetasByExpNumber( $send_email, $company, $type, $shipping_numbers, $exp_number, $labelType, $labelPosition, $labelFormat = 0 ) {
		$pdf = new CorreosOficial\PDFMerger($labelType, $labelFormat);
		$tempFolder = get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp';
		$pdfOutputFile = $tempFolder . '/' . uniqid('labels_') . '.pdf';
		$labels = array();
		$logoBase64 = '';
		
		$useUserLogo = CorreosOficialConfigDao::getConfigValue('ChangeLogoOnLabel');
		$getUserLogo = CorreosOficialConfigDao::getConfigValue('UploadLogoLabels');
		
		if ($useUserLogo == 'on') {
			$imagedata = file_get_contents($getUserLogo);
			$logoBase64 = base64_encode($imagedata);
		}

		// Acciones según formato
		switch ($labelFormat) {
			case LABEL_FORMAT_3A4: // 3/A4
				$labelsResponse = $this->cex_rest->getLabelFromWS($exp_number, '3', $logoBase64, $labelPosition, $type);
				$labels = $labelsResponse->listaEtiquetas;
				break;
			case LABEL_FORMAT_4A4: // 4/A4
				break;
			default:
				if ($company == 'CEX') {
					if ($type == 'return' || empty($labelPosition)) {
						$labelPosition = 0;
					}
					$labelsResponse = $this->cex_rest->getLabelFromWS($exp_number, '1', $logoBase64, $labelPosition, $type);
					$labels = $labelsResponse->listaEtiquetas;
				} elseif ($company == 'Correos') {
					$i = 0;
					// sin formato o estandar
					foreach ($shipping_numbers as $shippingNumber) {
						if ($type == 'order') {
							$label = $this->correos_soap->SolicitudEtiquetaOp($shippingNumber);
						} elseif ($type == 'return') {
							$label = $this->correos_soap->SolicitudEtiquetaOp($shippingNumber);
						} else {
							throw new LogicException('ERROR 19011: El tipo debe ser order o return');
						}
						if ($label !== null) {
							$labels[] = $label; // Agrega la etiqueta al array de etiquetas
						}
					}
				}
				break;
		}

		// Generación PDFs temporales
		for ($i = 0; $i < count($labels); $i++) {
			$tempPathPDF = $tempFolder . '/E_' . $i . '_' . $shipping_numbers[$i] . '.pdf';
			file_put_contents($tempPathPDF, base64_decode($labels[$i]));
			$pdf->addPDF($tempPathPDF, 'all');
		}

		$labels = array();

		// Opciones de mergeo
		if (
			$labelType == LABEL_TYPE_THERMAL
			|| $labelFormat == LABEL_FORMAT_3A4
			|| $labelFormat == LABEL_FORMAT_4A4
		) {
			$pdf->mergeTopages(
				'file',
				$pdfOutputFile
			);
		} else { // Adhesivas
			$pdf->merge(
				'file',
				$pdfOutputFile,
				$labelType,
				$labelPosition
			);
		}

		if ($send_email) {
			$labels[] = base64_encode(file_get_contents($pdfOutputFile));
			return $labels;
		}
		
		$mensajeRetorno = array(
			'codigoRetorno' => 0,
			'filePath' => $pdfOutputFile,
		);

		wp_die(json_encode($mensajeRetorno));
	}

	public function mergeArraysIntoOne( $shipping_numbers ) {
		$clean_shipping_numbers = array();
		foreach ($shipping_numbers as $shipping_number) {
			$clean_shipping_numbers[] = $shipping_number['shipping_number'];
		}

		return $clean_shipping_numbers;
	}

	public function getDocAduanera( $type, $exp_number, $optionButton, $customer_country, $customer_name ) {
		$files = array();
		$errors = array();

		if ($type == 'order') {
			$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByExp($exp_number);
		} else {
			$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByIdOrderForReturns($exp_number);
		}

		// DCAF y DDP solo se imprime una vez
		$print = 0;

		foreach ($shipping_numbers as $shipping_number => $field) {
			$result_doc_aduanera = $this->correos_soap->documentacionAduaneraOp($optionButton, $field['shipping_number'], $customer_country, $customer_name);

			if ($result_doc_aduanera['codigoRetorno'] == '0') {
				switch ($optionButton) {
					case 'ImprimirCN23Button':
						$prefijo_archivo = 'CN23';
						$fichero = $result_doc_aduanera['xml_retorno']->soapenvBody->RespuestaSolicitudDocumentacionAduaneraCN23CP71->Fichero;
						break;
					case 'ImprimirDUAButton':
						$prefijo_archivo = 'DCAF';
						$fichero = $result_doc_aduanera['xml_retorno']->soapenvBody->RespuestaSolicitudDocumentacionAduanera->Fichero;
						$print++;
						break;
					case 'ImprimirDDPButton':
						$prefijo_archivo = 'DDP';
						$fichero = $result_doc_aduanera['xml_retorno']->soapenvBody->RespuestaSolicitudDocumentacionAduanera->Fichero;
						$print++;
						break;
					default:
						throw new Exception('ERROR 19020: El tipo debe ser ImprimirCN23Button, ImprimirDUAButton o ImprimirDDPButton');
						break;
				}
				if ($optionButton == 'ImprimirCN23Button' || ( $print == 1 )) {
					$pdf = file_put_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp/' . $prefijo_archivo . '_' . $field['shipping_number'] . '.pdf', base64_decode($fichero));
					$files[] = $prefijo_archivo . '_' . $field['shipping_number'] . '.pdf';
				}
			} else {
				$errors[] = array( 'error_msg' => mb_convert_encoding($result_doc_aduanera['mensajeRetorno'], 'UTF-8') );
			}
		}

		$result = array(
			'errors' => $errors,
			'files' => $files,
		);

		die(json_encode($result));
	}

	public function getOrderStatus( $order_id ) {
		$correos_order = CorreosOficialOrders::getCorreosOrder($order_id);

		if (!isset($correos_order['shipping_number'])) {
			return false;
		}

		$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByExp($correos_order['shipping_number']);

		$last_status[] = array(
			'codEnvio' => '',
			'codProducto' => '',
			'desTextoResumen' => 'En espera de datos',
			'fecEvento' => '',
			'horEvento' => '',
			'unidad' => '',
		);

		foreach ($shipping_numbers as $bulto => $field) {
			if ($correos_order['carrier_type'] == 'Correos') {
				$package_status = $this->correos_rest->getOrderStatus($field['shipping_number'], true);

				if ($package_status != null) {

					$i = 0;

					if ($package_status[0]->eventos == null) {
						return $last_status;
					}
					foreach ($package_status[0]->eventos as $evento) {

						if ($evento->desTextoResumen == null) {
							continue;
						}

						$last_status[$i] = array(
							'codEnvio' => $package_status[0]->codEnvio,
							'codProducto' => $correos_order['name'],
							'desTextoResumen' => $evento->desTextoResumen,
							'fecEvento' => $evento->fecEvento,
							'horEvento' => $evento->horEvento,
							'unidad' => isset($evento->unidad) ? $evento->unidad : '',
						);
						$i++;
					}
				}
			} elseif (( $correos_order['carrier_type'] == 'CEX' )) {
				$cex_count = 0;
				$package_status = $this->cex_rest->TrackingCEXK8s($field['shipping_number'], false);

				if ($package_status) {
					$i = 0;
					foreach ($package_status->estadoEnvios as $package_status_cex) {
						$cex_hour[0] = substr($package_status_cex->horaEstado, 0, 2);
						$cex_hour[1] = substr($package_status_cex->horaEstado, 2, 2);
						$cex_hour[2] = substr($package_status_cex->horaEstado, 4, 2);

						$last_status[$i] = array(
							'codEnvio' => $package_status->bultoSeguimiento[$cex_count]->codUnico,
							'codProducto' => $correos_order['name'],
							'desTextoResumen' => $package_status_cex->descEstado,
							'fecEvento' => $package_status_cex->fechaEstado,
							'horEvento' => $cex_hour[0] . ':' . $cex_hour[1] . ':' . $cex_hour[2],
							'unidad' => '',
						);
						$i++;
					}
				}
				$cex_count++;
			}
		}

		return $last_status;
	}

	public function getSGAOrderStatus( $idOrder ) {
		$order = wc_get_order($idOrder);

		// Inicializa el array de estado
		$last_status = array();

		if ($order) {
			$trackingNumber = get_post_meta($idOrder, 'correosecom_sga_tracking_number', true);
			$shippingMethods = $order->get_shipping_methods();

			if (empty($shippingMethods)) {
				return $last_status;
			}

			foreach ($shippingMethods as $method) {
				$instanceId = $method->get_instance_id();
			}

			$product = $this->utilitiesDAO->readRecord('correos_oficial_carriers_products', 'where id_carrier=' . $instanceId , '', true);
			$carrier = $this->utilitiesDAO->readRecord('correos_oficial_products', 'where id=' . $product[0]['id_product'], '', true);

			if ($trackingNumber) {

				if ($carrier[0]['company'] == 'Correos') {
					$orderUpdatedStatus = $this->correos_rest->getOrderStatus($trackingNumber, true);

					if ($orderUpdatedStatus == null) {
						return $last_status;
					}

					foreach ($orderUpdatedStatus as $status) {
						foreach ($status->eventos as $event) {
							$last_status[] = array(
								'codEnvio' => $status->codEnvio,
								'codProducto' => $carrier[0]['name'],
								'desTextoResumen' => $event->desTextoResumen,
								'fecEvento' => $event->fecEvento,
								'horEvento' => $event->horEvento,
								'unidad' => isset($event->unidad) ? $event->unidad : '',
							);
						}
					}

				} else if ($carrier[0]['company'] == 'CEX') {
					$orderUpdatedStatus = $this->cex_rest->TrackingCEXK8s($trackingNumber, false);

					if ($orderUpdatedStatus == null) {
						return $last_status;
					}

					foreach ($orderUpdatedStatus->estadoEnvios as $status) {
						$cex_hour[0] = substr($status->horaEstado, 0, 2);
						$cex_hour[1] = substr($status->horaEstado, 2, 2);
						$cex_hour[2] = substr($status->horaEstado, 4, 2);
	
						$last_status[] = array(
							'codEnvio' => $trackingNumber,
							'codProducto' => $carrier[0]['name'],
							'desTextoResumen' => $status->descEstado,
							'fecEvento' => $status->fechaEstado,
							'horEvento' => $cex_hour[0] . ':' . $cex_hour[1] . ':' . $cex_hour[2],
							'unidad' => '',
						);
					}
				}
			} 

			// Si no se encontraron eventos, agrega el estado "En espera de datos"
			if (empty($last_status)) {
				$last_status[] = array(
					'codEnvio' => '',
					'codProducto' => '',
					'desTextoResumen' => 'En espera de datos',
					'fecEvento' => '',
					'horEvento' => '',
					'unidad' => '',
				);
			}
	
			return $last_status;
		}
	}

	public function sendEmail() {
		$order_id = Normalization::normalizeData('order_id');
		$company = Normalization::normalizeData('company');
		$expedition_number = Normalization::normalizeData('expedition_number');

		$shipping_numbers = $this->utilitiesDAO->getShippingNumbersByExp($expedition_number, 'return');

		$customer_email = Normalization::normalizeData('customer_email', 'email');
		$default_sender_email = Normalization::normalizeData('default_sender_email', 'email');

		$customer_cp = Normalization::normalizeData('customer_cp');
		$customer_country = Normalization::normalizeData('customer_country');
		$sender_cp = Normalization::normalizeData('sender_cp');
		$sender_country = Normalization::normalizeData('sender_country');
		$pickup_date = Normalization::normalizeData('pickup_date');
		$sender_from_time = Normalization::normalizeData('sender_from_time');

		$returns_code = array();

		for ($i = 1; $i < 11; $i++) {
			$returns_code[] = Normalization::normalizeData('return_code_' . $i);
		}

		$result_pickup = array();

		$exp_number = $this->utilitiesDAO->getExpeditionNumberByIdOrderForReturn($order_id);
				$ws_shipping_numbers = array();
				$ws_shipping_numbers = $this->utilitiesDAO->getShippingNumbersByIdOrderForReturns($order_id);
				$ws_shipping_numbers = $this->mergeArraysIntoOne($ws_shipping_numbers);

		$label = $this->getEtiquetasByExpNumber(true, $company, 'return', $ws_shipping_numbers, $exp_number, '2', '', $label_format = 0);

		$cp_source = $sender_cp;
		$country_source = $sender_country;

		$cp_dest = $customer_cp;
		$country_dest = $customer_country;

		$require_customs_doc = NeedCustoms::isCustomsRequired($cp_source, $cp_dest, $country_source, $country_dest);

		if ($require_customs_doc) {
			$cn23 = $this->getCN23ToEmail($order_id, $sender_country);
		} else {
			$cn23 = null;
		}

		$label_shipings_numbers = '';

		$returns_code_string = '';
		foreach ($returns_code as $return_code) {
			if (!empty($return_code)) {
				$returns_code_string .= $return_code . '<br />';
				$label_shipings_numbers .= $return_code . '_';
			}
		}

		if (count($ws_shipping_numbers) == 1 && $returns_code_string == '' ) {
			$label_shipings_numbers .= $ws_shipping_numbers[0];
		}

		$label_shipings_numbers = trim($label_shipings_numbers, '_');

		$returns_data = array(
			'customer_email' => $customer_email,
			'sender_email' => $default_sender_email,
			'label' => $label,
			'cn23' => $cn23,
			'company' => $company,
			'shipping_number' => $label_shipings_numbers,
			'pickup_date' => $pickup_date,
			'sender_from_time' => $sender_from_time,
			'return_code' => $returns_code_string,
			'order_id' => $order_id,
			'shop_name' => get_bloginfo('name'),
		);

		// Envío de email
		$email = new CorreosOficialReturnsMail($returns_data);

		$result_email = $email->sendEmail();

		if ($result_email == 'Enviado') {
			$result_pickup['mensajeRetorno'] = __('An email was sended to the customer with details of the return', 'correosoficial');
			$result_pickup['codigoRetorno'] = 0;
		} else {
			$result_pickup['mensajeRetorno'] = $result_email . '. ' . __('Can not send returns email to your customer. Please, print the label and CN23 documents and send an email to your customer', 'correosoficial');
		}
		CorreosOficialUtils::deleteFiles();

		return $result_pickup;
	}

	public static function getCustomsDesc( $bultos ) {

		$customs_desc_array = array();
		$returned_customs_desc_array = array();
		$units = array( ' €', ' Kg', ' Unid.' );

		if (isset($_POST['_nonce'])) {
			$nonce = sanitize_text_field($_POST['_nonce']);
			if (!wp_verify_nonce(wp_unslash($nonce), 'correosoficial_nonce')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wp_send_json_error( 'bad_nonce' );
				wp_die();
			}
		}

		for ($i = 1; $i <= $bultos; $i++) {
			$n = 0;

			if (!isset($_POST['dispatcher']['order_form'][$i])) {
				return;
			}
			$formData = array_map('sanitize_text_field', (array) $_POST['dispatcher']['order_form'][$i]);
			
			foreach ($formData as $customs_desc) {

				$customs_desc = str_replace($units, '', $customs_desc);
				$customs_desc = rtrim($customs_desc, ' • ');
				$customs_desc_array[$i][$n + 1] = $customs_desc;
				$n++;
			}

			foreach ($customs_desc_array as $customs_desc) {
				$h = 0;

				foreach ($customs_desc as $cd) {

					// Informamos solo las descripciones necesarias.
					if ($h < count($customs_desc_array[$i])) {

						$elements = explode(' • ', $cd);

						$len_ntarifario = strlen($elements[0]);

						if ($len_ntarifario == 6 || $len_ntarifario == 8 || $len_ntarifario == 10) {
							$returned_customs_desc_array[$i][$h]['numero_tarifario'] = $elements[0];
							$returned_customs_desc_array[$i][$h]['descripcion_aduanera'] = $elements[1];
						} else {
							$returned_customs_desc_array[$i][$h]['numero_tarifario'] = '';
							$returned_customs_desc_array[$i][$h]['descripcion_aduanera'] = $elements[0];
						}

						$returned_customs_desc_array[$i][$h]['valor_neto'] = $elements[2] * 100;
						$returned_customs_desc_array[$i][$h]['weight'] = $elements[3] * 1000;
						$returned_customs_desc_array[$i][$h]['unidades'] = $elements[4];
						$h++;
					}
				}
			}
		}

		return $returned_customs_desc_array;
	}

	public function getStatus( $search ) {
		if ($this->statusProcessActive == 'on') {
			return $this->utilitiesDAO->readSettings($search)->value;
		}
		return false;
	}

		/**
	 * Inserta los datos de Oficina o CityPaq del pedido en correos_oficial_requests
	 * int $order_id Id del pedido
	 * return void
	 */
	private function insertRequestData( $order_id ) {


		if (!$order_id) {
			throw new LogicException('ERROR 19030: Error al insertar datos de Oficina/CityPaq. Debe existir un número de pedido $order_id');
		}

		$order_form = Normalization::normalizeData('order_form');
		$request_data = Normalization::normalizeData('request_data');

		$order = wc_get_order($order_id);
		$id_cart = $order->get_cart_hash();

		$reference_code = $order_form['reference_code'];
		
		if (!$reference_code && empty($request_data)) {
			return false;
		}

		// Comprobamos que el array de data es válido y si sí convertimos a json
		if (!empty($request_data)) {
			$data = json_encode($request_data);
		} else {
			throw new LogicException('ERROR 19031: Error al insertar datos de Oficina/CityPaq. El json introducido no es válido');
		}

		try {
			CorreosOficialCheckout::insertReferenceCode($order_id, $id_cart, $reference_code, $data);
		} catch (Exception $e) {
			throw new LogicException('ERROR 19032: Error al insertar datos de Oficina/CityPaq.');
		}
	}
}
