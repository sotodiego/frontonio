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

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

require_once 'CorreosOficialOrders.php';
require_once 'CorreosOficialOrder.php';
require_once 'CorreosOficialCarrier.php';
require_once 'CorreosOficialOrdersWC.php';
require_once 'CorreosOficialCountriesWC.php';
require_once 'CorreosOficialZonesWC.php';
require_once 'CorreosOficialCustomerWC.php';
require_once 'CorreosOficialAddressWC.php';
require_once 'CorreosOficialSenders.php';

require_once __DIR__ . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialOrderDao.php';

require_once __DIR__ . '/../vendor/ecommerce_common_lib/Correos/CorreosRest.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/Correos/CorreosSoap.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/Cex/CexRest.php';

require_once __DIR__ . '/../vendor/ecommerce_common_lib/Commons/Normalization.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/CorreosOficialUtils.php';
use CorreosOficialCommonLib\Commons\CorreosOficialCrypto;

class CorreosOficialAdminOrderHook {


	private $smarty;
	private $plugin_dir;

	private $correos_rest;
	private $correos_soap;
	private $cex_rest;

	public function __construct( $smarty, $plugin_dir ) {
		
		$this->plugin_dir = $plugin_dir;
		$this->correos_rest = new CorreosRest();
		$this->correos_soap = new CorreosSoap();
		$this->cex_rest = new CexRest();
		$this->smarty = $smarty;
	
		if (!CorreosOficialUtils::sislogModuleIsActive()) {
			$this->hookDisplayAdminOrder();
		} else if (CorreosOficialUtils::sislogModuleIsActive()) {
			$this->correosecomsgaOrderTracking();
		}
	}

	/**
	 * Check if HPOS enabled.
	 */
	public function is_wc_order_hpos_enabled() {
		return function_exists( 'wc_get_container' ) ?
				wc_get_container()
				->get( CustomOrdersTableController::class )
				->custom_orders_table_usage_is_enabled()
			: false;
	}

