<?php
/**
 * Plugin Name: WooCommerce Email Template Customizer Premium
 * Plugin URI: https://villatheme.com/extensions/woocommerce-email-template-customizer/
 * Description: Make your WooCommerce emails become professional.
 * Version: 1.2.8
 * Author: VillaTheme
 * Author URI: https://villatheme.com
 * Text Domain: viwec-email-template-customizer
 * Domain Path: /languages
 * Copyright 2020-2024 VillaTheme.com. All rights reserved.
 * Requires at least: 5.0
 * Tested up to: 6.7
 * WC requires at least: 7.0.0
 * WC tested up to: 9.4
 * Requires PHP: 7.0
 * Requires Plugins: woocommerce
 **/

use VIWEC\INCLUDES\Email_Samples;
use VIWEC\INCLUDES\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!defined('VIWEC_VER')) {
	define( 'VIWEC_VER', '1.2.8' );
	define( 'VIWEC_NAME', 'WooCommerce Email Template Customizer' );
	define( 'VIWEC_SLUG', 'woocommerce-email-template-customizer' );
	define( 'VIWEC_DIR', plugin_dir_path( __FILE__ ) );
	define( 'VIWEC_INCLUDES', VIWEC_DIR . "includes" . DIRECTORY_SEPARATOR );
	define( 'VIWEC_SUPPORT', VIWEC_INCLUDES . "support" . DIRECTORY_SEPARATOR );
	define( 'VIWEC_TEMPLATES', VIWEC_INCLUDES . "templates" . DIRECTORY_SEPARATOR );
	define( 'VIWEC_LANGUAGES', VIWEC_DIR . "languages" . DIRECTORY_SEPARATOR );
	define( 'VIWEC_CSS', plugin_dir_url( __FILE__ ) . "assets/css/" );
	define( 'VIWEC_JS', plugin_dir_url( __FILE__ ) . "assets/js/" );
	define( 'VIWEC_IMAGES', plugin_dir_url( __FILE__ ) . "assets/img/" );
}
global $wpdb;
define( 'VIWEC_VIEW_PRODUCT_TB', $wpdb->prefix . 'viwec_clicked' );
require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

if ( ! class_exists( 'WooCommerce_Email_Template_Customizer' ) ) {
	class WooCommerce_Email_Template_Customizer {

		public $err_message;
		public $wp_version_require = '5.0';
		public $wc_version_require = '7.0';
		public $php_version_require = '7.0';

		public function __construct() {
			register_activation_hook( __FILE__, array( $this, 'viwec_activate' ) );

			add_action( 'plugins_loaded', function () {
				include_once VIWEC_SUPPORT . 'support.php';
				$environment = new \VillaTheme_Require_Environment( [
						'plugin_name'     => 'WooCommerce Email Template Customizer Premium',
						'php_version'     => $this->php_version_require,
						'wp_version'      => $this->wp_version_require,
						'require_plugins' => [
							[
								'slug' => 'woocommerce',
								'name' => 'WooCommerce',
								'file' => 'woocommerce/woocommerce.php',
								'version' => $this->wc_version_require,
							],
						]
					]
				);

				if ( $environment->has_error() ) {
					return;
				}

				if ( is_file( VIWEC_INCLUDES . 'init.php' ) ) {
					require_once VIWEC_INCLUDES . 'init.php';
				}

				//compatible with 'High-Performance order storage (COT)'
				add_action( 'before_woocommerce_init', array( $this, 'before_woocommerce_init' ) );

				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_actions_link' ) );
				add_action( 'init', [ $this, 'viwec_init' ] );
				add_action( 'init', array( $this, 'support_pro' ) );
			} );

		}
		public function viwec_init() {
			$check_exist = get_posts( [ 'post_type' => 'viwec_template', 'numberposts' => 1 ] );
			$check_default = get_option( 'viwec_email_default_setting', false );
			if ( empty( $check_exist ) && !$check_default) {
				$site_title      = get_option( 'blogname' );
				$default_subject = Email_Samples::default_subject();

				$header = Email_Samples::sample_header();
				$footer = Email_Samples::sample_footer();

				$header_id = Utils::insert_block( $header, 'Header' );
				$footer_id = Utils::insert_block( $footer, 'Footer' );

				$templates = Email_Samples::sample_templates( $header_id, $footer_id );

				if ( empty( $templates ) || ! is_array( $templates ) ) {
					return;
				}

				foreach ( $templates as $key => $template ) {
					$args     = [
						'post_title'  => $default_subject[ $key ] ? str_replace( '{site_title}', $site_title, $default_subject[ $key ] ) : '',
						'post_status' => 'publish',
						'post_type'   => 'viwec_template',
					];
					$post_id  = wp_insert_post( $args );
					$template = $template['basic']['data'];
					$template = str_replace( '\\', '\\\\', $template );
					update_post_meta( $post_id, 'viwec_settings_type', $key );
					update_post_meta( $post_id, 'viwec_email_structure', $template );
				}

				update_option( 'viwec_email_update_button', true, 'no' );
				update_option( 'viwec_email_default_setting', true, 'no' );
			}
		}
		public function before_woocommerce_init() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
			}
		}


		public function viwec_activate() {

			$this->create_table();
		}

		public function create_table() {
			global $wpdb;
			$collate = '';

			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$table_name = VIWEC_VIEW_PRODUCT_TB;

			$query = "CREATE TABLE IF NOT EXISTS {$table_name} (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `clicked_date` int(11) NOT NULL,
                             `product` int(11) NOT NULL,
                             PRIMARY KEY  (`id`)
                             ) {$collate}";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $query );
		}

		public function support_pro() {

			if ( class_exists( 'VillaTheme_Support_Pro' ) ) {
				new \VillaTheme_Support_Pro(
					array(
						'support'   => 'https://villatheme.com/supports/forum/plugins/',
						'docs'      => 'https://docs.villatheme.com/?item=woo-email-template-customizer',
						'review'    => 'https://codecanyon.net/downloads',
						'css'       => VIWEC_CSS,
						'image'     => VIWEC_IMAGES,
						'slug'      => 'woocommerce-email-template-customizer',
						'menu_slug' => 'edit.php?post_type=viwec_template',
						'version'   => VIWEC_VER
					)
				);
			}
		}

		public function plugin_actions_link( $links ) {
			if ( ! $this->err_message ) {
				$settings_link = '<a href="' . admin_url( 'edit.php?post_type=viwec_template' ) . '">' . esc_html__( 'Settings', 'viwec-email-template-customizer' ) . '</a>';
				array_unshift( $links, $settings_link );
			}

			return $links;
		}
	}

	new WooCommerce_Email_Template_Customizer();
}