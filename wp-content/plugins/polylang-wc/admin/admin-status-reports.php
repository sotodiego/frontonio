<?php
/**
 * @package Polylang-WC
 */

defined( 'ABSPATH' ) || exit; // Don't access directly.

/**
 * Manages the status reports for the WooCommerce pages
 * to verify if they exist for all languages.
 *
 * @since 1.3
 *
 * @phpstan-type WooPagesStatus object{
 *     is_error: bool,
 *     pages: array<
 *         string,
 *         object{
 *             page_id: int,
 *             page_name: string,
 *             help: string,
 *             is_error: bool,
 *             error_message: string,
 *             edit_link?: string
 *         }
 *     >
 * }
 */
class PLLWC_Admin_Status_Reports {

	/**
	 * Reference to PLL_Model object.
	 *
	 * @var PLL_Model
	 */
	protected $model;

	/**
	 * List of controls on default WooCommerce pages.
	 *
	 * @var stdClass|null
	 * @phpstan-var WooPagesStatus|null
	 */
	protected $woocommerce_pages_status = null;

	/**
	 * Retrieves the status of the WooCommerce pages.
	 * Partially copied from {@see WC_REST_System_Status_V2_Controller::get_pages()}.
	 *
	 * @since 1.3
	 *
	 * @return stdClass
	 *
	 * @phpstan-return WooPagesStatus
	 */
	public function get_woocommerce_pages_status() {
		if ( ! empty( $this->woocommerce_pages_status ) ) {
			return $this->woocommerce_pages_status;
		}

		$this->woocommerce_pages_status = (object) array(
			'is_error' => false,
			'pages'    => array(),
		);

		$check_pages = array(
			_x( 'Shop base', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_shop_page_id',
				'shortcode' => '',
				'block'     => '',
				'help'      => __( 'The status of your WooCommerce shop\'s homepage translations.', 'polylang-wc' ),
			),
			_x( 'Cart', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_cart_page_id',
				'shortcode' => apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ),
				'block'     => 'woocommerce/cart',
				'help'      => __( 'The status of your WooCommerce shop\'s cart translations.', 'polylang-wc' ),
			),
			_x( 'Checkout', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_checkout_page_id',
				'shortcode' => apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ),
				'block'     => 'woocommerce/checkout',
				'help'      => __( 'The status of your WooCommerce shop\'s checkout page translations.', 'polylang-wc' ),
			),
			_x( 'My account', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_myaccount_page_id',
				'shortcode' => apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ),
				'block'     => '',
				'help'      => __( 'The status of your WooCommerce shop\'s “My Account” page translations.', 'polylang-wc' ),
			),
			_x( 'Terms and conditions', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_terms_page_id',
				'shortcode' => '',
				'block'     => '',
				'help'      => __( 'The status of your WooCommerce shop\'s “Terms and conditions” page translations.', 'polylang-wc' ),
			),
		);

		$languages = pll_languages_list();

		$pages = array();
		foreach ( $check_pages as $page_name => $values ) {
			$page_id = get_option( $values['option'] );
			$page_id = is_numeric( $page_id ) ? (int) $page_id : 0;

			$page_properties = array(
				'page_id'   => $page_id,
				'page_name' => $page_name,
				'help'      => $values['help'],
				'is_error'  => false,
			);

			if ( ! $page_id ) {
				$page_properties['is_error']      = true;
				$page_properties['error_message'] = __( 'Page not set', 'polylang-wc' );
			} else {
				$translations = pll_get_post_translations( $page_id );
				$missing      = array_diff( $languages, array_keys( $translations ) );

				// Do translations exist?
				if ( $missing ) {
					foreach ( $missing as $key => $slug ) {
						$missing[ $key ] = PLL()->model->get_language( $slug )->name;
					}
					$page_properties['is_error']      = true;
					$page_properties['error_message'] = sprintf(
						/* translators: %s comma separated list of native languages names */
						_n( 'Missing translation: %s', 'Missing translations: %s', count( $missing ), 'polylang-wc' ),
						implode( ', ', $missing )
					);
				}

				// Do translations have the correct shortcode or block?
				elseif ( ! empty( $values['block'] || ! empty( $values['shortcode'] ) ) ) {
					$wrong_translations = array();
					foreach ( $translations as $lang => $translation ) {
						$_page = get_post( $translation );

						if ( empty( $_page ) ) {
							continue;
						}

						// Shortcode checks.
						$has_shortcode = false;
						if ( $values['shortcode'] ) {
							if ( has_shortcode( $_page->post_content, $values['shortcode'] ) ) {
								$has_shortcode = true;
							}
							// Compatibility with the classic shortcode block which can be used instead of shortcodes.
							if ( ! $has_shortcode && ( 'woocommerce/checkout' === $values['block'] || 'woocommerce/cart' === $values['block'] ) ) {
								$has_shortcode = has_block( 'woocommerce/classic-shortcode', $_page->post_content );
							}
						}

						// Block checks.
						$has_block = false;
						if ( $values['block'] ) {
							$has_block = has_block( $values['block'], $_page->post_content );
						}

						if ( ! $has_shortcode && ! $has_block ) {
							$wrong_translations[] = PLL()->model->get_language( $lang )->name;
						}
					}

					if ( $wrong_translations ) {
						$page_properties['is_error']      = true;
						$page_properties['error_message'] = sprintf(
							/* translators: %s comma separated list of native languages names */
							_n( 'The shortcode or block is missing for the translation in %s', 'The shortcode or block is missing for the translations in %s', count( $wrong_translations ), 'polylang-wc' ),
							implode( ', ', $wrong_translations )
						);
					}

					$page_properties['edit_link'] = get_edit_post_link( $page_id );
				}
			}

			$pages[ $page_name ] = (object) $page_properties;
			if ( $pages[ $page_name ]->is_error ) {
				$this->woocommerce_pages_status->is_error = $pages[ $page_name ]->is_error;
			}
		}

		$this->woocommerce_pages_status->pages = $pages;

		return $this->woocommerce_pages_status;
	}

	/**
	 * Loads the status report for the translations of the default pages in the WooCommerce status page.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function status_report() {
		include __DIR__ . '/view-status-report.php';
	}

	/**
	 * Loads the status report for the translations of the default pages in our wizard.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function wizard_status_report() {
		include __DIR__ . '/view-wizard-status-report.php';
	}
}
