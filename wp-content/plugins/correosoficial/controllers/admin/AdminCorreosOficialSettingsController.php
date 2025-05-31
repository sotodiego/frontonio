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

if (!defined('WPINC')) {
	die;
}

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialCustomerDataDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialProductsDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialActiveCustomersDao.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/functions.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/CorreosOficialUtils.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

require_once __DIR__ . '/../../classes/CorreosOficialZonesWC.php';
require_once __DIR__ . '/../../classes/Analitica.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CustomsProcessingDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialZonesCarriersDao.php';
use CorreosOficialCommonLib\Commons\CorreosOficialCrypto;

class AdminCorreosOficialSettingsController {

	private $dao;
	private $senders_dao;

	private $smarty;

	public function __construct( $smarty ) {
		$this->smarty = $smarty;

		include_once WP_PLUGIN_DIR . '/correosoficial/langs/settingsLang.php';

		$this->dao = new CorreosOficialDao();
		$this->senders_dao = new CorreosOficialSendersDao();

		if (isset($_POST['_nonce'])) {
			$nonce = sanitize_text_field($_POST['_nonce']);
			if (!wp_verify_nonce(wp_unslash($nonce), 'correosoficial_nonce')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wp_send_json_error( 'bad_nonce' );
				wp_die();
			}
		}

		if (isset($_POST['dispatcher']['action']) && $_POST['dispatcher']['action'] == 'getDataTable') {
			$this->getDataTableSenders();
		}

		$this->smarty->assign('co_base_dir', site_url());
		$this->smarty->assign('Processing', 'Procesando');
		$this->renderView();
	}

