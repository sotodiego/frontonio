<?php
namespace WP_Rocket\ThirdParty\Plugins\Ecommerce;

use WP_Rocket\Engine\Optimization\DelayJS\HTML;
use WP_Rocket\Event_Management\Event_Manager;
use WP_Rocket\Event_Management\Event_Manager_Aware_Subscriber_Interface;
use WP_Rocket\Logger\Logger;
use WP_Rocket\Traits\Config_Updater;

/**
 * WooCommerce compatibility
 *
 * @since 3.1
 */
class WooCommerceSubscriber implements Event_Manager_Aware_Subscriber_Interface {
	use Config_Updater;

	/**
	 * The WordPress Event Manager
	 *
	 * @var Event_Manager;
	 */
	protected $event_manager;

	/**
	 * Delay JS HTML class.
	 *
	 * @var HTML
	 */
	private $delayjs_html;

	/**
	 * WooCommerceSubscriber constructor.
	 *
	 * @param HTML $delayjs_html DelayJS HTML class.
	 */
	public function __construct( HTML $delayjs_html ) {
		$this->delayjs_html = $delayjs_html;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Event_Manager $event_manager The WordPress Event Manager.
	 */
	public function set_event_manager( Event_Manager $event_manager ) {
		$this->event_manager = $event_manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_subscribed_events() {
		$events = [
			'activate_woocommerce/woocommerce.php'   => [ 'activate_woocommerce', 11 ],
			'deactivate_woocommerce/woocommerce.php' => [ 'deactivate_woocommerce', 11 ],
		];

		if ( class_exists( 'WooCommerce' ) ) {
			$events['update_option_woocommerce_cart_page_id']             = [ 'after_update_single_option', 10, 2 ];
			$events['update_option_woocommerce_checkout_page_id']         = [ 'after_update_single_option', 10, 2 ];
			$events['update_option_woocommerce_myaccount_page_id']        = [ 'after_update_single_option', 10, 2 ];
			$events['update_option_woocommerce_default_customer_address'] = [ 'after_update_single_option', 10, 2 ];

			$events['shutdown']                           = 'maybe_update_config';
			$events['woocommerce_save_product_variation'] = 'clean_cache_after_woocommerce_save_product_variation';
			$events['transition_post_status']             = [ 'maybe_exclude_page', 10, 3 ];
			$events['rocket_cache_reject_uri']            = [
				[ 'exclude_pages' ],
			];
			$events['rocket_preload_exclude_urls']        = [
				[ 'exclude_pages' ],
			];
			$events['rocket_cache_query_strings']         = 'cache_geolocation_query_string';
			$events['rocket_cpcss_excluded_taxonomies']   = 'exclude_product_attributes_cpcss';
			$events['after_rocket_clean_post_urls']       = [ 'reformat_shop_url_for_preload', 10, 2 ];

			$events['rocket_exclude_post_taxonomy'] = 'exclude_product_shipping_taxonomy';

			/**
			 * Filters activation of WooCommerce empty cart caching
			 *
			 * @since 3.1
			 *
			 * @param bool true to activate, false to deactivate.
			 */
			if ( apply_filters( 'rocket_cache_wc_empty_cart', true ) ) {
				$events['init']              = [ 'serve_cache_empty_cart', 11 ];
				$events['template_redirect'] = [ 'cache_empty_cart', -1 ];
				$events['switch_theme']      = 'delete_cache_empty_cart';
			}

			$events['wp_head']                    = 'show_empty_product_gallery_with_delayJS';
			$events['rocket_delay_js_exclusions'] = 'show_notempty_product_gallery_with_delayJS';

			$events['wp_ajax_woocommerce_product_ordering'] = [ 'disallow_rocket_clean_post', 9 ];
			$events['woocommerce_after_product_ordering']   = [ 'allow_rocket_clean_post' ];
		}

		if ( class_exists( 'WC_API' ) ) {
			$events['rocket_cache_reject_uri'][] = [ 'exclude_wc_rest_api' ];
		}

		return $events;
	}

	/**
	 * Reformat Shop URL to prevent error on preload.
	 *
	 * @param array   $urls urls cleared.
	 * @param WP_Post $object post object.
	 * @return array
	 */
	public function reformat_shop_url_for_preload( array $urls, $object ) {
		$post_type = $object->post_type;
		if ( 'product' !== $post_type ) {
			return $urls;
		}
		$post_type_archive = get_post_type_archive_link( $post_type );
		if ( ! $post_type_archive ) {
			return $urls;
		}

		// Rename the caching filename for SSL URLs.
		$filename = 'index';
		if ( is_ssl() ) {
			$filename .= '-https';
		}

		$post_type_archive = trailingslashit( $post_type_archive );
		$index_url         = $post_type_archive . $filename . '.html';
		$index_url_gzip    = $post_type_archive . $filename . '.html_gzip';
		$pagination_url    = $post_type_archive . $GLOBALS['wp_rewrite']->pagination_base;

		foreach ( $urls as $index => $url ) {
			if ( in_array( $url, [ $index_url, $index_url_gzip, $pagination_url ], true ) ) {
				$urls[ $index ] = $post_type_archive;
			}
		}

		return array_unique( $urls );
	}

	/**
	 * Add query string to exclusion when activating the plugin
	 *
	 * @since 2.8.6
	 * @author Rémy Perona
	 */
	public function activate_woocommerce() {
		$this->event_manager->add_callback( 'rocket_cache_reject_uri', [ $this, 'exclude_pages' ] );
		$this->event_manager->add_callback( 'rocket_cache_reject_uri', [ $this, 'exclude_wc_rest_api' ] );
		$this->event_manager->add_callback( 'rocket_cache_query_strings', [ $this, 'cache_geolocation_query_string' ] );

		// Update .htaccess file rules.
		flush_rocket_htaccess();

		// Regenerate the config file.
		rocket_generate_config_file();
	}


	/**
	 * Remove query string from exclusion when deactivating the plugin
	 *
	 * @since 2.8.6
	 * @author Rémy Perona
	 */
	public function deactivate_woocommerce() {
		$this->event_manager->remove_callback( 'rocket_cache_reject_uri', [ $this, 'exclude_pages' ] );
		$this->event_manager->remove_callback( 'rocket_cache_reject_uri', [ $this, 'exclude_wc_rest_api' ] );
		$this->event_manager->remove_callback( 'rocket_cache_query_strings', [ $this, 'cache_geolocation_query_string' ] );

		// Update .htaccess file rules.
		flush_rocket_htaccess();

		// Regenerate the config file.
		rocket_generate_config_file();
	}

	/**
	 * Clean product cache on variation update
	 *
	 * @since 2.9
	 * @author Remy Perona
	 *
	 * @param int $variation_id ID of the variation.
	 * @return bool
	 */
	public function clean_cache_after_woocommerce_save_product_variation( $variation_id ) {
		$product_id = wp_get_post_parent_id( $variation_id );

		if ( ! $product_id ) {
			return false;
		}

		rocket_clean_post( $product_id );

		return true;
	}

	/**
	 * Maybe regenerate the htaccess & config file if a WooCommerce page is published
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 * @return bool
	 */
	public function maybe_exclude_page( $new_status, $old_status, $post ) {
		if ( 'publish' === $old_status || 'publish' !== $new_status ) {
			return false;
		}

		if ( ! function_exists( 'wc_get_page_id' ) ) {
			return false;
		}

		if ( wc_get_page_id( 'checkout' ) !== $post->ID && wc_get_page_id( 'cart' ) !== $post->ID && wc_get_page_id( 'myaccount' ) !== $post->ID ) {
			return false;
		}

		// Update .htaccess file rules.
		flush_rocket_htaccess();

		// Regenerate the config file.
		rocket_generate_config_file();

		return true;
	}

	/**
	 * Exclude WooCommerce cart, checkout and account pages from caching
	 *
	 * @since 2.11 Moved to 3rd party
	 * @since 2.4
	 *
	 * @param array $urls An array of excluded pages.
	 * @return array Updated array of excluded pages
	 */
	public function exclude_pages( $urls ) {
		if ( ! function_exists( 'wc_get_page_id' ) ) {
			return $urls;
		}

		$checkout_urls = $this->exclude_page( wc_get_page_id( 'checkout' ), 'page', '?(.*)' );
		$cart_urls     = $this->exclude_page( wc_get_page_id( 'cart' ) );
		$account_urls  = $this->exclude_page( wc_get_page_id( 'myaccount' ), 'page', '?(.*)' );
		return array_merge( $urls, $checkout_urls, $cart_urls, $account_urls );
	}

	/**
	 * Excludes WooCommerce checkout page from cache
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param int    $page_id   ID of page to exclude.
	 * @param string $post_type Post type of the page.
	 * @param string $pattern   Pattern to use for the exclusion.
	 * @return array
	 */
	private function exclude_page( $page_id, $post_type = 'page', $pattern = '' ) {
		global $wp_rewrite;
		$urls = [];

		if ( $page_id <= 0 || (int) get_option( 'page_on_front' ) === $page_id ) {
			return $urls;
		}

		if ( 'publish' !== get_post_status( $page_id ) ) {
			return $urls;
		}

		if ( $wp_rewrite->use_trailing_slashes ) {
			$pattern = "?$pattern";
		}

		$urls = get_rocket_i18n_translated_post_urls( $page_id, $post_type, $pattern );

		return $urls;
	}

	/**
	 * Automatically cache v query string when WC geolocation with cache compatibility option is active
	 *
	 * @since 2.8.6
	 * @author Rémy Perona
	 *
	 * @param array $query_strings list of query strings to cache.
	 * @return array Updated list of query strings to cache
	 */
	public function cache_geolocation_query_string( $query_strings ) {
		if ( 'geolocation_ajax' !== get_option( 'woocommerce_default_customer_address' ) ) {
			return $query_strings;
		}

		$query_strings[] = 'v';

		return $query_strings;
	}

	/**
	 * Returns WooCommerce API endpoint
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @return string
	 */
	private function get_wc_api_endpoint() {
		return home_url( '/wc-api/v(.*)' );
	}

	/**
	 * Exclude WooCommerce REST API URL from cache
	 *
	 * @since 2.6.5
	 *
	 * @param array $urls URLs to exclude from cache.
	 * @return array Updated list of URLs to exclude from cache
	 */
	public function exclude_wc_rest_api( $urls ) {
		/**
		 * By default, don't cache the WooCommerce REST API.
		 *
		 * @since 2.6.5
		 *
		 * @param bool false will force to cache the WooCommerce REST API
		 */
		if ( apply_filters( 'rocket_cache_reject_wc_rest_api', true ) ) {
			$urls[] = rocket_clean_exclude_file( $this->get_wc_api_endpoint() );
		}

		return $urls;
	}

	/**
	 * Serves the empty cart cache
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @return void
	 */
	public function serve_cache_empty_cart() {
		if ( ! $this->is_get_refreshed_fragments() || rocket_bypass() ) {
			return;
		}

		$cart = $this->get_cache_empty_cart();

		if ( false !== $cart ) {
			@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			echo $cart; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dynamic content is properly escaped in the view.
			die();
		}
	}

	/**
	 * Creates the empty cart cache
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @return void
	 */
	public function cache_empty_cart() {
		if ( ! $this->is_get_refreshed_fragments() || rocket_bypass() ) {
			return;
		}

		$cart = $this->get_cache_empty_cart();

		if ( false !== $cart ) {
			return;
		}

		ob_start( [ $this, 'save_cache_empty_cart' ] );
	}

	/**
	 * Gets the empty cart cache
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @return string
	 */
	private function get_cache_empty_cart() {
		$lang = rocket_get_current_language();

		if ( $lang ) {
			return get_transient( 'rocket_get_refreshed_fragments_cache_' . $lang );
		}

		return get_transient( 'rocket_get_refreshed_fragments_cache' );
	}

	/**
	 * Saves the empty cart JSON in a transient
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param string $content Current buffer content.
	 * @return string
	 */
	private function save_cache_empty_cart( $content ) {
		$lang = rocket_get_current_language();

		if ( $lang ) {
			set_transient( 'rocket_get_refreshed_fragments_cache_' . $lang, $content, 7 * DAY_IN_SECONDS );

			return $content;
		}

		set_transient( 'rocket_get_refreshed_fragments_cache', $content, 7 * DAY_IN_SECONDS );

		return $content;
	}

	/**
	 * Checks if the request is for get_refreshed_fragments and the cart is empty
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @return boolean
	 */
	private function is_get_refreshed_fragments() {
		if ( ! isset( $_GET['wc-ajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		if ( 'get_refreshed_fragments' !== $_GET['wc-ajax'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		if ( ! empty( $_COOKIE['woocommerce_cart_hash'] ) ) {
			return false;
		}

		if ( ! empty( $_COOKIE['woocommerce_items_in_cart'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Deletes the empty cart cache
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @return void
	 */
	public function delete_cache_empty_cart() {
		$langs = get_rocket_i18n_code();

		if ( $langs ) {
			foreach ( $langs as $lang ) {
				delete_transient( 'rocket_get_refreshed_fragments_cache_' . $lang );
			}
		}

		delete_transient( 'rocket_get_refreshed_fragments_cache' );
	}

	/**
	 * Excludes WC product attributes taxonomies from CPCSS generation
	 *
	 * @since 3.3.5
	 * @author Remy Perona
	 *
	 * @param array $excluded_taxonomies Taxonomies excluded from CPCSS generation.
	 * @return array
	 */
	public function exclude_product_attributes_cpcss( $excluded_taxonomies ) {
		if ( ! function_exists( 'wc_get_attribute_taxonomy_names' ) ) {
			return $excluded_taxonomies;
		}

		return array_merge( $excluded_taxonomies, wc_get_attribute_taxonomy_names() );
	}


	/**
	 * Exclude product_shipping_class taxonomy from post purge
	 *
	 * @since 3.9.1
	 *
	 * @param array $excluded_taxonomies Array of excluded taxonomies names.
	 *
	 * @return array
	 */
	public function exclude_product_shipping_taxonomy( $excluded_taxonomies ) {
		$excluded_taxonomies[] = 'product_shipping_class';

		return $excluded_taxonomies;
	}

	/**
	 * Check if current product page has images in gallery.
	 *
	 * @since 3.9.1
	 *
	 * @return bool
	 */
	private function product_has_gallery_images() {
		$product = wc_get_product( get_the_ID() );
		if ( empty( $product ) ) {
			return false;
		}
		return ! empty( $product->get_gallery_image_ids() );
	}

	/**
	 * Show product gallery main image directly when delay JS is enabled.
	 *
	 * @since 3.9.1
	 */
	public function show_empty_product_gallery_with_delayJS() {
		if ( ! $this->delayjs_html->is_allowed() ) {
			return;
		}

		if ( ! is_product() ) {
			return;
		}

		if ( $this->product_has_gallery_images() ) {
			return;
		}

		echo '<style>.woocommerce-product-gallery{ opacity: 1 !important; }</style>';
	}

	/**
	 * Exclude some JS files from delay JS when product gallery has images.
	 *
	 * @since 3.9.1
	 *
	 * @param array $exclusions Exclusions array.
	 *
	 * @return array
	 */
	public function show_notempty_product_gallery_with_delayJS( $exclusions = [] ): array {
		global $wp_version;

		if ( ! is_array( $exclusions ) ) {
			$exclusions = (array) $exclusions;
		}

		if ( ! $this->delayjs_html->is_allowed() ) {
			return $exclusions;
		}

		if ( ! is_product() ) {
			return $exclusions;
		}

		if ( ! $this->product_has_gallery_images() ) {
			return $exclusions;
		}

		$exclusions_gallery = [
			'/jquery-?[0-9.]*(.min|.slim|.slim.min)?.js',
			'/woocommerce/assets/js/zoom/jquery.zoom(.min)?.js',
			'/woocommerce/assets/js/photoswipe/',
			'/woocommerce/assets/js/flexslider/jquery.flexslider(.min)?.js',
			'/woocommerce/assets/js/frontend/single-product(.min)?.js',
		];

		if (
			isset( $wp_version )
			&&
			version_compare( $wp_version, '5.7', '<' )
		) {
			$exclusions_gallery[] = '/jquery-migrate(.min)?.js';
		}

		/**
		 * Filters the JS files excluded from delay JS when WC product gallery has images.
		 *
		 * @since 3.10.2
		 *
		 * @param array $exclusions_gallery Array of excluded filepaths.
		 */
		$exclusions_gallery = apply_filters( 'rocket_wc_product_gallery_delay_js_exclusions', $exclusions_gallery );

		return array_merge( $exclusions, $exclusions_gallery );
	}

	/**
	 * Disable post cache clearing during product sorting.
	 *
	 * @return void
	 */
	public function disallow_rocket_clean_post() : void {
		$this->event_manager->remove_callback( 'clean_post_cache', 'rocket_clean_post' );
	}

	/**
	 * Re-enable post cache clearing after product sorting.
	 *
	 * @param integer $product_id ID of sorted product.
	 * @return void
	 */
	public function allow_rocket_clean_post( int $product_id ) : void {
		$urls          = [];
		$category_list = wc_get_product_category_list( $product_id );

		if ( preg_match_all( '/<a\s+(?:[^>]*?\s+)?href=(["\'])(?<urls>.*?)\1/i', $category_list, $matches ) ) {
			$urls = $matches['urls'];
		}

		$shop_page = get_permalink( wc_get_page_id( 'shop' ) );

		if ( empty( $shop_page ) ) {
			$shop_page = home_url( 'shop' );
		}

		$urls[] = $shop_page;

		rocket_clean_files( $urls );
		$this->event_manager->add_callback( 'clean_post_cache', 'rocket_clean_post' );
	}
}
