<?php
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartSchema;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CheckoutSchema;

require_once 'vendor/ecommerce_common_lib/Correos/CorreosSoap.php';

/**
 * Shipping Workshop Extend Store API.
 */
class CorreosOficial_Wc_Extend_Store_Endpoint {

	/**
	 * Stores Rest Extending instance.
	 *
	 * @var ExtendRestApi
	 */
	private static $extend;

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'correosoficial';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {
		self::$extend = Automattic\WooCommerce\StoreApi\StoreApi::container()->get(
			Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::class
		);
		self::extendStore();
	}

	/**
	 * Registers the actual data into each endpoint.
	 */
	public static function extendStore() {

		if (is_callable(array( self::$extend, 'register_endpoint_data' ))) {
			self::$extend->register_endpoint_data(
				array(
					'endpoint' => CheckoutSchema::IDENTIFIER,
					'namespace' => self::IDENTIFIER,
					'schema_callback' => array( 'CorreosOficial_Wc_Extend_Store_Endpoint', 'extendCheckoutSchema' ),
					'schema_type' => ARRAY_A,
				)
			);
			self::$extend->register_endpoint_data(
				array(
					'endpoint' => CartSchema::IDENTIFIER,
					'namespace' => self::IDENTIFIER,
					'schema_callback' => function () {
						return array(
							'pickup_locations' => array(
								'description' => __('A list of Correos Pick UP Locations.', 'correosoficial'),
								'type' => 'array',
							),
							'products' => array(
								'description' => __('A Correos products list data.', 'correosoficial'),
								'type' => 'array',
							),
							'config' => array(
								'description' => __('Checkout User Config.', 'correosoficial'),
								'type' => 'array',
							),
						);
					},
					'data_callback' => function () {

						$locations = array();
						$customs_config = array();

						// Obtenemos Payload (Igual existe otra manera)
						$requestPayload = file_get_contents('php://input');
						$payload = json_decode($requestPayload, true);

						if ($payload) {
							$data = isset($payload['requests'][0]['data']) ? $payload['requests'][0]['data'] : '';
							// Comprobamos si es nuestro namespace y la acción a realizar
							if (isset($data['namespace']) && $data['namespace'] == self::IDENTIFIER) {

								if ($data['data']['action'] === 'search_postal_code') {
									$locations = self::getPickupLocations($data['data']['postcode'], $data['data']['selector_type']);
								}

								if ($data['data']['action'] === 'check_customs') {
									$customs_config = self::getCustomsConfig($data['data']['postcode'], $data['data']['country']);
								}

							}
						}

						return array(
							'pickup_locations' => $locations,
							'products' => CorreosOficialCarrier::getCarriersProductsList(),
							'config' => array(
								'googleApiKey' => CorreosOficialConfigDao::getConfigValue('GoogleMapsApi'),
								'customs' => $customs_config,
								'nif' => CorreosOficialConfigDao::getConfigValue('ActivateNifFieldCheckout'),
								'nif_required' => CorreosOficialConfigDao::getConfigValue('NifFieldRadio'),
							),
						);
					},
					'schema_type' => ARRAY_A,
				)
			);

			// Se ejecuta siempre indistintamente del endpoint
			self::$extend->register_update_callback(
				array(
					'namespace' => self::IDENTIFIER,
					'callback' => function ( $data ) {},
				)
			);

		}
	}

	private static function getCustomsConfig( $customer_postalcode, $customer_country ) {

		$customs_advice = CorreosOficialCheckoutDao::getValueConf('MessageToWarnBuyer');
		$customs_advice = $customs_advice['value'];
		$customsMessage = CorreosOficialCheckoutDao::getValueConf('TranslatableInput');
		$iso_code = get_locale();
		$id_lang = BridgeWCLanguage::getIdLanguageByIsoCode($iso_code);
		$string_translated = CorreosOficialUtils::translateStringsFromDB($customsMessage['value'], $id_lang);

		// Default sender
		$default_sender = CorreosOficialSendersDao::getDefaultSender();

		$need_customs = NeedCustoms::isCustomsRequired($default_sender['sender_cp'], $customer_postalcode, $default_sender['sender_iso_code_pais'], $customer_country);

		return array(
			'customs_advice' => ( $customs_advice == 'on' ) ? true : false,
			'string_translated' => $string_translated,
			'require_customs_doc' => $need_customs,
		);
	}

