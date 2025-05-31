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
 * Plugin Name: Correos Ecommerce
 * Plugin URI: https://es.wordpress.org/plugins/correos-ecommerce/
 * Description: Correos and Correos Express Spain plugin for shipment management. It integrates national and international parcel services, making the management of your orders a quick and easy task.
 * Version: 1.9.1
 * Author: Grupo Correos
 * Author URI: http://correos.es
 * Text Domain: correosoficial
 * Requires at least: 5.8
 * Requires PHP: 7.2
 * WC requires at least: 6.0
 * WC tested up to: 8.0
 */

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\FeaturesUtil;

if (!defined('WPINC')) {
	die;
}

define('MODULE_CORREOS_OFICIAL_PATH', __FILE__);
define('MODULE_CORREOS_OFICIAL_PATH_FRONT', plugin_dir_url('views/*.*'));

require_once 'controllers/admin/AdminCorreosOficialCronProcessController.php';

require_once 'classes/Analitica.php';
require_once 'classes/CorreosOficialAddShippingMethod.php';
require_once 'classes/CorreosOficialOrders.php';
require_once 'classes/CorreosOficialOrder.php';

require_once 'vendor/ecommerce_common_lib/Dao/CorreosOficialCheckoutDao.php';
require_once 'vendor/ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';
require_once 'vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once 'vendor/ecommerce_common_lib/Cron/CronCorreosOficial.php';
require_once 'vendor/ecommerce_common_lib/CorreosOficialSmarty.php';
require_once 'vendor/ecommerce_common_lib/Dao/CorreosOficialProductsDao.php';
require_once 'vendor/autoload.php';

//require_once 'correosDataTableController.php'; // DEPRECATED borrar en futuras versiones, junto al archivo.
require_once 'correosTrackings.php';
require_once 'controllers/admin/AdminCorreosOficialDatatableController.php';

global $smarty;
global $co_module_url;
global $co_page;

class CorreosOficial {


	private $version;
	private $smarty;

	public function __construct() {
		
		global $wpdb;

		//error_log(print_r($wpdb,true));
		define('CORREOS_OFICIAL_VERSION', $this->getModuleVersion());

		self::checkPHPversionCompatibility();

		self::installTables();

		$this->correosoficialIncludes();
		$this->correosoficialInitHooks();
		$this->version = $this->getModuleVersion();

		$ActivateNifFieldCheckout = CorreosOficialConfigDao::getConfigValue('ActivateNifFieldCheckout');
		$nifFieldRadio = CorreosOficialConfigDao::getConfigValue('NifFieldRadio');

		if ( ( isset($ActivateNifFieldCheckout) && $ActivateNifFieldCheckout == 'on' && $nifFieldRadio == 'OPTIONAL' ) ||
		$nifFieldRadio == 'OBLIGATORY' ) {
			add_action(
				'woocommerce_after_checkout_billing_form',
				'CorreosOficialNifNumberForCheckout::addNifFieldToCheckout'
			);
		}

		add_action('woocommerce_admin_order_data_after_billing_address', 'CorreosOficialNifNumberForCheckout::showPersonalisedFieldAdminOrder');
		add_action('woocommerce_checkout_update_order_meta', 'CorreosOficialNifNumberForCheckout::updateOrderInfoWithNewField');
		add_action('woocommerce_order_details_after_customer_details', array( $this, 'CorreosOficial::hookOrderDetailDisplayed' ));

		add_action('init', array( $this, 'registerShippedOrderStatus' ));
		add_filter('wc_order_statuses', array( $this, 'customOrderStatus' ));

		add_action('init', array( $this, 'init' ));

		add_action('woocommerce_checkout_order_created', array( $this, 'CorreosOficial::saveOrderFromCheckout' ));

		add_action('wp_ajax_correosOficialDispacher', array( $this, 'correosOficialDispacherProxy' ));

		self::checkPHPversionCompatibility();
		
		add_action('wp_ajax_nopriv_correosOficialDispacher', array( $this, 'correosOficialDispacherProxy' ));

		// Checkout process
		add_action('woocommerce_checkout_process', array( $this, 'CorreosOficial::validateCheckout' ));

		add_filter('upgrader_pre_install', array( $this, 'upgraderPreInstall' ), 10, 2);
		add_filter('upgrader_post_install', array( $this, 'upgraderPostInstall' ), 10, 2);

		// Eliminación de pedido
		add_action('before_delete_post', array( $this, 'CorreosOficial::deleteOrder' ), 10, 2);

		add_action( 'plugins_loaded', array( $this, 'check_woocommerce_version' ) );

		// Procesos a ejecutar para capturas de pedidos que entrar por API Rest usado por el módulo Channable
		add_action( 'woocommerce_new_order_item', array( $this, 'channableTasks' ), 10, 3 );
		$page = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : '');

		if ($page == 'settings' || $page == 'utilities' || $page == 'notifications' || $page == 'correosoficial') {
			$error = __('ERROR 12050: To use webservice credentials, you must have the SOAP feature installed. Please contact your hosting for more information.', 'correosoficial');
			CorreosOficialUtils::checkSoapInstalled($error);
		}

