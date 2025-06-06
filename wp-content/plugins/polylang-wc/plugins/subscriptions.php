<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce subscriptions.
 * Version tested: 2.2.19.
 *
 * @since 0.4
 */
class PLLWC_Subscriptions {

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 0.4
	 */
	public function __construct() {
		add_filter( 'pllwc_copy_post_metas', array( $this, 'copy_post_metas' ) );

		// Add languages to the subscriptions, similar to orders.
		add_filter( 'pllwc_get_order_types', array( $this, 'translate_types' ) );

		// Renewal and Resubscribe.
		add_filter( 'wcs_new_order_created', array( $this, 'new_order_created' ), 10, 2 );

		// After `Polylang_Woocommerce::custom_order_tables_init()`, to make sure `PLLWC()->admin_orders` is set.
		add_action( 'pllwc_declare_compatibility_custom_order_tables', array( $this, 'custom_order_tables_init' ), 20 );

		if ( version_compare( $GLOBALS['wp_version'], '6.7-beta' ) < 0 ) {
			// Backward compatibility with WP < 6.7.
			add_action( 'change_locale', array( $this, 'change_locale' ) );
		}

		if ( PLL() instanceof PLL_Frontend ) {
			add_action( 'parse_query', array( $this, 'parse_query' ), 3 ); // Before Polylang.
		}

		// Strings translations.
		add_filter( 'pll_sanitize_string_translation', array( $this, 'sanitize_strings' ), 10, 3 );
		add_action( 'init', array( $this, 'register_strings' ) );

		// Endpoints.
		add_filter( 'pll_translation_url', array( $this, 'pll_translation_url' ), 10, 2 );

		// Check if a user has a subscription.
		add_filter( 'wcs_user_has_subscription', array( $this, 'user_has_subscription' ), 10, 4 );
		add_filter( 'woocommerce_get_subscriptions_query_args', array( $this, 'get_subscriptions_query_args' ), 10, 2 );

		// Variable subscription products.
		add_action( 'wp_trash_post', array( $this, 'delete_variation' ) );
		add_action( 'before_delete_post', array( $this, 'delete_variation' ) );

		// Work around endpoints options added in wpml-config.xml :/.
		remove_filter( 'option_woocommerce_myaccount_subscriptions_endpoint', array( PLL_WPML_Config::instance(), 'translate_strings' ) );
		remove_filter( 'option_woocommerce_myaccount_view_subscription_endpoint', array( PLL_WPML_Config::instance(), 'translate_strings' ) );

		// Add e-mails for translation.
		add_filter( 'pllwc_order_email_actions', array( $this, 'filter_order_email_actions' ) );
	}

	/**
	 * Add Subscription e-mails in the translation mechanism.
	 *
	 * @since 1.6
	 *
	 * @param string[] $actions Array of actions used to send emails.
	 * @return string[]
	 */
	public function filter_order_email_actions( $actions ) {
		return array_merge(
			$actions,
			array(
				// Cancelled subscription.
				'cancelled_subscription_notification',
				// Customer completed order.
				'woocommerce_order_status_completed_renewal_notification',
				// Customer Completed Switch Order.
				'woocommerce_order_status_completed_switch_notification',
				// Customer renewal order.
				'woocommerce_order_status_pending_to_processing_renewal_notification',
				'woocommerce_order_status_pending_to_on-hold_renewal_notification',
				// Customer renewal invoice.
				'woocommerce_generated_manual_renewal_order_renewal_notification',
				'woocommerce_order_status_failed_renewal_notification',
				// Expired subscription.
				'expired_subscription_notification', // Since WCS 2.1.
				// New order (to the shop).
				'woocommerce_order_status_pending_to_processing_renewal_notification',
				'woocommerce_order_status_pending_to_completed_renewal_notification',
				'woocommerce_order_status_pending_to_on-hold_renewal_notification',
				'woocommerce_order_status_failed_to_processing_renewal_notification',
				'woocommerce_order_status_failed_to_completed_renewal_notification',
				'woocommerce_order_status_failed_to_on-hold_renewal_notification',
				// Switch order (to the shop).
				'woocommerce_order_status_pending_to_processing_switch_notification',
				'woocommerce_order_status_pending_to_completed_switch_notification',
				'woocommerce_order_status_pending_to_on-hold_switch_notification',
				'woocommerce_order_status_failed_to_processing_switch_notification',
				'woocommerce_order_status_failed_to_completed_switch_notification',
				'woocommerce_order_status_failed_to_on-hold_switch_notification',
				// Suspended Subscription.
				'on-hold_subscription_notification', // Since WCS 2.1.
			)
		);
	}