	/**
	 * Get Pickup Locations from Correos.
	 *
	 * @param string $value
	 * @param string $selector_type
	 * @return array
	 */
	private static function getPickupLocations( $postcode, $selector_type ) {

		$locations = array();

		switch ($selector_type) {

			case 'citypaq':
				$correos_soap = new CorreosSoap();
				$correos_soap_result = $correos_soap->homePaqConsultaCP1($postcode, true);
				$locations_array = json_decode($correos_soap_result, true);

				$locationsList = array();

				if (isset($locations_array['json_retorno']['soapenvBody']['homePaqRespuesta1']['listaHomePaq']['homePaq'])) {
					$locationsListHomePaq = $locations_array['json_retorno']['soapenvBody']['homePaqRespuesta1']['listaHomePaq']['homePaq'];
				
					// Tenemos más de una citypaq
					if (isset($locationsListHomePaq[0]) && is_array($locationsListHomePaq[0])) {
						$locationsList = $locationsListHomePaq;
					} else {
						$locationsList[] = $locationsListHomePaq;
					}
				
				}

				if (count($locationsList)) {
					$locations = array_map(function ( $item ) {
						return array(
							'reference' => $item['cod_homepaq'],
							'name' => $item['alias'],
							'address' => $item['des_via'] . ' ' . $item['direccion'] . ' ' . $item['numero'],
							'zipcode' => $item['cod_postal'],
							'city' => $item['desc_localidad'],
							'phone' => '',
							'scheduleLV' => '',
							'scheduleS' => '',
							'scheduleF' => '',
							'schedule' => $item['ind_horario'],
							'lat' => $item['latitudWGS84'],
							'long' => $item['longitudWGS84'],
							'data' => $item,
						);
					}, $locationsList);

				}

				break;
			case 'office':
				$correos_soap = new CorreosSoap();
				$correos_soap_result = $correos_soap->localizadorConsulta($postcode, true);
				$locations_array = json_decode($correos_soap_result, true);

				$locationsListItem = $locations_array['mensajeRetorno']['item'];
				$locationsList = array();

				// Tenemos más de una oficina
				if (isset($locationsListItem[0]) && is_array($locationsListItem[0])) {
					$locationsList = $locationsListItem;
				} else {
					$locationsList[] = $locationsListItem;
				}

				if (count($locationsList)) {

					// hacemos map para que sea igual que el de citypaq
					$locations = array_map(function ( $item ) {
						return array(
							'reference' => $item['unidad'],
							'name' => $item['nombre'],
							'address' => $item['direccion'],
							'zipcode' => $item['cp'],
							'city' => $item['descLocalidad'],
							'phone' => $item['telefono'],
							'scheduleLV' => $item['horarioLV'],
							'scheduleS' => $item['horarioS'],
							'scheduleF' => $item['horarioF'],
							'schedule' => '',
							'lat' => $item['latitudWGS84'],
							'long' => $item['longitudWGS84'],
							'data' => $item,
						);
					}, $locationsList);

				}

				break;
			// Los insert Citypaq y Office no aplican(se hace con el action woocommerce_checkout_order_created)
			case 'insertCityPaq':
			case 'insertOffice':
				break;
			default:
				throw new LogicException('Error 21000: No se ha indicado un "action" para el formulario.');
		}

		return $locations;
	}

	/**
	 * Register shipping workshop schema into the Checkout endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extendCheckoutSchema() {
		return array(
			'selectedPickupLocationOption' => array(
				'description' => 'Pickup location selected by the user',
				'type' => 'object',
				'context' => array( 'view', 'edit' ),
				'readonly' => true,
				'arg_options' => array(
					'validate_callback' => function ( $value ) {
						return true;
					},
				),
			),
			'nifCode' => array(
				'description' => 'Cutomer nif code',
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
				'readonly' => true,
				'arg_options' => array(
					'validate_callback' => function ( $value ) {
						return true;
					},
				),
			),
		);
	}
}