	private function renderView() {

		wp_enqueue_script('customer_data', plugins_url('correosoficial/js/customer-data.js'), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_script('senders', plugins_url('correosoficial/js/senders.js'), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_script('user_configuration', plugins_url('correosoficial/js/user-configuration.js'), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_script('zones_carrier', plugins_url('correosoficial/js/zones-carriers.js'), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_script('products', plugins_url('correosoficial/js/products.js'), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_script('customs_processing', plugins_url('correosoficial/views/js/commons/customs-processing.js'), array(), CORREOS_OFICIAL_VERSION, 'all');

		wp_enqueue_script(
			'co_ajax', plugins_url('correosoficial/views/js/commons/ajax.js'),
			array(),
			CORREOS_OFICIAL_VERSION,
			true
		);

		wp_enqueue_script(
			'co_ajax_wc', plugins_url('correosoficial/js/ajax_wc_settings.js'),
			array(),
			CORREOS_OFICIAL_VERSION,
			true
		);

		wp_enqueue_script(
			'co_common_settings',
			plugins_url('correosoficial/views/js/commons/common-settings.js'),
			array(),
			CORREOS_OFICIAL_VERSION,
			true
		);

		// Pasamos variables js al frontal
		wp_localize_script(
			'senders', 'varsAjax', array(
			'nonce' => wp_create_nonce('correosoficial_nonce'),
			'ajaxUrl' => admin_url('admin-ajax.php'),
			)
		);

		global $co_module_url;

		$this->smarty->assign('UploadLogoLabels');
		// Rellenamos checkbox y selectores de forma global en Ajustes.
		$this->fillSettingsCheckBoxAndSelectores();

		// Rellenar selectores de contrato en formulario remitente
		$this->fillSenderFormContractSelector($this->dao);

		$this->getProducts($this->dao);
		$this->getZonesAndCarriers();
		
		$DefaultLabel = CorreosOficialConfigDao::getConfigValue('DefaultLabel');
		$payment_method_selected = CorreosOficialConfigDao::getConfigValue('CashOnDeliveryMethod');
		$customs_desc_array = CustomsProcessingDao::getDefaultCustomsDescription();
		$customs_tariff_array = CorreosOficialConfigDao::getConfigValue('Tariff');
		$customs_desc_selected = CorreosOficialConfigDao::getConfigValue('DefaultCustomsDescription');
		$ShippCustomsReference = CorreosOficialConfigDao::getConfigValue('ShippCustomsReference');

		$select_label_options = array( '0' => __('Adhesive', 'correosoficial'), /* '1' => __('Half sheet', 'correosoficial'),  */'2' => __('Thermic', 'correosoficial') );
		$select_payment_method = array();
		
		$gateways = WC()->payment_gateways->get_available_payment_gateways();

		$select_payment_method = array( '0' => __('None', 'correosoficial') );

		foreach ($gateways as $gateway) {
			$select_payment_method[$gateway->id] = $gateway->title;
		}

		// Obtenemos status de los pedidos
		$select_shipment_status_options = array();
		
		// Obtener los estados de pedido de WooCommerce
		$wc_order_statuses = wc_get_order_statuses();

		// Convertir el array asociativo en el formato que deseas
		$records = array();
		foreach ($wc_order_statuses as $key => $value) {
			$records[] = array( 'id_order_state' => $key, 'name' => $value );
		}

		$ShipmentPreregistered = $this->dao->readSettings('ShipmentPreregistered');
		$ShipmentDelivered = $this->dao->readSettings('ShipmentDelivered');
		$ShipmentInProgress = $this->dao->readSettings('ShipmentInProgress');
		$ShipmentCanceled = $this->dao->readSettings('ShipmentCanceled');
		$ShipmentReturned = $this->dao->readSettings('ShipmentReturned');

		$i = 0;
		foreach ($records as $record) {
			$select_shipment_status_options[$i]['id_order_state'] = $record['id_order_state'];
			$select_shipment_status_options[$i]['name'] = $record['name'];
			$i++;
		}

		// Mapeo Amazon Channable
		if (is_null($this->dao->readSettings('AutomaticProductAssignmentText'))) {
			$this->dao->createSettingRecord('AutomaticProductAssignmentText', '', 'correos_oficial_configuration');
		}
		if (is_null($this->dao->readSettings('AutomaticProductAssignmentProduct'))) {
			$this->dao->createSettingRecord('AutomaticProductAssignmentProduct', '', 'correos_oficial_configuration');
		}
		$AutomaticProductAssignmentText         = $this->dao->readSettings('AutomaticProductAssignmentText');
		$AutomaticProductAssignmentProduct      = $this->dao->readSettings('AutomaticProductAssignmentProduct');

		$activeProducts                         = $this->dao->readRecord('correos_oficial_products', 'WHERE active=1');

		$sga_module = CorreosOficialUtils::sislogModuleIsActive();

		$this->smarty->assign('sga_module', $sga_module);
		$this->smarty->assign('AutomaticProductAssignmentText', $AutomaticProductAssignmentText);
		$this->smarty->assign('AutomaticProductAssignmentProduct', $AutomaticProductAssignmentProduct);    
		$this->smarty->assign('active_products', $activeProducts);

		$this->smarty->assign('ShipmentPreregistered', $ShipmentPreregistered);
		$this->smarty->assign('ShipmentDelivered', $ShipmentDelivered);
		$this->smarty->assign('ShipmentInProgress', $ShipmentInProgress);
		$this->smarty->assign('ShipmentCanceled', $ShipmentCanceled);
		$this->smarty->assign('ShipmentReturned', $ShipmentReturned);

		$this->smarty->assign('select_shipment_status_options', $select_shipment_status_options);
		$this->smarty->assign('select_label_options', $select_label_options);
		$this->smarty->assign('DefaultLabel', $DefaultLabel);
		$this->smarty->assign('select_payment_method', $select_payment_method);
		$this->smarty->assign('payment_method_selected', $payment_method_selected);
		$this->smarty->assign('customs_desc_array', $customs_desc_array);
		$this->smarty->assign('customs_tariff_array', $customs_tariff_array);
		$this->smarty->assign('customs_desc_selected', $customs_desc_selected);
		$this->smarty->assign('ShippCustomsReference', $ShippCustomsReference);

		$TariffRadio = $this->dao->readSettings('TariffRadio');
		if ($TariffRadio->value == 'on') {
			$config_default_aduanera = 1;
		} else {
			$config_default_aduanera = 0;
		}
		$this->smarty->assign('config_default_aduanera', $config_default_aduanera);

		// Ruta para recuperar el logo de las etiquetas
		$this->smarty->assign('co_base_dir', $co_module_url);

		$this->smarty->registerFilter('pre', 'Prefilter::preFilterConstants');

		$analitica = new Analitica();

		// Comprobamos si han pasado las 12 h para actualizar
		$lastComprove = $analitica->lastHour();
		$now = gmdate('Y-m-d H:i:s');

		if (!empty($lastComprove) && strtotime($now) > strtotime($lastComprove . '+ 12 hours')) {
			$analitica->moduleRecord(); 
			$analitica->externalModulesRecord();
			$analitica->configurationCall('undefined');
			$analitica->updateTime();
		}

		$vars = array();

		if (isset($_POST['gdpr_nonce'])) {
			$gdprNonce = sanitize_text_field( $_POST['gdpr_nonce'] );
			if (wp_verify_nonce($gdprNonce, 'gdpr_nonce')) {
				$vars = $_POST;
			}
		}
		
		$gdpr = $analitica->gdpr($vars);

		//plantilla
		$template = 'settings.tpl';
		if ($gdpr) {
			$template = 'correosGdpr.tpl';
			$this->smarty->assign('gdpr_nonce', wp_create_nonce( 'gdpr_nonce' ));
		}
		$this->smarty->fetch(__DIR__ . '/../../views/templates/admin/' . $template);
		$this->smarty->display($template);
	}

	/**
	 * Rellenamos checkbox y selectores de forma global en Ajustes.
	 *
	 * @param Oject $dao. El dao.
	 */
	public function fillSettingsCheckBoxAndSelectores() {
		$records = $this->dao->readRecord('correos_oficial_configuration');

		/**
		 * Autorreleno de Selectores y checkbox
		 */
		foreach ($records as $record) {

			$this->smarty->assign($record->name, $record->value);

			if ($record->name == 'BankAccNumberAndIBAN') {
				if (!empty($record->value)) {
					$BankAccNumberAndIBAN = CorreosOficialCrypto::decrypt($record->value);

					//Se sustituyen los primeros caracteres por asteriscos y se dejan sólo los últimos cuatro números
					$BankIni = substr($BankAccNumberAndIBAN, 0, -4);
					$BankFin = substr($BankAccNumberAndIBAN, -4);
					$BankAccNumberAndIBAN = str_repeat('*', strlen($BankIni)) . $BankFin;

					$this->smarty->assign($record->name, $BankAccNumberAndIBAN);
				} else {
					$this->smarty->assign($record->name, $record->value);
				}
			}

			// Si no ha seleccionado ningún idioma del selector toma el idioma del contexto
			if ($record->name == 'FormSwitchLanguage') {
				if (!empty($record->value)) {
					$language_id = $record->value;
				} else {
					$languages[] = CorreosOficialUtils::getActiveLanguages();
					$language_id = $languages[0][0]['id_lang'];
				}
			}

			if ($record->name == 'TranslatableInput') {
				$this->smarty->assign('TranslatableInputH', CorreosOficialUtils::restoreBadCharacters($record->value));
				$string_translated = CorreosOficialUtils::translateStringsFromDB($record->value, $language_id);
				$this->smarty->assign($record->name, $string_translated);
			}

			if ($record->type == 'checkbox' && ( $record->value == 'true' || $record->value == 'on' )) {
				$this->smarty->assign($record->name, 'checked');
			}

			$getUserLogo = CorreosOficialConfigDao::getConfigValue('UploadLogoLabels');

			if ($record->name == 'UploadLogoLabels' && ( $getUserLogo == '' || $getUserLogo == 'default.jpg' )) {
				if ($record->value == '') {
					$this->smarty->assign('baseLabel', 'default.jpg');
				} else {
					$result = $record->value;
					if (strstr($result, 'ERROR:  12010')) {
						$result = __('ERROR 12010: Allowed formats: png, jpg, jpeg', 'correosoficial');
						$this->smarty->assign('ErrorLogoLabels', $result);
					} else {
						$this->smarty->assign('UploadLogoLabelsName', Normalization::filterFiles($result));
						$this->smarty->assign('UploadLogoLabels', $result);
					}

				}
			}

			if ($record->name == 'CronInterval') {
				$this->smarty->assign('CronInterval', $record->value);
			}

			$this->smarty->assign('showNIF', 'true');

		}

		$active_languages = CorreosOficialUtils::getActiveLanguages();
		CorreosOficialUtils::fillLanguagesSelector($active_languages, $this->smarty, $language_id);
	}

	/**
	 * Para conseguir el datatable de Senders
	 */
	public function getDataTableSenders() {

		global $wpdb;

		// Este código se tiene que mover a readSenders en el senders dao

		$records = $wpdb->get_results($wpdb->prepare("SELECT a.*, b.CorreosContract, b.CorreosCustomer, c.CEXCustomer
		FROM {$wpdb->prefix}correos_oficial_senders a
		LEFT JOIN {$wpdb->prefix}correos_oficial_codes b ON a.correos_code = b.id
		LEFT JOIN {$wpdb->prefix}correos_oficial_codes c ON a.cex_code = c.id"));
		
		die(json_encode($records));
	}

	/**
	 * Rellenar selectores de contrato en formulario remitente
	 *
	 * @param Oject $dao. El dao.
	 */
	public function fillSenderFormContractSelector( $dao ) {

		$optionsCountsCorreos = $dao->readRecord(
			'correos_oficial_codes',
			"WHERE company='Correos'",
			'`id`, `CorreosContract`, `CorreosCustomer`',
			true
		);

		$optionsCountsCex = $dao->readRecord(
			'correos_oficial_codes',
			"WHERE company='CEX'",
			'`id`, `CEXCustomer`',
			true
		);

		$this->smarty->assign('optionsCorreos', $optionsCountsCorreos);
		$this->smarty->assign('optionsCex', $optionsCountsCex);
	}

	public function getProducts( $dao ) {

		// Se precargan los productos pero se controlan a nivel de ajax en el frontal
		$products_column1 = $dao->readRecord('correos_oficial_products', "WHERE company='CEX'");
		$products_column2 = $dao->readRecord('correos_oficial_products', "WHERE company='CORREOS'");
		$cex = true;
		$correos = true;

		$this->smarty->assign('exist_products', true);
		$this->smarty->assign('cex', $cex);
		$this->smarty->assign('correos', $correos);

		$this->smarty->assign('products_column1', $products_column1);
		$this->smarty->assign('products_column2', $products_column2);
	}

	/**
	 * Obtiene el nombre el método de envío traducido, si no lo encuentra
	 * transforma el $method_rate_id para que pueda ser traducible.
	 * La tabla consultada es wp_options de WP.
	 *
	 * @param  string $method_id
	 * @param  int    $instance_id
	 * @return string titulo del nombre de envio
	 */
	public function getShippingNameById( $method_rate_id, $instance_id ) {
		if (!empty($method_rate_id)) {
			$method_key_id = str_replace(':', '_', $method_rate_id);
			$option_name = 'woocommerce_' . $method_key_id . '_' . $instance_id . '_settings';

			// Si existe la opción
			if (get_option($option_name, false)) {
				$title = isset(get_option($option_name)['title']) ? get_option($option_name)['title'] : '';
			}
			if (!empty($title)) {
				return $title;
			} else { // Transforma ej. flat_rate a Flat Rate para poder ser traducido
				$carrier_name = str_replace('_', ' ', $method_rate_id);
				$carrier_name = ucfirst($carrier_name);
				return $carrier_name;
			}
		}
	}

	// Obtenemos la relación de zonas y carriers y cada producto seleccionado para cada carrier
	public function getZonesAndCarriers() {
		$this->dao = new CorreosOficialDao();
		$zonesandcarriers = array();

		$this->smarty->assign('zonesandcarriers', $zonesandcarriers);

		$zones_and_carriers = new CorreosOficialZonesWC();

		$wc_zones = $zones_and_carriers->getZones('woocommerce_shipping_zones', true);

		if (empty($wc_zones)) {
			return;
		}

		foreach ($wc_zones as $wc_zone) {
			$zones[] = array(
				'id_zone' => $wc_zone['zone_id'],
				'name' => $wc_zone['zone_name'],
				'active' => 1,
			);
		}

		foreach ($zones as $zone) {
			$carriers = array();

			$wc_carriers = $zones_and_carriers->getCarriersByZone($zone['id_zone'], 'woocommerce_shipping_zone_methods');

			foreach ($wc_carriers as $wc_carrier) {

				/* Mediante el method_id y el instance_id obtenemos el nombre del método de envío y
							lo transformamos a una palabra traducible por el gestor de idiomas de WP. */
				$carrier_name = self::getShippingNameById($wc_carrier['method_id'], $wc_carrier['instance_id']);

				if ($wc_carrier['method_id'] != 'local_pickup' && !preg_match('/request_shipping_quote(_\d+)?/', $wc_carrier['method_id'])) {
					$carriers[] = array(
						'id_carrier' => $wc_carrier['instance_id'],
						'name' => __($carrier_name, 'woocommerce'),
						'active' => $wc_carrier['is_enabled'],
					);
				}
			}

			$carriers_products = array();

			$no_display_zones_without_products = false;
			$products = array();

			foreach ($carriers as $carrier) {
				$product_selected = $this->dao->getActiveProductCarrier($carrier['id_carrier'], $zone['id_zone']);

				if (!empty($product_selected)) {
					$carriers_products[] = array(
						'id_carrier' => $carrier['id_carrier'],
						'name' => $carrier['name'],
						'active' => $carrier['active'],
						'product_selected' => $product_selected[0]['id_product'],
					);
				} else {
					$carriers_products[] = array(
						'id_carrier' => $carrier['id_carrier'],
						'name' => $carrier['name'],
						'active' => $carrier['active'],
						'product_selected' => '0',
					);
				}
				$products = $this->getActiveProductsForSelect($zone['id_zone']);

				if (!$products) {
					$no_display_zones_without_products = true;
				}
			}

			// Si la zona no tiene productos asociados en WC->Ajustes->Envío no la mostramos
			if ($no_display_zones_without_products) {
				continue;
			}

			$zonesandcarriers[] = array(
				'id_zone' => $zone['id_zone'],
				'zonename' => $zone['name'],
				'carriers' => $carriers_products,
				'products' => $products,
			);
			$this->smarty->assign('zonesandcarriers', $zonesandcarriers);
		}
	}

	public function getActiveProductsForSelect( $id_zone ) {

		$products = CorreosOficialCarrier::getCarriersByCompany('both', $id_zone);
		
		$products2 = array();
		$correos_oficial_products_counter = 0;

		/*
		 * Ordenamos el array por la clave 'id_product'
		 */
		usort($products, array( $this, 'sortByKey' ));

		$before = 0;
		foreach ($products as $product) {
			$product_dao = new CorreosOficialProductsDao();

			/* Si es un producto nativo si no es de Correos */
			if ($product['id_product'] == null) {
				continue;
			} else {
				$correos_oficial_products_counter++;
			}

			/*
			 * Eliminamos repetición
			 */
			if ($before == $product['id_product']) {
				continue;
			}

			$before = $product['id_product'];

			$product_dao->id = $product['id_product'];
			$product_dao->name = $product['name'];
			$product_dao->product_type = $product['product_type'];
			$products2[] = $product_dao;
		}

		if ($correos_oficial_products_counter == 0) {
			$products2 = array();
			$this->smarty->assign('select_active_products_html', $products2);
		} else {
			$this->smarty->assign('select_active_products_html', $products2);
		}

		return $products2;
	}

	/**
	 * Se ordena por clave 'id_product'
	 */
	private function sortByKey( $a, $b ) {
		if ($a['id_product'] === $b['id_product']) {
			return 0;
		}
		return ( $a['id_product'] < $b['id_product'] ) ? -1 : 1;
	}
}