	public function hookDisplayAdminOrder() {
		global $co_module_url_wc;
		global $post;
		global $woocommerce;

		$client_data = array();
		$carriers = array();

		$array_packages_order = array();
		$array_packages_return = array();

		$saved_return_pickup = array();

		$pickup_return_data_response = array();
		$pickup_return_cancelable = '';

		$return_status = '';

		$cod_office = '';
		$cod_homepaq = '';

		// Init Modal para remitentes
		$showSenderModal = false;
		$errorSenderName = '';
		$errorCompanyName = '';

		// Multicliente (Se tendría que implementar un método que devuelva contratos activos)
		// $customer_dao = new CorreosOficialActiveCustomersDao();
		// $active_client = $customer_dao->getActivesCustomers();
		$active_client = 'both'; // Forzado

		$is_international = '';
		$require_customs_doc = '';

		$order_returnable = '';
		$is_code_at = false;

		$shipping_method_data = array();
		$id_zone = null;

		// Order
		if ($this->is_wc_order_hpos_enabled()) {
			$id_order = Normalization::normalizeData('id');
		} else {
			$id_order = $post->ID;
		}

		$order = new WC_Order($id_order);
		$order_reference = str_replace('wc_order_', '', $order->get_order_key());

		$order_number = $order->get_order_number();

		$correos_order = CorreosOficialOrders::getCorreosOrder($order->get_id());
		$correos_return = CorreosOficialOrders::getCorreosReturn($order->get_id());
		$correos_pickup_return = CorreosOficialOrders::getCorreosPickupReturn($order->get_id());

		// Comprobamos Cash on delivery
		if ($order->get_payment_method() == 'cod') {
			$cash_on_delivery = true;
		} else {
			$cash_on_delivery = false;
		}
		$cash_on_delivery_value = number_format($order->get_total(), 2);

		$NifFieldRadio = CorreosOficialConfigDao::getConfigValue('NifFieldRadio');

		if ($NifFieldRadio && $NifFieldRadio == 'PERSONALIZED') {
			$NifFieldValue = CorreosOficialConfigDao::getConfigValue('NifFieldPersonalizedValue');
		} else {
			$NifFieldValue = 'NIF';
		}

		$customer = new CorreosOficialCustomerWC($order);
		$address = new CorreosOficialAddressWC($order, $NifFieldValue);
		$countries = CorreosOficialCountriesWC::getCountries();

		$shipping_methods = $order->get_shipping_methods();

		foreach ($shipping_methods as $shipping_method) {
			$shipping_method_data = $shipping_method->get_data();
		}

		if (count($shipping_method_data) > 1) {
			// $order->id_carrier = $shipping_method_data['instance_id'];
			$order_id_carrier = $shipping_method_data['instance_id'];
		} else { // Transportista aún no seleccionado (ejemplo un pedido hecho desde Woocommerce->Pedidos)
			// $order->id_carrier = '';
			$order_id_carrier = '';
		}
		
		$id_zone = CorreosOficialCarrier::getCarrierZone(
			isset($shipping_method_data['instance_id']) ? $shipping_method_data['instance_id'] : '');

		//$carrier_order = $this->getCarrierOrder($id_zone, $order_id_carrier, $correos_order);

		// Borrar multicliente
		// Seleccionamos carriers según usuario (Correos, Cex, All)
		// if ($active_client != 'none') {
		//  $carriers = CorreosOficialCarrier::getCarriersByCompanyInOrder($active_client, $id_zone);
		// }

		$carriers = CorreosOficialCarrier::getCarriersByCompanyInOrder($active_client, $id_zone);

		// Remitente por defecto
		$default_sender = CorreosOficialSendersDao::getDefaultSender();
		
		// Sobreescribimos con correos_code y cex_code
		if ($default_sender) {
			$default_sender = CorreosOficialSenders::getSenderById($default_sender['id']);
		}

		// Si el pedido está preregistrado obtenemos información guardada
		if ($correos_order) {
			$default_sender = CorreosOficialSendersDao::getSenderById($correos_order['id_sender']);
			$carrier_order = CorreosOficialCarrier::getSavedOrderProduct($id_order);
		}

		// Contrato según remitente por defecto y producto
		if (isset($carrier_order) && $default_sender) {
			$client_data = CorreosOficialSendersDao::getCodeBySenderAndCompany($default_sender['id'], strtolower($carrier_order['company']));
		} else {
			$carrier_order = $this->getCarrierOrder($id_zone, $order_id_carrier, $correos_order);
		}

		// Senders
		$senders = CorreosOficialSendersDao::getSenders();

		// Client code actual si existe relación
		$client_code = isset($client_data['customer_code']) ? $client_data['customer_code'] : '';

		// Alerta Modal sobre Remitentes
		if (empty($senders) || empty($default_sender)) {
			$showSenderModal = true;
		} else {
			
			// Si está preregistrado
			if ($correos_order) {
				$order_company = $correos_order['carrier_type'];
			} else {
				$order_company = CorreosOficialCarrier::getCompanyByOrder($id_order, $id_zone);
			}
			
			if (
				( $order_company == 'Correos' && !$default_sender['correos_code'] ) ||
				( $order_company == 'CEX' && !$default_sender['cex_code'] )
			) {
				$errorSenderName = $default_sender['sender_name'];
				$errorCompanyName = $order_company;
				$showSenderModal = true;
			}
		}

		$delivered = false;

		// Comprobamos si está preregistrado y si tiene recogida grabada
		if (empty($correos_order)) {

			$order_done = false;
			$cancelable = true;
			$pickup = 0;
			$pickup_cancelable = false;
			$pickup_data_response = self::getPickUpDataResponse('Estado 1');

		} elseif ($correos_order['shipping_number'] != '') {

			$order_done = true;

			// Comprobamos bultos para traer información de cada bulto
			$array_packages_order = CorreosOficialOrders::getCorreosPackages($order->get_id(), $correos_order['shipping_number']);
			$company = sanitize_text_field(isset($correos_order['company']) ? $correos_order['company'] : '');

			if ($correos_order['pickup'] == 1) {
				$pickup = 1;
				if ($company == 'Correos') {
					$pickup_data = array(
						'CodigoSRE' => $correos_order['pickup_number'],
						'CorreosContract' => $client_data['CorreosContract'],
						'CorreosCustomer' => $client_data['CorreosCustomer'],
						'CorreosOv2Code' => $client_data['CorreosOv2Code'],
						'ModoOperacion' => '1', // Info + Todos los estados
					);
					$pickup_status = $this->correos_soap->ConsultaSRE($pickup_data);
					if ($pickup_status['xml_retorno'] != null) {
						$array_status = $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->TrazasSolicitudRecogidaEsporadica;

						if ($array_status == null) {
							$pickup_data_response = self::getPickUpDataResponse($pickup_status['mensajeRetorno'], '5', 'Sin datos');
						} else {

							$pickup_last_status = self::returnPickupLastStatus($array_status);

							$pickup_data_response = array(
								'codEstado' => $pickup_last_status->codEstado,
								'status' => $pickup_last_status->desTextoResumen,
								'pickup_reference' => $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3CodigoSolicitudRecogidaEsporadica->ReferenciaRecogida,
								'pickup_date' => str_replace('00:00:00.0', '', $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->FecRecogida),
								'pickup_from_hour' =>gmdate('H:i', strtotime($correos_order['pickup_from_hour'])),
								'pickup_to_hour' =>gmdate('H:i', strtotime($correos_order['pickup_to_hour'])),
								'pickup_address' => $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->NomNombreViaRec,
								'pickup_city' => $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->NomLocalidadRec,
								'pickup_cp' => $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->CodigoPostalRecogida,
							);
						}
					} else {
						$pickup_data_response = self::getPickUpDataResponse('En espera de datos', '3', 'Sin datos');
					}
				} elseif ($company == 'CEX') {
					$pickup_data = array(
						'recogida' => $correos_order['pickup_number'],
						'codigoCliente' => $client_data['CEXCustomer'],
						'fecRecogida' => '',
						'idioma' => 'ES',
					);
					$pickup_status = $this->cex_rest->consultarRecogida($pickup_data, $default_sender['id']);
					$pickup_data_cex = json_decode($pickup_status['json_retorno']);
					$pickup_data_response = array(
						'status' => $pickup_data_cex->situaciones[0]->descSituacion,
						'pickup_reference' => $pickup_data_cex->referencia,
						'pickup_date' => $pickup_data_cex->fecRecogida,
						'pickup_from_hour' =>gmdate('H:i', strtotime($correos_order['pickup_from_hour'])),
						'pickup_to_hour' =>gmdate('H:i', strtotime($correos_order['pickup_to_hour'])),
						'pickup_address' => $pickup_data_cex->domRecogida,
						'pickup_city' => $pickup_data_cex->pobRecogida,
						'pickup_cp' => $pickup_data_cex->codPosRecogida,
					);
				}
				// Comprobamos estado de la recogida
				$pickup_cancelable = true;

				if ($pickup_data_response['status'] != 'RECOGIDA REGISTRADA' 
					&& $pickup_data_response['status'] != 'PDTE ASIGNAR'
				) {
					$pickup_cancelable = false;
				}

				// LA RESPUESTA DE CEX NO TIENE codEstado
				// if ($pickup_data_response['codEstado'] != 'SR-001'  // Recogida solicitada Correos
				//  && $pickup_data_response['codEstado'] != 'SR-003'  // Alta Unidad de recogida Correos
				//  && $pickup_data_response['status'] != 'RECOGIDA REGISTRADA' 
				//  && $pickup_data_response['status'] != 'PDTE ASIGNAR'
				// ) {
				//  $pickup_cancelable = false;
				// }

				if ($pickup_data_response['status'] == 'ANULADA') {
					$pickup = 0;
					$pickup_cancelable = false;
				}
			} else {
				$pickup = 0;
				$pickup_cancelable = false;
				$pickup_data_response = self::getPickUpDataResponse('Estado 2');
			}

			$last_status[] = array(
				'codEnvio' => '',
				'codProducto' => '',
				'desTextoResumen' => 'En espera de datos',
				'fecEvento' => '',
				'horEvento' => '',
				'unidad' => '',
			);

			foreach ($array_packages_order as $bulto) {
				if ($correos_order['carrier_type'] == 'Correos') {
					$package_status = $this->correos_rest->getOrderStatus($bulto['shipping_number'], false, $default_sender['id']);
					if (isset($package_status[0]->eventos)) {
						$i = 0;
						foreach ($package_status[0]->eventos as $evento) {
							if ($evento->desTextoResumen == null) {
								continue;
							}
							$last_status[$i] = array(
								'codEnvio' => $package_status[0]->codEnvio,
								'desTextoResumen' => $evento->desTextoResumen,
								'fecEvento' => $evento->fecEvento,
								'unidad' => '',
							);
							$i++;
						}
					}
				} elseif ($correos_order['carrier_type'] == 'CEX') {
					$package_status = $this->cex_rest->TrackingCEXK8s($bulto['shipping_number'], false);
					$last_status[0] = array(
						'codEnvio' => $package_status->bultoSeguimiento[0]->codUnico,
						'codProducto' => $package_status->producto,
						'desTextoResumen' => $package_status->bultoSeguimiento[0]->descEstado,
						'fecEvento' => $package_status->bultoSeguimiento[0]->fechaEstado,
						'unidad' => '',
					);
				}
			}

			// De inicio ningún en ningún estado se podrá cancelar, hasta comprobar exclusiones.
			$cancelable = false;
			foreach ($last_status as $status_bulto) {

				$statusBultoResumen = $status_bulto['desTextoResumen'];

				// Exclusiones de estados en los que se puede cancelar
				if (
					$statusBultoResumen == 'En espera de datos' ||
					$statusBultoResumen == 'Prerregistrado' ||
					$statusBultoResumen == 'Admisión anulada' ||
					$statusBultoResumen == 'SIN RECEPCION'
				) {
					$cancelable = true;
				}

				// Si está entregado no se podrá cancelar (ya que no está excluido) y marcamos flag delivered
				if (
					$statusBultoResumen == 'Entregado' ||
					$statusBultoResumen == 'ENTREGADO'
				) {
					$delivered = true;
				}
			}

		} else {
			$order_done = false;
			$cancelable = true;
			$pickup = 0;
		}

		// DEVOLUCIONES
		if (empty($correos_return)) {
			$exist_return = false;
			$return_cancelable = true;
			$pickup_return = 0;
		} else {
			$exist_return = true;
			$saved_return = new CorreosOficialOrderDao();
			$saved_return_pickup = $saved_return->getPickupReturn($order->get_id());
			$client_data = CorreosOficialCarrier::getDefaultClientCode($correos_return['carrier_type']);

			if (!empty($saved_return_pickup)) {
				$pickup_return = 1;
				if ($correos_return['carrier_type'] == 'Correos') {
					$pickup_return_data = array(
						'CodigoSRE' => $saved_return_pickup[0]->pickup_number,
						'CorreosContract' => $client_data['CorreosContract'],
						'CorreosCustomer' => $client_data['CorreosCustomer'],
						'CorreosOv2Code' => $client_data['CorreosOv2Code'],
						'ModoOperacion' => '1', // Info + Todos los estados
					);
					$pickup_return_status = $this->correos_soap->ConsultaSRE($pickup_return_data);
					if ($pickup_return_status['xml_retorno'] != null) {
						$array_status = $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->TrazasSolicitudRecogidaEsporadica;

						if ($array_status == null) {
							$pickup_return_data_response = self::getPickUpDataResponse('Sin trazabilidad', '4', 'En espera de datos');
						} else {
							$last_status = self::returnPickupLastStatus($array_status);

							if (count($array_status->ns3TrazaSolicitudRecogidaEsporadica) == 1) {
								$codEstado = $array_status->ns3TrazaSolicitudRecogidaEsporadica->codEstado;
								$status = $array_status->ns3TrazaSolicitudRecogidaEsporadica->desTextoResumen;
								$pickup_date = substr($array_status->ns3TrazaSolicitudRecogidaEsporadica->fecEstado . ' ' . $array_status->ns3TrazaSolicitudRecogidaEsporadica->horEstado, 0, 10);
							} else {
								$codEstado = $last_status->codEstado;
								$status = $last_status->desTextoResumen;
								$pickup_date = substr($last_status->fecEstado . ' ' . $last_status->horEstado, 0, 10);
							}

							$pickup_return_data_response = array(
								'codEstado' => $codEstado,
								'status' => $status,
								'pickup_reference' => $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3CodigoSolicitudRecogidaEsporadica->ReferenciaRecogida,
								'pickup_from_hour' =>gmdate('H:i', strtotime($correos_pickup_return['pickup_from_hour'])),
								'pickup_to_hour' =>gmdate('H:i', strtotime($correos_pickup_return['pickup_to_hour'])),
								'pickup_date' => $pickup_date,
								'pickup_address' => $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->NomNombreViaRec,
								'pickup_city' => $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->NomLocalidadRec,
								'pickup_cp' => $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->CodigoPostalRecogida,
							);

						}

					} else {
						$pickup_return_data_response = self::getPickUpDataResponse('En espera de datos', '3', 'Sin datos');
					}
				} elseif ($correos_return['carrier_type'] == 'CEX') {
					$pickup_return_data = array(
						'recogida' => $saved_return_pickup[0]->pickup_number,
						'codigoCliente' => $client_data['CEXCustomer'],
						'fecRecogida' => '',
						'idioma' => 'ES',
					);
					$pickup_return_status = $this->cex_rest->consultarRecogida($pickup_return_data, $default_sender['id']);
					$pickup_data_cex = json_decode($pickup_return_status['json_retorno']);
					$pickup_return_data_response = array(
						'status' => $pickup_data_cex->situaciones[0]->descSituacion,
						'pickup_reference' => $pickup_data_cex->referencia,
						'pickup_date' => $pickup_data_cex->fecRecogida,
						'pickup_from_hour' =>gmdate('H:i', strtotime($correos_pickup_return['pickup_from_hour'])),
						'pickup_to_hour' =>gmdate('H:i', strtotime($correos_pickup_return['pickup_to_hour'])),
						'pickup_address' => $pickup_data_cex->domRecogida,
						'pickup_city' => $pickup_data_cex->pobRecogida,
						'pickup_cp' => $pickup_data_cex->codPosRecogida,
					);
				}
				// Comprobamos estado de la recogida
				$pickup_return_cancelable = true;
				if ($pickup_return_data_response['status'] != 'RECOGIDA REGISTRADA' 
					&& $pickup_return_data_response['status'] != 'PDTE ASIGNAR'
				) {
					$pickup_return_cancelable = false;
				}
				if ($pickup_return_data_response['status'] == 'ANULADA') {
					$pickup_return = 1;
					$pickup_return_cancelable = false;
				}
			} else {
				$pickup_return = 0;
				$pickup_return_cancelable = false;
				$pickup_return_data_response = self::getPickUpDataResponse('Estado 3');
			}

			$array_packages_return = CorreosOficialOrders::getCorreosPackagesReturn($order->get_id());
			foreach ($array_packages_return as $bulto => $field) {
				if ($correos_return['carrier_type'] == 'Correos') {
					$package_status_return = $this->correos_rest->getOrderStatus($field['shipping_number'], false);
					
					if (isset($package_status_return[0]->eventos)) {
						foreach ($package_status_return[0]->eventos as $evento => $field2) {
							$last_status_return[] = array(
								'codEnvio' => $package_status_return[0]->codEnvio,
								'codProducto' => isset($package_status_return[0]->codProducto) ? $package_status_return[0]->codProducto : '',
								'desTextoResumen' => $field2->desTextoResumen,
								'fecEvento' => $field2->fecEvento,
								'unidad' => isset($field2->unidad) ? $field2->unidad : '',
							);
						}
					}
				} else {
					$package_status_return = $this->cex_rest->TrackingCEXK8s($field['shipping_number'], false);

					if ($package_status_return) {
						$last_status_return[] = array(
							'codEnvio' => $package_status_return->bultoSeguimiento[0]->codUnico,
							'codProducto' => $package_status_return->producto,
							'desTextoResumen' => $package_status_return->bultoSeguimiento[0]->descEstado,
							'fecEvento' => $package_status_return->bultoSeguimiento[0]->fechaEstado,
							'unidad' => '',
						);
					}
				}
			}

			$return_cancelable = true;
			$return_status = __('No information', 'correosoficial');

			if (isset($last_status_return) && is_array($last_status_return)) {
				foreach ($last_status_return as $status_bulto => $field3) {
					if ($field3['desTextoResumen'] != 'Prerregistrado' && $field3['desTextoResumen'] != 'Admisión anulada' && $field3['desTextoResumen'] != 'SIN RECEPCION' && $field3['desTextoResumen'] != 'En espera de datos') {
						$return_cancelable = false;
					}
					$return_status = $field3['desTextoResumen'];
				}
			} else {
				$return_cancelable = false;
				$return_status = __('No information', 'correosoficial');
			}
		}

		$correos_order_bultos = isset($correos_order['bultos']) ? $correos_order['bultos'] : 0;
		// Bultos a devolver
		if (!isset($correos_order)) {
			$bultos_return = 1;
		} else {
			$bultos_return = $correos_order_bultos;
		}

		// Si hay request (office, citypaq) de checkout
		if ($this->is_wc_order_hpos_enabled()) {
			$correos_request = CorreosOficialOrdersWC::getRequestRecordHPOS($order->get_id());
		} else {
			$correos_request = CorreosOficialOrdersWC::getRequestRecord($post->ID);
		}
		//Comprobamos datos del checkout
		if ($correos_request) {

			$parsed_data = array();
			$cod_office = '';
			$cod_homepaq = '';
			$parsed_data = json_decode($correos_request['data']);

			if ($carrier_order['product_type'] == 'office') {
				if (isset($parsed_data->cod_homepaq)) { // Si ha cambiado a citypaq
					$address_paq = array( 'dir_paq' => __('See label for selected office', 'correosoficial') );
				} else {
					$address_paq = array( 'dir_paq' => $parsed_data->direccion, 'loc_paq' => $parsed_data->descLocalidad, 'cp_paq' => $parsed_data->cp );
					$cod_office = $parsed_data->unidad;
				}

			} elseif ($carrier_order['product_type'] == 'citypaq') {
				if (!isset($parsed_data->des_via)) { // Si ha cambiado a oficina
					$address_paq = array( 'dir_paq' => __('See label for selected citypaq', 'correosoficial') );
				} else {
					$address_paq = array( 'dir_paq' => $parsed_data->des_via . ' ' . $parsed_data->direccion . ' ' . $parsed_data->numero, 'loc_paq' => $parsed_data->desc_localidad, 'cp_paq' => $parsed_data->cod_postal );
					$cod_homepaq = $parsed_data->cod_homepaq;
				}
			}
		} else {
			$address_paq = array( 'dir_paq' => '', 'loc_paq' => '', 'cp_paq' => '' );
		}

		$customer_postal_code = $address->postcode;
		$customer_country = $order->get_shipping_country();

		// Aduanas
		if ($default_sender) {
			$sender_postal_code = $default_sender['sender_cp'];
			$sender_country = $default_sender['sender_iso_code_pais'];
			$require_customs_doc = NeedCustoms::isCustomsRequired($sender_postal_code, $customer_postal_code, $sender_country, $customer_country);
			$is_international = NeedCustoms::isInternational($sender_country, $customer_country);
		}

		if ($carrier_order) {
			$order_returnable = $this->isOrderReturnable($carrier_order, $customer_country);
			$is_code_at = $this->isATCode($carrier_order, $address, $default_sender);
		}

		$height_by_default = '';
		$large_by_default = '';
		$width_by_default = '';
		$bank_acc_number = '';
		
		// Obtenemos configuración por defecto
		$correos_config = CorreosOficialConfigDao::getConfig();
		foreach ($correos_config as $prop) {
			if ($prop['name'] == 'DefaultPackages') {
				$bultos_config = $prop['value'];
				if ($order_done) {
					$bultos = $correos_order['bultos'];
				} else {
					$bultos = $bultos_config;
				}
			}
			if ($prop['name'] == 'BankAccNumberAndIBAN') {
				$bank_acc_number = CorreosOficialCrypto::decrypt($prop['value']);
				$BankIni = substr($bank_acc_number, 0, -4);
				$BankFin = substr($bank_acc_number, -4);
				$bank_acc_number = str_repeat('*', strlen($BankIni)) . $BankFin;
			}
			if ($prop['name'] == 'DefaultLabel') {
				$DefaultLabel = $prop['value'];
			}
			if ($prop['name'] == 'WeightByDefault') {
				$weight_by_default = $prop['value'];
			}

			/* Obtenemos dimensiones por defecto si existen */
			if ($prop['name'] == 'DimensionsByDefaultHeight') {
				$height_by_default = $prop['value'];
			}
			if ($prop['name'] == 'DimensionsByDefaultLarge') {
				$large_by_default = $prop['value'];
			}
			if ($prop['name'] == 'DimensionsByDefaultWidth') {
				$width_by_default = $prop['value'];
			}

			if ($prop['name'] == 'GoogleMapsApi') {
				$google_maps_api = $prop['value'];
			}
			if ($prop['name'] == 'LabelObservations') {
				if ($prop['value'] == 'on') {
					$customer_message = substr($order->get_customer_note(), 0, 80);
				} else {
					$customer_message = '';
				}
			}
			if ($prop['name'] == 'TariffRadio') {
				if ($prop['value'] == 'on') {
					$config_default_aduanera = 1;
				} else {
					$config_default_aduanera = 0;
				}
			}
			if ($prop['name'] == 'AgreeToAlterReferences') {
				if ($prop['value'] == 'on') {
					$option_labeldata = CorreosOficialConfigDao::getConfigValue('ShowLabelData');

					switch ($option_labeldata['value']) {
						case '1':
							$ship_reference = $order->get_id();
							break;
						case '2':
							$ship_reference = $order_reference; // $order->reference
							break;
						case '3':
							$ship_reference = $order_reference; // $order->reference
							break;
						case '4':
							$ship_reference = '';
							break;
						default:
							$ship_reference = $order_reference; // $order->reference
					}
				} else {
					$ship_reference = $order_reference; // $order->reference
				}
			}
		}

		// Obtenemos unidades
		$co_order = new CorreosOficialOrder($order->get_id());
		$orderUnits = $co_order->getUnits();

		// Calculamos peso
		$items = $order->get_items();
		$totalWeight = self::getTotalWeightCart($items);

		if ($totalWeight == 0) {
			$orderWeight = $weight_by_default;
		} else {
			$orderWeight = $totalWeight;
			if ($bultos > 1) {
				$orderWeight = '';
			}
		}

		// added_values: ocultamos el número de IBAN excepto últimos 4 dígitos
		if (isset($correos_order['added_values_cash_on_delivery_iban'])) {
			$BankIni = substr($correos_order['added_values_cash_on_delivery_iban'], 0, -4);
			$BankFin = substr($correos_order['added_values_cash_on_delivery_iban'], -4);
			$correos_order['added_values_cash_on_delivery_iban'] = str_repeat('*', strlen($BankIni)) . $BankFin;
		}

		// Calculamos valor
		if ($bultos > 1) {
			$orderTotalValue = '';
		} else {
			$orderTotalValue = $order->get_subtotal();
		}

		// Url acceso a settings desde pedido
		$shop_admin_url = admin_url();
		$slug = 'admin.php?page=settings';
		$co_url_settings = $shop_admin_url . $slug;

		// Descripciones aduaneras
		$customs_desc_array = CorreosOficialConfigDao::getDefaultCustomsDescription();
		// Descripción aduanera por defecto
		$customs_desc_selected = CorreosOficialConfigDao::getConfigValue('DefaultCustomsDescription');

		// Número tarifario por defecto
		$customs_tariff_selected = CorreosOficialConfigDao::getConfigValue('Tariff');
		// Descripción del número tarifario
		$customs_tariff_description = CorreosOficialConfigDao::getConfigValue('TariffDescription');
		// Referencia aduanera del expedidor
		$customs_reference = CorreosOficialConfigDao::getConfigValue('ShippCustomsReference');

		// Si no están definidas las definimoas a blanco
		$correos_order['shipping_number'] = isset($correos_order['shipping_number']) ? $correos_order['shipping_number'] : '';
		$correos_order['pickup_number'] = isset($correos_order['pickup_number']) ? $correos_order['pickup_number'] : '';
		$correos_order['AT_code'] = isset($correos_order['AT_code']) ? $correos_order['AT_code'] : '';

		$correos_return = !$correos_return ? array( 'shipping_number' => '' ) : $correos_return;

		$carrier_type = isset($correos_return['carrier_type']) ? $correos_return['carrier_type'] : 'Correos';

		$address_paq = isset($address_paq) ? $address_paq : array();
		$address_paq['dir_paq'] = isset($address_paq['dir_paq']) ? $address_paq['dir_paq'] : '';
		$address_paq['loc_paq'] = isset($address_paq['loc_paq']) ? $address_paq['loc_paq'] : '';
		$address_paq['cp_paq'] = isset($address_paq['cp_paq']) ? $address_paq['cp_paq'] : '';

		$pickup_return_data_response['status'] = isset($pickup_return_data_response['status']) ? $pickup_return_data_response['status'] : '';
		$pickup_return_data_response['pickup_date'] = isset($pickup_return_data_response['pickup_date']) ? $pickup_return_data_response['pickup_date'] : '';
		$pickup_return_data_response['pickup_address'] = isset($pickup_return_data_response['pickup_address']) ? $pickup_return_data_response['pickup_address'] : '';
		$pickup_return_data_response['pickup_city'] = isset($pickup_return_data_response['pickup_city']) ? $pickup_return_data_response['pickup_city'] : '';
		$pickup_return_data_response['pickup_cp'] = isset($pickup_return_data_response['pickup_cp']) ? $pickup_return_data_response['pickup_cp'] : '';

		$pickup_to = isset($pickup_to) ? $pickup_to : '';
		$pickup_from = isset($pickup_from) ? $pickup_from : '';

		// Asignamos datos a la plantilla
		$this->smarty->assign('show_sender_modal', $showSenderModal);
		$this->smarty->assign('error_sender_name', $errorSenderName);
		$this->smarty->assign('error_company_name', $errorCompanyName);

		$this->smarty->assign('active_client', $active_client);
		$this->smarty->assign('order', $order);
		$this->smarty->assign('order_number', $order_number);
		$this->smarty->assign('order_reference', $order_reference);
		$this->smarty->assign('order_id', $order->get_id());
		$this->smarty->assign('orderTotalValue', $orderTotalValue);
		$this->smarty->assign('order_done', $order_done);
		$this->smarty->assign('exist_return', $exist_return);
		$this->smarty->assign('correos_order', $correos_order);
		$this->smarty->assign('correos_return', $correos_return);

		$this->smarty->assign('carrier_type', $carrier_type);

		$this->smarty->assign('array_packages_order', $array_packages_order);
		$this->smarty->assign('array_packages_return', $array_packages_return);

		$this->smarty->assign('cash_on_delivery', $cash_on_delivery);
		$this->smarty->assign('cash_on_delivery_value', $cash_on_delivery_value);

		$this->smarty->assign('customer_message', $customer_message);

		$this->smarty->assign('carriers', $carriers);
		$this->smarty->assign('id_zone', $id_zone);

		$this->smarty->assign('carrier_order', $carrier_order);

		$this->smarty->assign('client_code', $client_code);

		$this->smarty->assign('default_sender', $default_sender);
		$this->smarty->assign('senders', $senders);

		$this->smarty->assign('customer', $customer);
		$this->smarty->assign('address', $address);
		$this->smarty->assign('countries', $countries);

		$this->smarty->assign('pickup', $pickup);

		$this->smarty->assign('pickup_data_response', $pickup_data_response);
		$this->smarty->assign('pickup_cancelable', $pickup_cancelable);

		$this->smarty->assign('order_returnable', $order_returnable);

		$this->smarty->assign('pickup_return', $pickup_return);
		$this->smarty->assign('saved_return_pickup', $saved_return_pickup);
		$this->smarty->assign('pickup_return_data_response', $pickup_return_data_response);
		$this->smarty->assign('pickup_return_cancelable', $pickup_return_cancelable);

		$this->smarty->assign('cancelable', $cancelable);
		$this->smarty->assign('delivered', $delivered);

		$this->smarty->assign('return_cancelable', $return_cancelable);

		$this->smarty->assign('return_status', $return_status);

		$this->smarty->assign(
			'select_label_options', array(
			LABEL_TYPE_THERMAL => __('Thermic', 'correosoficial'),
			LABEL_TYPE_ADHESIVE => __('Adhesive', 'correosoficial'),
			/* LABEL_TYPE_HALF     => __('Half sheet', 'correosoficial'), */
			)
		);

		$this->smarty->assign('DefaultLabel', $DefaultLabel);
		$company = sanitize_text_field(isset($carrier_order['company']) ? $carrier_order['company'] : '');

		$this->smarty->assign(
			'select_label_options_format', array(
			LABEL_FORMAT_STANDAR => __('Standar', 'correosoficial'),
			LABEL_FORMAT_3A4 => __('3/3A (Only CEX)', 'correosoficial'),
			/* LABEL_FORMAT_4A4     => __('4/3A (Only CEX)', 'correosoficial') */
			)
		);

		$this->smarty->assign('DefaultLabel', $DefaultLabel);
		$this->smarty->assign('bank_acc_number', $bank_acc_number);

		$this->smarty->assign('bultos', $bultos);
		$this->smarty->assign('bultos_return', $bultos_return);
		$this->smarty->assign('orderWeight', $orderWeight);

		// comprobamos si el carier está dentro de los disponibles para las dimiensiones por defecto
		$carriers_default_dimensions = array( 'S0179', 'S0176', 'S0178' );

		$codigoProducto = ( !isset($carrier_order) ) ? $correos_order['codigoProducto'] : $carrier_order['codigoProducto'];
		$available_carrier_d = ( $codigoProducto !== null && in_array($codigoProducto, $carriers_default_dimensions ) ) ? 1 :0;

		$this->smarty->assign('available_carrier_default_dimensions', $available_carrier_d);
		$this->smarty->assign('height_by_default', $height_by_default);
		$this->smarty->assign('large_by_default', $large_by_default);
		$this->smarty->assign('width_by_default', $width_by_default);

		$this->smarty->assign('orderUnits', $orderUnits);
		$this->smarty->assign('ship_reference', $ship_reference);
		$this->smarty->assign('google_maps_api', $google_maps_api);

		$this->smarty->assign('require_customs_doc', $require_customs_doc);
		$this->smarty->assign('is_international', $is_international);
		$this->smarty->assign('config_default_aduanera', $config_default_aduanera);

		$this->smarty->assign('customs_desc_array', $customs_desc_array);
		$this->smarty->assign('customs_desc_selected', $customs_desc_selected);
		$this->smarty->assign('customs_tariff_selected', $customs_tariff_selected);
		$this->smarty->assign('customs_tariff_description', $customs_tariff_description);
		$this->smarty->assign('customs_reference', $customs_reference);

		$this->smarty->assign('address_paq', $address_paq);
		$this->smarty->assign('cod_office', $cod_office);
		$this->smarty->assign('cod_homepaq', $cod_homepaq);

		$this->smarty->assign('is_code_at', $is_code_at);

		$this->smarty->assign('co_base_dir', $co_module_url_wc);
		$this->smarty->assign('co_url_settings', $co_url_settings);
		
		// copy data button (office/citypaq)
		$contentCopied = __('Content copied to clipboard', 'correosoficial');
		$co_titleAddress = __('"Address: "', 'correosoficial');
		$co_titleCity = __('"City: "', 'correosoficial');
		$co_titleCp = __('"CP: "', 'correosoficial');

		$this->smarty->assign('contentCopied', $contentCopied);
		$this->smarty->assign('co_titleAddress', $co_titleAddress);
		$this->smarty->assign('co_titleCity', $co_titleCity);
		$this->smarty->assign('co_titleCp', $co_titleCp);

		// modalDialog adminOrder
		$atention = __('"Atention"', 'correosoficial');
		$messageForCancelOfficeAndCityPaq = __('"Cancelling the order will delete the data from the office/CityPaq. They can be copied to the clipboard by pressing the button next to Change office/CityPaq"', 'correosoficial');
		$messageWrongLabelFormat = __('"The selected format is only available for CEX "', 'correosoficial');
		$cancelOrderStr = __('"Cancel Order"', 'correosoficial');
		$cancelStr = __('"Cancel"', 'correosoficial');

		$this->smarty->assign('atention', $atention);
		$this->smarty->assign('messageForCancelOfficeAndCityPaq', $messageForCancelOfficeAndCityPaq);
		$this->smarty->assign('cancelOrderStr', $cancelOrderStr);
		$this->smarty->assign('cancelStr', $cancelStr);
		$this->smarty->assign('messageWrongLabelFormat', $messageWrongLabelFormat);
		$this->smarty->assign('sga_module', CorreosOficialUtils::sislogModuleIsActive());
		$this->smarty->assign('sga_id_order', '');
		
		$analitica = new Analitica();

		$vars = array();

		if (isset($_POST['gdpr_nonce'])) {
			$gdprNonce = sanitize_text_field( $_POST['gdpr_nonce'] );
			if (wp_verify_nonce($gdprNonce, 'gdpr_nonce')) {
				$vars = $_POST;
			}
		}

		$gdpr = $analitica->gdpr($vars);
		
		$template = 'hook/admin-order.tpl';
		if ($gdpr) {
			$template = 'admin/correosGdpr.tpl';
			$this->smarty->assign('gdpr_nonce', wp_create_nonce( 'gdpr_nonce' ));
		}

		$this->smarty->registerFilter('pre', 'Prefilter::preFilterConstants');
		$this->smarty->display($this->plugin_dir . 'views/templates/' . $template);
	}