	/**
	 * Copies or synchronizes metas.
	 * Hooked to the filter 'pllwc_copy_post_metas'.
	 *
	 * @since 0.4
	 *
	 * @param array $keys List of custom fields names.
	 * @return array
	 */
	public function copy_post_metas( $keys ) {
		$wcs_keys = array(
			'_subscription_payment_sync_date',
			'_subscription_length',
			'_subscription_limit',
			'_subscription_period',
			'_subscription_period_interval',
			'_subscription_price',
			'_subscription_sign_up_fee',
			'_subscription_trial_length',
			'_subscription_trial_period',
		);
		return array_merge( $keys, $wcs_keys );
	}

	/**
	 * Language and translation management for the subscriptions post type.
	 * Hooked to the filter 'pllwc_get_order_types'.
	 *
	 * @since 0.4
	 *
	 * @param array $types List of post type names for which Polylang manages language and translations.
	 * @return array List of post type names for which Polylang manages language and translations.
	 */
	public function translate_types( $types ) {
		return array_merge( $types, array( 'shop_subscription' ) );
	}

	/**
	 * Assigns the order language when it is created from a subscription.
	 * Hooked to the filter 'wcs_new_order_created'.
	 *
	 * @since 0.4.4
	 *
	 * @param object $new_order    New order.
	 * @param object $subscription Parent subscription.
	 * @return object Unmodified order
	 */
	public function new_order_created( $new_order, $subscription ) {
		if ( $lang = pll_get_post_language( $subscription->get_id() ) ) {
			$data_store = PLLWC_Data_Store::load( 'order_language' );
			$data_store->set_language( $new_order->get_id(), $lang );
		}
		return $new_order;
	}

	/**
	 * Reloads Subscription translations in emails.
	 * Hooked to the action 'change_locale'.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function change_locale() {
		if ( class_exists( 'WC_Subscriptions_Core_Plugin' ) && method_exists( WC_Subscriptions_Core_Plugin::instance(), 'load_plugin_textdomain' ) ) {
			WC_Subscriptions_Core_Plugin::instance()->load_plugin_textdomain();
		}
	}

	/**
	 * Registers string translations.
	 *
	 * @since 2.1.4
	 *
	 * @return void
	 */
	public function register_strings(): void {
		$options = array(
			'add_to_cart_button_text' => __( 'Add to Cart Button Text', 'polylang-wc' ),
			'order_button_text'       => __( 'Place Order Button Text', 'polylang-wc' ),
			'switch_button_text'      => __( 'Switch Button Text', 'polylang-wc' ),
		);

		foreach ( $options as $option => $name ) {
			if ( PLL() instanceof PLL_Frontend ) {
				add_filter( 'option_woocommerce_subscriptions_' . $option, 'pll__' );
				continue;
			}

			if ( ! PLL() instanceof PLL_Admin_Base ) {
				continue;
			}

			$string = get_option( 'woocommerce_subscriptions_' . $option );

			if ( empty( $string ) ) {
				continue;
			}

			pll_register_string( $name, $string, 'WooCommerce Subscriptions' );
		}
	}

	/**
	 * Translated strings must be sanitized the same way WooCommerce Subscriptions does before they are saved.
	 * Hooked to the filter 'pll_sanitize_string_translation'.
	 *
	 * @since 0.4
	 *
	 * @param string $translation A string translation.
	 * @param string $name        The string name.
	 * @param string $context     The group the string belongs to.
	 * @return string Sanitized translation
	 */
	public function sanitize_strings( $translation, $name, $context ) {
		if ( 'WooCommerce Subscriptions' === $context ) {
			$translation = wp_kses_post( trim( $translation ) );
		}
		return $translation;
	}

	/**
	 * Disables the language filter for a customer to see all his/her subscriptions whatever the languages.
	 * Hooked to the action 'parse_query'.
	 *
	 * @since 0.4
	 *
	 * @param WP_Query $query WP_Query object.
	 * @return void
	 */
	public function parse_query( $query ) {
		$qvars = $query->query_vars;

		// Customers should see all their subscriptions whatever the language.
		if ( isset( $qvars['post_type'] ) && ( 'shop_subscription' === $qvars['post_type'] || ( is_array( $qvars['post_type'] ) && in_array( 'shop_subscription', $qvars['post_type'] ) ) ) ) {
			$query->set( 'lang', 0 );
		}
	}

