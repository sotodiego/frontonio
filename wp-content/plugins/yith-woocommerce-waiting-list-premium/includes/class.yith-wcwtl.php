<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWTL' ) ) {
	/**
	 * YITH WooCommerce Waiting List
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWTL {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCWTL
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WCWTL_VERSION;

		/**
		 * Plugin emails array
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $emails = array();


		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WCWTL
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->init_plugin_emails_array();
			$this->load_requested();

			// Load Plugin Framework
			add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );
			// Register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// Email actions
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );
			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );
			// Register plugin account endpoint
			add_filter( 'init', array( $this, 'add_endpoint' ), 0 );

			// gdpr actions
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporters' ) );
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_erasers' ) );
		}

		/**
		 * Include and load requested file/class
		 *
		 * @since 1.9.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function load_requested() {

			// Class admin
			if ( $this->is_admin() ) {
				// required class
				include_once 'class.yith-wcwtl-admin.php';
				include_once 'class.yith-wcwtl-admin-premium.php';
				YITH_WCWTL_Admin_Premium();

				// compatibility with multi vendor
				$enabled_for_vendor = get_option( 'yith_wpv_vendors_option_waiting_list_management', 'no' ) == 'yes';
				if ( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM && $enabled_for_vendor ) {
					// required class
					include_once 'compatibility/yith-woocommerce-product-vendors.php';
					YITH_WCWTL_Multivendor();
				}
			}

			if ( 'yes' === get_option( 'yith-wcwtl-enable' ) ) {

				include_once 'class.yith-wcwtl-mailer.php';
				YITH_WCWTL_Mailer();

				if( $this->is_admin() ) {
					include_once 'class.yith-wcwtl-meta.php';
					YITH_WCWTL_Meta();
				}
				elseif( $this->load_frontend() ) {
					// required class
					include_once 'class.yith-wcwtl-frontend.php';
					// Class frontend
					YITH_WCWTL_Frontend();
				}
			}
		}

		/**
		 * Init an array of plugin emails
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 */
		public function init_plugin_emails_array() {
			$this->emails = apply_filters( 'yith_wcwtl_plugin_emails_array', array(
				'YITH_WCWTL_Mail_Instock',
				'YITH_WCWTL_Mail_Subscribe_Optin',
				'YITH_WCWTL_Mail_Subscribe',
				'YITH_WCWTL_Mail_Admin',
			) );
		}

		/**
		 * Get plugin emails array
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_emails() {
			return $this->emails;
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Check if is admin
		 *
		 * @since  1.1.0
		 * @access public
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function is_admin() {
			$context_check    = isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend';
			$actions_to_check = apply_filters( 'yith_wcwtl_actions_to_check_admin', array(
				'jckqv',
			) );
			$action_check     = isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $actions_to_check );
			$is_admin         = is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( $context_check || $action_check ) );

			return apply_filters( 'yith_wcwtl_check_is_admin', $is_admin );
		}

		/**
		 * Check to load frontend class
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function load_frontend() {
			return apply_filters( 'yith_wcwtl_check_load_frontend', get_option( 'yith-wcwtl-enable' ) == 'yes' );
		}

		/**
		 * Filters woocommerce available mails, to add waitlist related ones
		 *
		 * @since 1.0
		 * @param $emails array
		 *
		 * @return array
		 */
		public function add_woocommerce_emails( $emails ) {
			// load common class
			include( 'email/class.yith-wcwtl-mail.php' );

			foreach ( $this->emails as $email ) {
				$file_name        = strtolower( str_replace( '_', '-', $email ) );
				$emails[ $email ] = include "email/class.{$file_name}.php";
			}

			return $emails;
		}

		/**
		 * Loads WC Mailer when needed
		 *
		 * @since  1.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.it>
		 * @return void
		 */
		public function load_wc_mailer() {
			foreach ( $this->emails as $email ) {
				$email = str_replace( 'wcwtl', 'waitlist', strtolower( $email ) );
				add_action( 'send_' . $email, array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );
			}
		}

		/**
		 * Add waiting list account endpoint
		 *
		 * @since  1.1.2
		 * @author Francesco Licandro
		 * @access public
		 */
		public function add_endpoint() {
			WC()->query->query_vars['waiting-list'] = get_option( 'woocommerce_myaccount_waiting_list_endpoint', 'waiting-list' );
		}

		/**
		 * Register exporter for GDPR compliance
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param array $exporters List of exporter callbacks.
		 * @return array
		 */
		public function register_exporters( $exporters = array() ) {
			$exporters['yith-wcwtl-customer-data'] = array(
				'exporter_friendly_name' => __( 'Waiting List Data', 'yith-woocommerce-waiting-list' ),
				'callback'               => array( 'YITH_WCWTL', 'customer_data_exporter' ),
			);

			return $exporters;
		}

		/**
		 * GDPR exporter callback
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param string $email_address The user email address.
		 * @param int    $page          Page.
		 * @return array
		 */
		public static function customer_data_exporter( $email_address, $page ) {
			$user           = get_user_by( 'email', $email_address );
			$data_to_export = array();
			$products_list  = array();
			// get products list if any
			( $user instanceof WP_User ) && $products_list = yith_get_user_waitlists( $user->ID );

			if ( ! empty( $products_list ) ) {

				$products = array();
				foreach ( $products_list as $product_id ) {
					$product = wc_get_product( $product_id );
					$product && $products[] = $product->get_name();
				}

				$data_to_export[] = array(
					'group_id'    => 'yith_wcwtl_data',
					'group_label' => __( 'Waiting List Data', 'yith-woocommerce-waiting-list' ),
					'item_id'     => 'waiting-list',
					'data'        => array(
						array(
							'name'  => __( 'Waiting List Subscriptions', 'yith-woocommerce-waiting-list' ),
							'value' => implode( ', ', $products ),
						),
					),
				);
			}

			return array(
				'data' => $data_to_export,
				'done' => true,
			);
		}

		/**
		 * Register ereaser for GDPR compliance
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param array $erasers List of erasers callbacks.
		 * @return array
		 */
		public function register_erasers( $erasers = array() ) {
			$erasers['yith-wcwtl-customer-data'] = array(
				'eraser_friendly_name' => __( 'Waiting List Data', 'yith-woocommerce-waiting-list' ),
				'callback'             => array( 'YITH_WCWTL', 'customer_data_ereaser' ),
			);

			return $erasers;
		}

		/**
		 * GDPR ereaser callback
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param string $user_email The user email
		 * @param int    $page
		 * @return array
		 */
		public static function customer_data_ereaser( $user_email, $page ) {
			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);

			$user = get_user_by( 'email', $user_email ); // Check if user has an ID in the DB to load stored personal data.
			if ( ! $user instanceof WP_User ) {
				return $response;
			}

			$products_list = yith_get_user_waitlists( $user->ID );
			foreach ( $products_list as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product && yith_waitlist_unregister_user( $user_email, $product ) ) {
					$response['messages'][]    = sprintf( __( 'Removed customer from waiting list for "%s"', 'woocommerce' ), $product->get_name() );
					$response['items_removed'] = true;
				}
			}

			return $response;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCWTL_DIR . 'plugin-fw/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WCWTL_INIT, YITH_WCWTL_SECRET_KEY, YITH_WCWTL_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WCWTL_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_WCWTL_SLUG, YITH_WCWTL_INIT );
		}
	}
}

/**
 * Unique access to instance of YITH_WCWTL class
 *
 * @since 1.0.0
 * @return \YITH_WCWTL
 */
function YITH_WCWTL() {
	return YITH_WCWTL::get_instance();
}