	private function isOrderReturnable( $carrier_order, $customer_country ) {
		// Comprobación envío admite devolución
		$order_returnable = false;

		// Para cualquier transportista ajeno a Correos/CEX
		if (empty($carrier_order['company'])) {
			return true;
		} elseif ($carrier_order['company'] == 'CEX') {
			// CEX admite ES/AD
			if ($customer_country == 'ES' || $customer_country == 'PT') {
				$order_returnable = true;
			}
		} elseif ($carrier_order['company'] == 'Correos') {
			// Correos admite ES/PT
			if ($customer_country == 'ES' || $customer_country == 'AD') {
				$order_returnable = true;
			}
		}

		return $order_returnable;
	}

	private function isATCode( $carrier_order, $address, $default_sender ) {
		// CódigoAT -> Exclusivo CEX
		if ($default_sender && $carrier_order['company'] == 'CEX'
		&& $address->id_country == 'PT' && $default_sender['sender_iso_code_pais'] == 'PT') {
			return true;
		}
	
		return false;
	}
	

	private function getCarrierOrder( $id_zone, $order_id_carrier, $correos_order ) {
		$carrier = array(
			'id_carrier' => null,
			'codigoProducto' => null,
			'product_type' => null,
			'company' => null,
		);
		if ($id_zone >= 0) {
			$id_carrier_product = CorreosOficialCarrier::getCarriersProducts($order_id_carrier, $id_zone);

			// Si ha cambiado de zona (CP y Provincia) y ha sido preregistrado
			if (empty($id_carrier_product) && isset($correos_order['id_product'])) {
				$id_carrier_product[0]['id_product'] = $correos_order['id_product'];
			}

			// Si es un tranporsita externo
			if (empty($id_carrier_product)) {
				return $carrier;
			} else {
				$carrier_order = CorreosOficialCarrier::getCarrierByProductId($id_carrier_product[0]['id_product'], $id_zone);
			}
		} elseif (empty($correos_order)) {
				$carrier_order = CorreosOficialCarrier::getCarrier($order_id_carrier);
		} elseif ($correos_order['shipping_number'] != '') {
				$carrier_order = CorreosOficialCarrier::getCarrier($correos_order['id_carrier']);
		} else {
			$carrier_order = CorreosOficialCarrier::getCarrier($order_id_carrier);
		}

		return $carrier_order;
	}