	/**
	 * Returns the translation of the current url.
	 * Handles the translations of the Subscriptions endpoints slugs.
	 * Hooked to the filter 'pll_translation_url'.
	 *
	 * @since 0.4
	 *
	 * @param string $url  URL of the translation, to modify.
	 * @param string $lang Language slug.
	 * @return string
	 */
	public function pll_translation_url( $url, $lang ) {
		if ( $url && defined( 'POLYLANG_PRO' ) && POLYLANG_PRO && get_option( 'permalink_structure' ) ) {
			$wcs_query = pll_get_anonymous_object_from_filter( 'init', array( 'WCS_Query', 'add_endpoints' ) );

			if ( is_object( $wcs_query ) ) {
				$endpoint = $wcs_query->get_current_endpoint();

				if ( $endpoint && isset( $wcs_query->query_vars[ $endpoint ] ) ) {
					$language = PLL()->model->get_language( $lang );
					$url      = PLL()->translate_slugs->slugs_model->switch_translated_slug( $url, $language, 'wc_' . $wcs_query->query_vars[ $endpoint ] );
				}
			}
		}

		return $url;
	}

	/**
	 * Checks if a user has a subscription to a translated product.
	 * Hooked to the filter 'wcs_user_has_subscription'.
	 *
	 * @since 0.9.2
	 *
	 * @param bool  $has_subscription Whether WooCommerce Subscriptions found a subscription.
	 * @param int   $user_id          The ID of a user in the store.
	 * @param int   $product_id       The ID of a product in the store.
	 * @param mixed $status           Subscription status.
	 * @return bool
	 */
	public function user_has_subscription( $has_subscription, $user_id, $product_id, $status ) {
		if ( false === $has_subscription && ! empty( $product_id ) ) {
			$data_store = PLLWC_Data_Store::load( 'product_language' );
			foreach ( wcs_get_users_subscriptions( $user_id ) as $subscription ) {
				if ( empty( $status ) || 'any' === $status || $subscription->has_status( $status ) ) {
					foreach ( $data_store->get_translations( $product_id ) as $tr_id ) {
						if ( $subscription->has_product( $tr_id ) ) {
							$has_subscription = true;
							break 2;
						}
					}
				}
			}
		}
		return $has_subscription;
	}

	/**
	 * When querying subscriptions and no subscriptions have been found for the current product,
	 * checks if there are subscriptions for the translated products.
	 * Hooked to the filter 'woocommerce_get_subscriptions_query_args'.
	 *
	 * @since 1.2
	 *
	 * @param array $query_args WP_Query() arguments.
	 * @param array $args       Arguments of wcs_get_subscriptions().
	 * @return array
	 */
	public function get_subscriptions_query_args( $query_args, $args ) {
		if ( isset( $query_args['post__in'] ) && array( 0 ) === $query_args['post__in'] ) { // Where `array( 0 ) correspond to `WCS_Admin_Post_Types::$post__in_none`, telling no result should be returned.
			$data_store = PLLWC_Data_Store::load( 'product_language' );
			$found_subs_from_translations = wcs_get_subscriptions_for_product(
				array_merge(
					$data_store->get_translations( $args['product_id'] ),
					$data_store->get_translations( $args['variation_id'] )
				)
			);

			if ( ! empty( $found_subs_from_translations ) ) {
				$query_args['post__in'] = $found_subs_from_translations;
			}
		}
		return $query_args;
	}

	/**
	 * Synchronizes the subscription variations deletion.
	 * The case is handled specifically in WC Subscriptions because
	 * subscription variations are trashed and not deleted permanently.
	 * Hooked to the actions 'wp_trash_post' and 'before_delete_post'.
	 *
	 * @since 1.3.3
	 *
	 * @param int $variation_id Subscription variation id.
	 * @return void
	 */
	public function delete_variation( $variation_id ) {
		static $avoid_delete = array();

		$post_type = get_post_type( $variation_id );

		if ( 'product_variation' === $post_type && ! in_array( $variation_id, $avoid_delete ) ) {
			$variation_product = wc_get_product( $variation_id );

			if ( $variation_product && $variation_product->is_type( 'subscription_variation' ) ) {
				$data_store = PLLWC_Data_Store::load( 'product_language' );
				$tr_ids = $data_store->get_translations( $variation_id );
				$avoid_delete = array_merge( $avoid_delete, array_values( $tr_ids ) ); // To avoid deleting a variation two times.
				foreach ( $tr_ids as $tr_id ) {
					wp_trash_post( $tr_id );
				}
			}
		}
	}

	/**
	 * "Safe" init after the plugin is declared compatible with the WC's HPOS feature.
	 * At this point, `pll_init` has been triggered we're in `before_woocommerce_init`.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	public function custom_order_tables_init(): void {
		if ( PLL() instanceof PLL_Admin && PLLWC()->admin_orders instanceof PLLWC_Admin_Orders_HPOS ) {
			add_action( 'woocommerce_after_subscription_object_save', array( PLLWC()->admin_orders, 'save_order_language' ) );
		}
	}
}