		// Cron schedule interval
		add_action('correosoficial_tracking_cron_event', array( AdminCorreosOficialCronProcessController::class, 'cronExecute' ));
		add_filter('cron_schedules', array( AdminCorreosOficialCronProcessController::class, 'updateCronInterval' ));
	}

	public function check_woocommerce_version() {
		if ( class_exists( 'WooCommerce' ) ) {
			$current_version = WC_VERSION;
	
			if ( version_compare( $current_version, '8.5.3', '>' ) ) {
				// Declara la compatibilidad con los bloques de WooCommerce.
				add_action( 'before_woocommerce_init', function () {
					if ( class_exists( FeaturesUtil::class ) ) {
						FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true ); // Checkout Blocks
						FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true ); // HPOS
					}
				} );
	
				/**
				 * Include the dependencies needed to instantiate the block.
				 */
				add_action(
					'woocommerce_blocks_loaded',
					function () {
						require_once __DIR__ . '/correosoficial-wc-extend-store-endpoint.php';
						require_once __DIR__ . '/correosoficial-wc-extend-woo-core.php';
						require_once __DIR__ . '/correosoficial-wc-blocks-integration.php';
	
						// Initialize our store endpoint extension when WC Blocks is loaded.
						CorreosOficial_Wc_Extend_Store_Endpoint::init();
	
						// Add hooks relevant to extending the Woo core experience.
						$extend_core = new CorreosOficial_Wc_Extend_Woo_Core();
						$extend_core->init();
	
						add_action(
							'woocommerce_blocks_checkout_block_registration',
							function ( $integration_registry ) {
								$integration_registry->register(new CorreosOficial_Wc_Blocks_Integration());
							}
						);
					}
				);
			} else {
				add_action( 'admin_notices', function () {
					echo '<div class="notice notice-error">';
					echo '<p><strong>' . esc_html(__( 'Error:', 'correosoficial' )) . '</strong> ' . esc_html(__( 'The current installed version of WooCommerce is not compatible with this Correos Oficial version.', 'correosoficial' )) . '</p>';
					echo '</div>';
				} );
			}
		}
	}
	
	public function init() {
		$this->updater();
	}

	public function updater() {
		if (!get_option('CORREOS_OFICIAL_LAST_UPDATE') || get_option('CORREOS_OFICIAL_LAST_UPDATE') != $this->version) {

			//$this->deleteDuplicatedOrders('correos_oficial_saved_orders');
			//$this->deleteDuplicatedOrders('correos_oficial_saved_returns');

			$this->deleteLabelFromTables();
			$this->updateOldShippingMethods();

			self::createCronTasks();

			$plugin_path = 'correosoficial/correosoficial.php';
			$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
			update_option('CORREOS_OFICIAL_LAST_UPDATE', $plugin_data['Version']);
		}
	}

	public function correosoficialCronSchedules( $schedules ) {
		$schedules['correosoficial_cron'] = array(
			'interval' => 3600 * CorreosOficialConfigDao::getConfigValue('CronInterval'),
			'display'  => __('Cada ' . CorreosOficialConfigDao::getConfigValue('CronInterval') . ' Horas'),
		);
		return $schedules;
	}

	public function updateOldShippingMethods() {
		global $wpdb;
		$shippingZoneTable = $wpdb->prefix . 'woocommerce_shipping_zone_methods';

		try {

			$carriersList = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}correos_oficial_carriers_products
			WHERE id_carrier IN (SELECT instance_id FROM %i)", $shippingZoneTable ), ARRAY_A);

			foreach ($carriersList as $carrier) {
				// Actualizar ids de los shippings methods
				$newMethodId = "request_shipping_quote_{$carrier['id_product']}";

				$wpdb->query($wpdb->prepare('UPDATE %i
				SET method_id = %s WHERE instance_id = %d', $shippingZoneTable, $newMethodId, $carrier['id_carrier']));
				// Actualizar wp_option shipping methods quote
				$oldOptionName = 'woocommerce_request_shipping_quote_' . $carrier['id_carrier'] . '_settings';
				if (get_option($oldOptionName) !== false) {
					$newOptionName = 'woocommerce_request_shipping_quote_' . $carrier['id_product'] . '_' . $carrier['id_carrier'] . '_settings';
					$wpdb->query(
						$wpdb->prepare("UPDATE $wpdb->options SET option_name = %s WHERE option_name = %s", $newOptionName, $oldOptionName)
					);
				}

			}

		} catch (Exception $e) {
			error_log('ERROR: ' . $e);
		}
	}

	public function deleteDuplicatedOrders( $input_table ) {
		global $wpdb;

		$table = "{$wpdb->prefix}$input_table";

		try {
			// Recupera los pedidos duplicados
			$records = $wpdb->get_results($wpdb->prepare('SELECT id_order FROM %i GROUP BY id_order HAVING COUNT(id_order)>1;', $table));

			if (!count($records)) {
				return;
			}

			$bad_ones = array();

			/**
			 * Devuelve los registros duplicados de cada envío
			 */
			foreach ($records as $record) {
				$records2 = $wpdb->get_results($wpdb->prepare('SELECT * FROM  %i WHERE id_order = {$record->id_order} ORDER BY id ASC', $table), ARRAY_A);

				$i = 0;
				foreach ($records2 as $record) {

					if ($i > 0 && $records2[0]['exp_number'] != $record['exp_number']) {
						array_push($bad_ones, $record['id']);
					}
					$i++;
				}
			}

			$final = join(',', $bad_ones);

			if (!empty($final)) {
				// Eliminamos los duplicados que no sean los reales
				$wpdb->get_results($wpdb->prepare('DELETE FROM %i WHERE id IN ($final)', $table));
			}
		} catch (Exception $e) {
			// Captura cualquier excepción que se haya generado durante la ejecución
			error_log('Error :' . $e->getMessage());
		}
	}

	public function deleteLabelFromTables() {
		global $wpdb;

		$table_orders = $wpdb->prefix . 'correos_oficial_saved_orders';
		$table_returns = $wpdb->prefix . 'correos_oficial_saved_returns';

		try {
			// Comprobar si la columna 'label' existe en la tabla 'correos_oficial_saved_orders / returns '
			$column_exists_orders = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM %i LIKE 'label'", $table_orders));
			$column_exists_returns = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM %i LIKE 'label'", $table_returns));

			if ($column_exists_orders || $column_exists_returns) {
				// Si la columna 'label' existe en alguna de las tablas, intenta eliminarla
				if ($column_exists_orders) {
					$wpdb->query($wpdb->prepare('ALTER TABLE %i DROP COLUMN label', $table_orders));
				}
				if ($column_exists_returns) {
					$wpdb->query($wpdb->prepare('ALTER TABLE %i DROP COLUMN label', $table_returns));
				}
			}
		} catch (Exception $e) {
			// Captura cualquier excepción que se haya generado durante la ejecución
			error_log('Error :' . $e->getMessage());
		}
	}

	// Acciones antes de actualizar
	public function upgraderPreInstall() {
	}

	// Acciones tras la actualización
	public function upgraderPostInstall() {
	}

	/**
	 * Callback para las llamadas ajax al dispacher, haciendo de proxy
	 * entre admin-ajax.php y dispatcher.php
	 */
	public function correosOficialDispacherProxy() {

		// Verificar la seguridad del nonce (opcional pero recomendado)
		check_ajax_referer('correosoficial_nonce', '_nonce');

		// Reindexamos REQUEST
		$_REQUEST = isset($_POST['dispatcher']) ? CorreosOficialUtils::sanitize($_POST['dispatcher']) : array(); // phpcs:ignore

		// No cargar autoload en el dispacher
		$_GET['autoload'] = false;

		// Para el switch del dispacher
		$_GET['controller'] = isset($_REQUEST['controller']) ? CorreosOficialUtils::sanitize($_REQUEST['controller']) : ''; // phpcs:ignore

		require_once 'dispatcher.php';
	}

	public function validateCheckout() {
		$nonce = sanitize_text_field(
			isset($_POST['woocommerce-process-checkout-nonce']) ? $_POST['woocommerce-process-checkout-nonce'] : '');

		if (!wp_verify_nonce($nonce, 'woocommerce-process_checkout')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			foreach ($_POST as $key => $value) {
				if (strpos($key, 'citypaq_reference') !== false || strpos($key, 'office_reference') !== false) {
					if (!isset($_POST['ReferenceType']) || !isset($_POST['SelectedReference'])) {
						wc_add_notice(__('The order could not be completed, check the shipping method.', 'correosoficial'), 'error');
						return;
					}
				}
			}
		}
	}

	public function saveOrderFromCheckout( $params ) {
		if ( isset($_POST['woocommerce-process-checkout-nonce']) &&
			wp_verify_nonce(sanitize_text_field($_POST['woocommerce-process-checkout-nonce']), 'woocommerce-process_checkout')
		) {
			if (!isset($_POST['ReferenceType']) || !isset($_POST['SelectedReference'])) {
				return false;
			}

			$ReferenceType = CorreosOficialUtils::sanitize($_POST['ReferenceType']); // phpcs:ignore

			// Si es pedido de Oficina o CityPaq
			if (isset($ReferenceType) && ( $ReferenceType == 'Oficina' || $ReferenceType == 'CityPaq' )) {
				$selectedReference = filter_var($_POST['SelectedReference'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$selectedReferenceData = !empty($_POST['SelectedReferenceData']) ? CorreosOficialUtils::sanitize($_POST['SelectedReferenceData']) : ''; // phpcs:ignore

				$id_order = $params->get_id();
				$id_cart = $params->get_cart_hash();

				json_decode($selectedReferenceData);

				if (!json_last_error() != JSON_ERROR_NONE) {
					return false;
				}
				CorreosOficialCheckoutDao::insertReferenceCodeWithOrderId($id_cart, $selectedReference, $selectedReferenceData, $id_order);
			}
		}
	}

	public function registerShippedOrderStatus() {
		register_post_status(
			'wc-prepared-cocex',
			array(
				'label' => __('Shipment prepared for Correos - CEX', 'correosoficial'),
				'public' => true,
				'exclude_from_search' => false,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: Nº de pedido en estado Preparado */
				'label_count' => _n_noop('Prepared <span class="count">(%s)</span>', 'Prepared <span class="count">(%s)</span>'),
			)
		);
		register_post_status(
			'wc-cancelled-cocex',
			array(
				'label' => __('Shipment cancelled Correos - CEX', 'correosoficial'),
				'public' => true,
				'exclude_from_search' => false,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: Nº de pedido en estado Cancelado */
				'label_count' => _n_noop('Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>'),
			)
		);
		register_post_status(
			'wc-returned-cocex',
			array(
				'label' => __('Shipment returned Correos - CEX', 'correosoficial'),
				'public' => true,
				'exclude_from_search' => false,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: Nº de pedido en estado Devuelto */
				'label_count' => _n_noop('Returned <span class="count">(%s)</span>', 'Returned <span class="count">(%s)</span>'),
			)
		);
		register_post_status(
			'wc-delivered-cocex',
			array(
				'label' => __('Shipment delivered Correos - CEX', 'correosoficial'),
				'public' => true,
				'exclude_from_search' => false,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: Nº de pedido en estado Entregado */
				'label_count' => _n_noop('Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>'),
			)
		);
		register_post_status(
			'wc-inprogress-cocex',
			array(
				'label' => __('Shipment in progress Correos - CEX', 'correosoficial'),
				'public' => true,
				'exclude_from_search' => false,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: Nº de pedido en estado En Curso */
				'label_count' => _n_noop('In progress <span class="count">(%s)</span>', 'In progress <span class="count">(%s)</span>'),
			)
		);
	}

	public function customOrderStatus( $order_statuses ) {
		$order_statuses['wc-prepared-cocex'] = __('Shipment prepared for Correos - CEX', 'correosoficial');
		$order_statuses['wc-inprogress-cocex'] = __('Shipment in progress Correos - CEX', 'correosoficial');
		$order_statuses['wc-delivered-cocex'] = __('Shipment delivered Correos - CEX', 'correosoficial');
		$order_statuses['wc-cancelled-cocex'] = __('Shipment cancelled  Correos - CEX', 'correosoficial');
		$order_statuses['wc-returned-cocex'] = __('Shipment returned Correos - CEX', 'correosoficial');
		return $order_statuses;
	}

	private function correosoficialIncludes() {
		if (!class_exists('Smarty')) {
			include_once 'vendor/smarty/Smarty.class.php';
		}

		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/config.php';

		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/header.php';

		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/controllers/admin/AdminCorreosOficialHomeController.php';
		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/controllers/admin/AdminCorreosOficialSettingsController.php';
		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/controllers/admin/AdminCorreosOficialUtilitiesController.php';
		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/controllers/admin/AdminCorreosOficialNotificationsController.php';

		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/classes/CorreosOficialParamsAdapter.php';
		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/classes/CorreosOficialCarrierExtraContent.php';
		include_once $this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/classes/CorreosOficialNifNumberForCheckout.php';

		include_once 'vendor/ecommerce_common_lib/Prefilter.php';
	}

	private function correosoficialInitHooks() {
		global $co_module_url_wc;
		global $co_module_url;

		//Analitica
		register_activation_hook(__FILE__, array( $this, 'correosOficialActivation' ));
		register_deactivation_hook(__FILE__, array( $this, 'correosOficialDeactivation' ));
		add_action('all_admin_notices', array( $this, 'correosAdminHeader' ));

		load_plugin_textdomain('correosoficial', false, plugin_basename(__DIR__) . '/languages');

		add_action('admin_enqueue_scripts', array( $this, 'adminMenuCSS' ));

		add_action('admin_menu', array( $this, 'menuCorreosOficial' ));

		$this->smarty = CorreosOficialSmarty::loadSmartyInstance();
		$this->smarty->setTemplateDir(plugin_dir_path(__FILE__) . '/views/templates/admin');

		$co_module_url = plugin_dir_url(__FILE__);
		$this->smarty->assign('co_base_dir', $co_module_url);

		// Checkout
		add_action('wp_enqueue_scripts', array( $this, 'loadCheckoutStyles' ), 19);
		add_action('woocommerce_after_shipping_rate', array( $this, 'hookdisplayCarrierExtraContent' ), 20);
		add_action('wp_enqueue_scripts', array( $this, 'loadCheckoutScripts' ), 21);

		//Admin Order (Pedido)
		add_action( 'add_meta_boxes', array( $this, 'displayAdminOrderBox' ) );
	}

	public function correosOficialActivation() {
		if (version_compare(PHP_VERSION, '5.6', '<')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die('Mi Plugin requiere al menos PHP 5.6. Por favor actualiza PHP.');
		}
		if (CorreosOficialConfigDao::getConfigValue('GDPR') === '1') {
			( new Analitica() )->moduleRecord();
		}

		// Init Cron tasks
		self::createCronTasks();
	}

	public function correosOficialDeactivation() {
		( new Analitica() )->disableCall();

		wp_clear_scheduled_hook('correosoficial_tracking_cron_event');
	}

	private static function createCronTasks() {
		// Init Cron tasks
		if (! wp_next_scheduled('correosoficial_tracking_cron_event')) {
			wp_schedule_event(time(), 'correosoficial_cron', 'correosoficial_tracking_cron_event');
		}
	}

	public function correosAdminHeader() {
		if (
			isset($_GET['page'])
			&& $_GET['page'] === 'notifications'
		) {
			return;
		}

		if (CorreosOficialConfigDao::getConfigValue('GDPR') === '0') {
			return;
		}

		$lastNotificationsCall = get_option('CORREOS_OFICIAL_LAST_NOTIFICATIONS_CALL');

		if (
			!$lastNotificationsCall ||
			( $lastNotificationsCall && strtotime(gmdate('Y-m-d H:i:s')) > strtotime($lastNotificationsCall . '+ 1 hours') )
		) {

			update_option('CORREOS_OFICIAL_LAST_NOTIFICATIONS_CALL', gmdate('Y-m-d H:i:s'));

			$notifications = ( new Analitica() )->getNotifications();

			$total_notifications = 0;
			if ($notifications['status'] === 200) {
				$total_notifications = count($notifications['output']);
			}

			if ($total_notifications === 0) {
				return;
			}

			$this->smarty->assign(array(
				'notifications' => $total_notifications,
				'img' => get_home_url() . '/wp-content/plugins/correosoficial/views/commons/img/logo.gif',
				'link' => admin_url() . 'admin.php?page=notifications',
				'msg1' => __('Yo have', 'correosoficial'),
				'msg2' => __(' notifications without read in the Correosoficial module', 'correosoficial'),
				'msgButton' => __('Go to notifications', 'correosoficial'),
			));

			$this->smarty->registerFilter('pre', 'Prefilter::preFilterConstants');
			return $this->smarty->display(__DIR__ . '/views/templates/admin/notificationalert.tpl');

		}
	}

	public function loadCheckoutStyles() {
		wp_enqueue_style('co_global', plugins_url('views/commons/css/global.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_checkout', plugins_url('views/commons/css/checkout.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_override_checkout', plugins_url('override/css/checkout.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
	}

	public function loadCheckoutScripts() {

		$is_blocks_enabled = WC_Blocks_Utils::has_block_in_page( wc_get_page_id('checkout'), 'woocommerce/checkout' );        

		$google_api_key = CorreosOficialConfigDao::getConfigValue('GoogleMapsApi');
		if (!empty($google_api_key) && !$is_blocks_enabled) {
			wp_enqueue_script('google_js', 'https://maps.googleapis.com/maps/api/js?callback=Function.prototype&key=' . $google_api_key, array(), CORREOS_OFICIAL_VERSION, true);
		}

		wp_enqueue_script('co_woocommerce', plugins_url('/js/woocommerce.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		wp_enqueue_script('co_reference_code', plugins_url('js/library/reference-code.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		wp_enqueue_script('co_checkout_hide_map', plugins_url('/js/checkout_hide_map.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		self::definePluginURLS();

		// Encolando el primer script
		wp_enqueue_script(
			'co_ajax',
			plugins_url('correosoficial/views/js/commons/ajax.js'),
			array(),
			CORREOS_OFICIAL_VERSION,
			true
		);

		// Localizando variables para el primer script
		wp_localize_script(
			'co_ajax',
			'varsAjax',
			array()
		);

		$whereAmI = '';

		if (is_cart()) {
			$whereAmI = 'cart';
		} elseif (is_checkout()) {
			$whereAmI = 'checkout';
		}

		// Encolando el segundo script
		wp_enqueue_script(
			'co_ajax_wc',
			plugins_url('correosoficial/js/ajax_wc_checkout.js'),
			array(),
			CORREOS_OFICIAL_VERSION,
			true
		);

		// Localizando variables para el segundo script
		wp_localize_script(
			'co_ajax_wc',
			'varsAjax',
			array(
				'nonce' => wp_create_nonce('correosoficial_nonce'),
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'whereAmI' => $whereAmI,
			)
		);
	}

	public function hookdisplayCarrierExtraContent( $session_cart_params ) {
		return new CorreosOficialCarrierExtraContent($session_cart_params, $this->smarty);
	}

	public function displayAdminOrderBox() {
		if (!CorreosOficialUtils::sislogModuleIsActive()) {
			$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
			? wc_get_page_screen_id( 'shop-order' )
			: 'shop_order';

			// Meta box para mostrar el hook de pedidos
			add_meta_box(
				'correosoficial-order',
				'Correos Ecommerce',
				array( $this, 'correosoficialOrderMetaBox' ),
				$screen,
				'normal',
				'low'
			);
		} else if (CorreosOficialUtils::sislogModuleIsActive()) {
			$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
			? wc_get_page_screen_id( 'shop-order' )
			: 'shop_order';

			// Meta box para mostrar el hook de pedidos
			add_meta_box(
				'correosecomsga-order',
				'Correos Ecom SGA',
				array( $this, 'correosecomsgaOrderMetaBox' ),
				$screen,
				'normal',
				'low'
			);
		}
	}

	public function correosoficialOrderMetaBox() {
		include_once WP_PLUGIN_DIR . '/correosoficial/langs/orderLang.php';
		include_once __DIR__ . '/classes/CorreosOficialAdminOrderHook.php';

		$plugin_dir = WP_PLUGIN_DIR . '/correosoficial/';
		$google_api_key = CorreosOficialConfigDao::getConfigValue('GoogleMapsApi');

		// Carga de estilos
		wp_enqueue_style('co_jquery_datatables', plugins_url('views/commons/css/datatables/jquery.dataTables.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_bootstrap_min', plugins_url('views/commons/css/bootstrap.min.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_global', plugins_url('views/commons/css/global.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_admin-order', plugins_url('views/commons/css/admin-order.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_override_admin-order', plugins_url('override/css/admin-order.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');

		// Carga de scripts
		self::loadgeneralScripts();
		wp_enqueue_script('co_jquery_validate', plugins_url('views/js/jquery.validate.min.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		wp_enqueue_script('co_custom-validators', plugins_url('views/js/commons/common-settings.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		wp_enqueue_script('co_admin_order_library', plugins_url('views/js/library/admin-order.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		wp_enqueue_script('co_admin_order', plugins_url('js/admin-order.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		wp_enqueue_script('co_jquery_datatables', plugins_url('views/js/datatables/jquery.dataTables.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
				
		$google_api_key = CorreosOficialConfigDao::getConfigValue('GoogleMapsApi');
		if (!empty($google_api_key)) {
			wp_enqueue_script('co_maps', 'https://maps.googleapis.com/maps/api/js?callback=Function.prototype&key=' . $google_api_key, array(), CORREOS_OFICIAL_VERSION, true);
		}
		//Cargar variable varAjax.
		wp_localize_script(
			'co_admin_order',
			'varsAjax',
			array(
				'nonce' => wp_create_nonce('correosoficial_nonce'),
				'ajaxUrl' => admin_url('admin-ajax.php'),
			)
		);

		wp_enqueue_script(
			'co_ajax',
			plugins_url('correosoficial/views/js/commons/ajax.js'),
			array(),
			CORREOS_OFICIAL_VERSION,
			true
		);

		wp_enqueue_script(
			'co_ajax_wc',
			plugins_url('correosoficial/js/ajax_wc_admin_order.js'),
			array(),
			CORREOS_OFICIAL_VERSION,
			true
		);

		self::loadBootstrapScripts();
		$this->smarty->registerFilter('pre', 'Prefilter::preFilterConstants');

		return new CorreosOficialAdminOrderHook($this->smarty, $plugin_dir);
	}

	public function correosecomsgaOrderMetaBox() {
		include_once WP_PLUGIN_DIR . '/correosoficial/langs/orderLang.php';
		include_once __DIR__ . '/classes/CorreosOficialAdminOrderHook.php';
		$plugin_dir = WP_PLUGIN_DIR . '/correosoficial/';
		$sga_version = get_option('CORREOS_SGA_VERSION');

		// Carga de estilos
		wp_enqueue_style('co_jquery_datatables', plugins_url('views/commons/css/datatables/jquery.dataTables.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_bootstrap_min', plugins_url('views/commons/css/bootstrap.min.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_global', plugins_url('views/commons/css/global.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_admin-order', plugins_url('views/commons/css/admin-order.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_override_admin-order', plugins_url('override/css/admin-order.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');

		// Carga de scripts
		self::loadgeneralScripts();
		wp_enqueue_script('co_jquery_datatables', plugins_url('views/js/datatables/jquery.dataTables.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		wp_enqueue_script('co_jquery_validate',
		plugins_url('views/js/historic-table.js', WP_PLUGIN_DIR . '/correosecomsga/correosecomsga.php'), array(), $sga_version, true);

		//Cargar variable varAjax.
		wp_localize_script(
			'co_jquery_validate',
			'varsAjax',
			array(
				'nonce' => wp_create_nonce('correosoficial_nonce'),
				'ajaxUrl' => admin_url('admin-ajax.php'),
			)
		);

		self::loadBootstrapScripts();
		$this->smarty->registerFilter('pre', 'Prefilter::preFilterConstants');

		return new CorreosOficialAdminOrderHook($this->smarty, $plugin_dir);
	}

	public function menuCorreosOficial() {
		// $home = __('Home', 'correosoficial');
		$settings = __('Settings', 'correosoficial');
		$notifications = __('Notifications', 'correosoficial');


		add_menu_page('Correos Oficial ' . CORREOS_OFICIAL_VERSION, 'Correos Ecommerce', 'manage_options', 'correosoficial', array( $this, 'getContentSettings' ), plugins_url('correosoficial/views/commons/img/logos/correos_logo_white.svg'));
		// add_submenu_page('correosoficial', $home, $home, 'manage_options', 'home', array($this, 'getContentHome'));
		
		add_submenu_page('correosoficial', $settings, $settings, 'manage_options', 'settings', array( $this, 'getContentSettings' ));
		if (!CorreosOficialUtils::sislogModuleIsActive()) {
			$utilities = __('Utilities', 'correosoficial');
			add_submenu_page('correosoficial', $utilities, $utilities, 'manage_options', 'utilities', array( $this, 'getContentUtilities' ));
		}
		add_submenu_page('correosoficial', $notifications, $notifications, 'manage_options', 'notifications', array( $this, 'getContentNotifications' ));
	}

	public function adminMenuCSS() {
		wp_enqueue_style('co_menu', plugins_url('/override/css/menu.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
	}

	public function getContentHome() {
		if (isset($_GET['page']) && $_GET['page'] == 'home') {

			// Carga de estilos
			self::loadGeneralStyles();
			wp_enqueue_style('co_home', plugins_url('views/commons/css/home.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');

			// Carga de scripts
			self::loadBootstrapScripts();
			self::loadgeneralScripts();
			return new AdminCorreosOficialHomeController($this->smarty);
		}
	}

	public function getContentSettings() {
		if (isset($_GET['page']) && ( $_GET['page'] == 'settings' || $_GET['page'] == 'correosoficial' )) {

			// Carga de estilos
			self::loadGeneralStyles();
			wp_enqueue_style('co_settings', plugins_url('views/commons/css/settings.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
			wp_enqueue_style('co_override_settings', plugins_url('/override/css/settings.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');

			// Carga de scriptsp
			self::loadBootstrapScripts();
			wp_enqueue_script('co_jquery_validate', plugins_url('views/js/jquery.validate.min.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
			wp_enqueue_script('co_custom-validators', plugins_url('views/js/commons/custom-validators.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
			wp_enqueue_script('co_back', plugins_url('js/back.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
						
			self::loadgeneralScripts();
			return new AdminCorreosOficialSettingsController($this->smarty);
		}
	}

	public function getContentUtilities() {
		if (isset($_GET['page']) && $_GET['page'] == 'utilities') {

			//Optimizador DataTable
			$this->dataTableRegisterAjax();

			// Carga de estilos
			self::loadGeneralStyles();
			wp_enqueue_style('co_utilities', plugins_url('views/commons/css/utilities.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
			wp_enqueue_style('co_override_utilities', plugins_url('/override/css/utilities.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');

			// Carga de scripts
			self::loadBootstrapScripts();
			wp_enqueue_script('co_back', plugins_url('js/back.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
						
			self::loadgeneralScripts();
			return new AdminCorreosOficialUtilitiesController($this->smarty);
		}
	}

	public function getContentNotifications() {
		if (isset($_GET['page']) && $_GET['page'] == 'notifications') {

			// Carga de estilos
			self::loadGeneralStyles();
			wp_enqueue_style('co_notifications', plugins_url('views/commons/css/notifications.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');

			// Carga de scripts
			self::loadBootstrapScripts();
			wp_enqueue_script('co_notifications', plugins_url('js/notifications.js', __FILE__), array( 'jquery' ), CORREOS_OFICIAL_VERSION, true);

			wp_localize_script(
				'co_notifications',
				'notificationsVar',
				array(
					'correos_inView_check' => __('Mark as ready and discart', 'correosoficial'),
					'gdpr_nonce' => wp_create_nonce( 'gdpr_nonce' ),
				)
			);

			self::loadgeneralScripts();
			return new AdminCorreosOficialNotificationsController($this->smarty);
		}
	}

	public static function loadGeneralStyles() {
		wp_enqueue_style('co_all', plugins_url('views/commons/css/all.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
		wp_enqueue_style('co_global', plugins_url('/views/commons/css/global.css', __FILE__), array(), CORREOS_OFICIAL_VERSION, 'all');
	}

	public static function loadgeneralScripts() {
		wp_enqueue_script('co_woocommerce', plugins_url('js/woocommerce.js', __FILE__), array(), CORREOS_OFICIAL_VERSION, true);
		self::definePluginURLS();
	}

	public static function loadBootstrapScripts() {
		wp_enqueue_script('co_popper_min', plugins_url('views/js/popper.min.js', __FILE__), array(), '2.9.2', false);
		wp_enqueue_script('co_bootstrap_min', plugins_url('views/js/bootstrap.min.js', __FILE__), array(), '5.0.2', false);
	}

	public function getModuleVersion() {
		$configFile = file_get_contents($this->getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/config.xml');
		$module = new SimpleXMLElement($configFile);
		return (string) $module->version;
	}

	// Funciones auxiliares
	public function getRealPath( $file ) {
		return dirname(realpath($file));
	}

	// Método install de las tablas de la Base de Datos
	public static function installTables() {
		global $wpdb;
		$installTablesLockDir = sys_get_temp_dir() . '/' . $wpdb->dbname;
		$installTablesLockFile = $installTablesLockDir . '/correosoficial_' . CORREOS_OFICIAL_VERSION . '.lock';
		/**
		 * Retornamos si ya se ha actualizado
		 */
		if (file_exists($installTablesLockFile)) {
			return;
		}

		// Tareas Cron
		self::createCronTasks();

		global $ShowShippingStatusProcessFlag;
		global $IdOrderFlag;

		$dao = new CorreosOficialDAO();

		$sql = 'CREATE TABLE IF NOT EXISTS ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_install(
            id int(11) NOT NULL AUTO_INCREMENT,
            installed varchar(50) NOT NULL,
            PRIMARY KEY (id)
          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';

		$dao->executeQuery($sql);

		$record = $dao->readRecord('correos_oficial_install', "WHERE id='1'");

		if (!count($record)) {
			$sql = 'INSERT INTO ' . CorreosOficialUtils::getPrefix() . "correos_oficial_install (id, installed) VALUES (1, '')";
			$dao->executeQuery($sql);
		}

		// Para versiones menores o igual que 1.0.0.0
		$query = "SELECT count(name) FROM {$wpdb->prefix}correos_oficial_configuration WHERE name='ShowShippingStatusProcess'";

		$query2 = "SELECT count(postcode) FROM {$wpdb->prefix}correos_oficial_postcodes";
		$query3 = "SHOW COLUMNS FROM {$wpdb->prefix}correos_oficial_requests LIKE 'id_order';";

		// phpcs:disable
		if (!self::areTablesIntalled()) {
			include_once __DIR__ . '/install.php';
			include_once __DIR__ . '/upgrade.php';
		} elseif ($wpdb->get_var($query) == 0) { // Si no existe ShowShippingStatusProcess
			$ShowShippingStatusProcessFlag = false;
			include_once __DIR__ . '/upgrade.php';
		} elseif (is_null($wpdb->get_var($query3))) { // Si no existe id_order de la tabla correos_oficial_requests
			$IdOrderFlag = true;
			include_once __DIR__ . '/upgrade.php';
		}

		// Actualización de productos
		include_once __DIR__ . '/upgrade/update_1_3_5_0.php';
		include_once __DIR__ . '/upgrade/update_1_6_0.php';

		// Operaciones multicliente
		include_once __DIR__ . '/upgrade/update_1_7_0.php';

		// Operaciones evolutivo dic
		include_once __DIR__ . '/upgrade/update_1_8_0.php';

		// Operaciones Evolutivo ID00014670
		include_once __DIR__ . '/upgrade/update_1_8_2.php';

		// Operaciones Evolutivo ID00014670 - RN60
		include_once __DIR__ . '/upgrade/update_1_8_3.php';

		// FIXES RN-60
		include_once dirname(__FILE__) . '/upgrade/update_1_8_4.php';

		// ADD Indices para consultas en utilidades
		include_once dirname(__FILE__) . '/upgrade/update_1_8_6.php';

		if ($wpdb->get_var($query2) == 0) {
			include_once __DIR__ . '/sql/postcodes.php';
		}
		// phpcs:enable
		// Creamos directorio de bloqueo
		if (!file_exists($installTablesLockDir)) {
			mkdir($installTablesLockDir, 0777, true);
		}
		// Generamos fichero de bloqueo
		file_put_contents($installTablesLockFile, 'locking');

		/**
		 * Se elimina fichero de bloqueo antiguo
		 */
		foreach (glob($installTablesLockDir . '/correosoficial*.lock') as $old) {
			if ($old != $installTablesLockFile) {
				unlink($old);
			}
		}
	}

	public static function areTablesIntalled() {
		$dao = new CorreosOficialDao();
		$record = $dao->readRecord('correos_oficial_install', "WHERE id='1'");
		return $record[0]->installed;
	}

	/**
	 * Hook Detalles del usuario
	 */
	public function hookOrderDetailDisplayed( $order ) {
		include_once __DIR__ . '/vendor/ecommerce_common_lib/Dao/CorreosOficialOrderDao.php';

		global $co_module_url;
		global $co_page;
		$items = array();
		$co_page = 'my_account';

		include_once WP_PLUGIN_DIR . '/correosoficial/langs/orderDetailLang.php';

		$order_id = $order->get_id();

		$items = $order->get_items('shipping');

		/*
		 * Salimos si no es transportista de correos_oficial
		 */
		if (!count($items) || reset($items)->get_method_id() != 'request_shipping_quote') {
			return false;
		}

		$saved_order = new CorreosOficialOrderDao();
		$saved_order_record = $saved_order->getShippingNumberByOrderId($order_id);

		// Salimos si el envío todavía no se ha prerregistrado.
		if (!isset($saved_order_record[0])) {
			return false;
		}

		$shipping_number = $saved_order_record[0]->shipping_number;

		$this->smarty->assign('co_base_dir', $co_module_url);
		$this->smarty->assign('shipping_number', $shipping_number);
		$this->smarty->registerFilter('pre', 'Prefilter::preFilterConstants');

		return $this->smarty->display(self::getRealPath(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/hook/OrderDetail.tpl');
	}

	public static function checkPHPversionCompatibility() {
		if (version_compare(phpversion(), '7.2', '<')) {
			$out = 'Versión de plugin ' . CORREOS_OFICIAL_VERSION . '. Este plugin necesita PHP7.2+ o PHP8.0+';
			wp_die(esc_html($out));
		}

		if (version_compare(phpversion(), '9', '>=')) {
			$out = 'Versión de plugin ' . CORREOS_OFICIAL_VERSION . '. Este plugin de CorreosEcommerce no es compatible con la versión ' . phpversion() . ' de PHP';
			wp_die(esc_html($out));
		}
	}

	public static function definePluginURLS() {
		wp_localize_script(
			'co_woocommerce',
			'woocommerceVars',
			array(
				'pluginsUrl' => plugins_url(),
				'adminUrl' => get_admin_url(),
			)
		);
	}

	//Optimizador DataTable
	public function dataTableRegisterAjax() {
		wp_enqueue_script('dataTableAjax', plugins_url('/js/ajax_wc_utilities.js', __FILE__), array( 'jquery' ), CORREOS_OFICIAL_VERSION, true);

		// Pasar datos a JavaScript
		wp_localize_script('dataTableAjax', 'dataTableVars', array(
			'dataTableNonce' => wp_create_nonce('dataTableNonce'),
			'dataTableurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('correosoficial_nonce'),
			'ajaxUrl' => admin_url('admin-ajax.php'),
		));
	}

	/**
	 * Eliminamos un pedido si ha sido eliminado permanentemente de Woocommerce->Pedidos
	 */
	public function deleteOrder( $postid ) {
		// Verificamos si el post que se esta eliminando es un pedido
		if (get_post_type($postid) == 'shop_order') {
			CorreosOficialOrder::deleteOrder($postid);
		}
	}

	/**
	 * Tareas de asignación de transportista a los pedidos que entran por Channable
	 */
	public function channableTasks( $item_id, $item, $id_order ) {

		global $wpdb;

		// Comprobamos si el item es un objeto de tipo WC_Order_Item_Shipping
		if ( ! $item instanceof WC_Order_Item_Shipping ) {
			return;
		}

		// Expresión regular para comprobar si el nombre del item tiene la palabra "Amazon"
		$automaticProductAssignmentText = CorreosOficialConfigDao::getConfigValue('AutomaticProductAssignmentText');
		$pattern = '/' . $automaticProductAssignmentText . '/i';

		$productId = CorreosOficialConfigDao::getConfigValue('AutomaticProductAssignmentProduct');

		if (!$productId) {
			return;
		}

		$objProduct = new CorreosOficialProductsDao();
		$product = $objProduct->getProduct($productId, 'correos_oficial_products');

		if (!empty($product)) {
			$productName = $product[0]->name;

			// Comprobamos si el nombre del item contiene la palabra guardada
			if (preg_match($pattern, $item->get_name())) {
				// Si el nombre del item contiene la palabra guardada, añadimos una nota al pedido
				$order = new WC_Order($id_order);
				$order->add_order_note('El módulo Correos Ecommerce ha cambiado el transportista.');

				//$carrier_order = CorreosOficialCarrier::getCarrierByProductId($productId);

				$instanceId = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT instance_id FROM %i WHERE method_id=%s',
						CorreosOficialUtils::getPrefix() . 'woocommerce_shipping_zone_methods',
						'request_shipping_quote_' . $productId
					)
				)[0]->instance_id;

				// Añadimos método de envío
				$shipping_method = new WC_Order_Item_Shipping($item_id);
				$shipping_method->set_method_title($productName . ' (' . $item->get_name() . ')');
				$shipping_method->set_method_id("request_shipping_quote_{$productId}:{$instanceId}");
				$shipping_method->save();


				//guardamos en el log
				$filename = WP_PLUGIN_DIR . '/correosoficial/log/log_automatic_product_assignment.txt';
				file_put_contents(
					$filename,
					gmdate('Y-m-d H:i:s') . " Pedido con Id {$id_order} Se ha asignado automáticamente el transportista de origen '{$automaticProductAssignmentText}' al producto '{$productName}'\r\n",
					FILE_APPEND
				);

			}
		}
	}
}

$co = new CorreosOficial();