	/**
	 * Devuelve el peso total del carrito
	 *
	 * @param  array $items Array con los elementos del carrito
	 * @return float Devuelve el peso del carrito en Kg.
	 */
	private static function getTotalWeightCart( $items ) {

		$order_weight = 0;
		$totalWeight = 0;

		/* Calculamos peso */
		foreach ($items as $item) {

			if ($item['product_id'] > 0) {
				$product = $item->get_product();

				if (!$product->is_virtual()) {
					$order_weight += (float) $product->get_weight() * $item['qty'];
				}
			}
		}

		$totalWeight = $totalWeight + $order_weight;

		return $totalWeight;
	}

	private static function getPickUpDataResponse( $status, $cod_status = '', $pickup_date = '' ) {
		return array(
			'codEstado' => $cod_status,
			'status' => $status,
			'pickup_reference' => '',
			'pickup_date' => $pickup_date,
			'pickup_from_hour' => '',
			'pickup_to_hour' => '',
			'pickup_address' => '',
			'pickup_city' => '',
			'pickup_cp' => '',
		);
	}

	private static function returnPickupLastStatus( $array_status ) {
		$last_status = '';
		$count = 1;

		if (is_array($array_status) || is_object($array_status)) {

			$count = count($array_status->ns3TrazaSolicitudRecogidaEsporadica);

			if ($count == 1) {
				$last_status = end($array_status); //Nos quedamos con estado único
			} else {
				$last_status = $array_status->ns3TrazaSolicitudRecogidaEsporadica[$count - 1]; //Nos quedamos con el último estado
			}
		}
		return $last_status;
	}

	private function correosecomsgaOrderTracking() {
		global $post;
		
		// Order
		if ($this->is_wc_order_hpos_enabled()) {
			$id_order = Normalization::normalizeData('id');
		} else {
			$id_order = $post->ID;
		}

		$order = new WC_Order($id_order);
		$this->smarty->assign('sga_id_order', $order->get_id());
		$this->smarty->assign('sga_module', CorreosOficialUtils::sislogModuleIsActive());
		
		$template_path = $this->plugin_dir . 'views/templates/hook/adminOrderTracking.tpl';

		$this->smarty->registerFilter('pre', 'Prefilter::preFilterConstants');

		$this->smarty->display($template_path);
	}
}
