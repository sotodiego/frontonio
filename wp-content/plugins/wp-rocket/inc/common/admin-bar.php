<?php

defined( 'ABSPATH' ) || exit;

/**
 * Add menu in admin bar.
 * From this menu, you can preload the cache files, clear entire domain cache or post cache (front & back-end).
 *
 * @since 1.3.5 Compatibility with qTranslate
 * @since 1.3.0 Compatibility with WPML
 * @since 1.0
 *
 * @param Object $wp_admin_bar Admin bar object.
 */
function rocket_admin_bar( $wp_admin_bar ) {
	global $pagenow, $post;

	if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$referer = filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ), FILTER_SANITIZE_URL );
		$referer = '&_wp_http_referer=' . rawurlencode( remove_query_arg( 'fl_builder', $referer ) );
	} else {
		$referer = '';
	}

	$has_cap = false;

	$capabilities = [
		'rocket_manage_options',
		'rocket_purge_cache',
		'rocket_preload_cache',
		'rocket_regenerate_critical_css',
		'rocket_remove_unused_css',
	];

	foreach ( $capabilities as $cap ) {
		if ( current_user_can( $cap ) ) {
			$has_cap = true;
			break;
		}
	}

	if ( $has_cap ) {
		/**
		 * Parent.
		 */
		$wp_admin_bar->add_menu(
			[
				'id'    => 'wp-rocket',
				'title' => WP_ROCKET_PLUGIN_NAME,
				'href'  => current_user_can( 'rocket_manage_options' ) ? admin_url( 'options-general.php?page=' . WP_ROCKET_PLUGIN_SLUG ) : false,
			]
		);
	}

	if ( current_user_can( 'rocket_manage_options' ) ) {
		/**
		 * Settings.
		 */
		$wp_admin_bar->add_menu(
			[
				'parent' => 'wp-rocket',
				'id'     => 'rocket-settings',
				'title'  => __( 'Settings', 'rocket' ),
				'href'   => admin_url( 'options-general.php?page=' . WP_ROCKET_PLUGIN_SLUG ),
			]
		);
	}

	if ( current_user_can( 'rocket_purge_cache' ) ) {
		/**
		 * Purge Cache.
		 */
		$action = 'purge_cache';

		if ( rocket_valid_key() ) {
			$i18n_plugin = rocket_has_i18n();

			if ( $i18n_plugin ) {
				// Parent.
				$wp_admin_bar->add_menu(
					[
						'parent' => 'wp-rocket',
						'id'     => 'purge-all',
						'title'  => (bool) get_rocket_option( 'manual_preload', false ) ? __( 'Clear and preload cache', 'rocket' ) : __( 'Clear cache', 'rocket' ),
					]
				);

				$langlinks_default = [];

				// Add submenu for each active langs.
				switch ( $i18n_plugin ) {
					case 'wpml':
						$langlinks = get_rocket_wpml_langs_for_admin_bar();
						break;
					case 'qtranslate':
						$langlinks = get_rocket_qtranslate_langs_for_admin_bar();
						break;
					case 'qtranslate-x':
						$langlinks = get_rocket_qtranslate_langs_for_admin_bar( 'x' );
						break;
					case 'polylang':
						$langlinks = get_rocket_polylang_langs_for_admin_bar();
						break;
					default:
						/**
						 * Filters the value of the lang links menu
						 *
						 * @param array $langlinks Array of languages.
						 */
						$langlinks = apply_filters( 'rocket_i18n_admin_bar_menu', [] );

						if ( ! is_array( $langlinks ) ) {
							$langlinks = $langlinks_default;
						}
				}

				if ( $langlinks ) {
					foreach ( $langlinks as $lang ) {
						$wp_admin_bar->add_menu(
							[
								'parent' => 'purge-all',
								'id'     => 'purge-all-' . $lang['code'],
								'title'  => $lang['flag'] . '&nbsp;' . $lang['anchor'],
								'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=' . $action . '&type=all&lang=' . $lang['code'] . $referer ), $action . '_all' ),
							]
						);
					}

					if ( 'wpml' !== $i18n_plugin ) {
						// Add subemnu "All langs" (the one for WPML is already printed).
						$wp_admin_bar->add_menu(
							[
								'parent' => 'purge-all',
								'id'     => 'purge-all-all',
								'title'  => '<div class="dashicons-before dashicons-admin-site" style="line-height:1.5"> ' . __( 'All languages', 'rocket' ) . '</div>',
								'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=' . $action . '&type=all&lang=all' . $referer ), $action . '_all' ),
							]
						);
					}
				}
			} else {
				// Purge All.
				$wp_admin_bar->add_menu(
					[
						'parent' => 'wp-rocket',
						'id'     => 'purge-all',
						'title'  => (bool) get_rocket_option( 'manual_preload', false ) ? __( 'Clear and preload cache', 'rocket' ) : __( 'Clear cache', 'rocket' ),
						'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=' . $action . '&type=all' . $referer ), $action . '_all' ),
					]
				);
			}

			/**
			 * Filters the rocket clear post admin bar menu.
			 *
			 * @since 3.11.4
			 *
			 * @param bool  $should_skip Should skip adding clear post to rocket option in admin bar.
			 * @param type  $post Post object.
			 */
			if ( ! apply_filters( 'rocket_skip_admin_bar_cache_purge_option', false, $post ) && rocket_can_display_options() ) {
				if ( is_admin() ) {
					/**
					 * Purge a post.
					 */
					if ( $post && 'post.php' === $pagenow && isset( $_GET['action'], $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$wp_admin_bar->add_menu(
							[
								'parent' => 'wp-rocket',
								'id'     => 'purge-post',
								'title'  => __( 'Clear this post', 'rocket' ),
								'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=' . $action . '&type=post-' . $post->ID . $referer ), $action . '_post-' . $post->ID ),
							]
						);

					}
				} else {
					/**
					 * Purge this URL (frontend).
					 */
					$wp_admin_bar->add_menu(
						[
							'parent' => 'wp-rocket',
							'id'     => 'purge-url',
							'title'  => __( 'Purge this URL', 'rocket' ),
							'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=' . $action . '&type=url' . $referer ), $action . '_url' ),
						]
					);
				}
			}
		}
	}

	if ( current_user_can( 'rocket_purge_sucuri_cache' ) ) {
		/**
		 * Purge Sucuri cache if Sucuri is active.
		 */
		if ( get_rocket_option( 'sucury_waf_cache_sync', 0 ) ) {
			$action = 'rocket_purge_sucuri';

			$wp_admin_bar->add_menu(
				[
					'parent' => 'wp-rocket',
					'id'     => 'purge-sucuri',
					'title'  => __( 'Purge Sucuri cache', 'rocket' ),
					'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=' . $action . $referer ), $action ),
				]
			);
		}
	}

	/**
	 * Fires when adding WP Rocket admin bar items
	 *
	 * @since 3.6
	 *
	 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	do_action( 'rocket_admin_bar_items', $wp_admin_bar );

	if ( current_user_can( 'rocket_manage_options' ) ) {
		$rocketcdn_status = get_transient( 'rocketcdn_status' );

		if ( isset( $rocketcdn_status['subscription_active'] ) && 'running' === $rocketcdn_status['subscription_active'] ) {
			$wp_admin_bar->add_menu(
				[
					'parent' => 'wp-rocket',
					'id'     => 'purge-cdn-cache',
					'title'  => __( 'Purge RocketCDN cache', 'rocket' ),
					'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=rocket_purge_rocketcdn' . $referer ), 'rocket_purge_rocketcdn' ),
				]
			);
		}

		/**
		 * Go to WP Rocket Documentation.
		 */
		$wp_admin_bar->add_menu(
			[
				'parent' => 'wp-rocket',
				'id'     => 'docs',
				'title'  => __( 'Documentation', 'rocket' ),
				'href'   => get_rocket_documentation_url(),
			]
		);

		/**
		 * Go to WP Rocket FAQ.
		 */
		$wp_admin_bar->add_menu(
			[
				'parent' => 'wp-rocket',
				'id'     => 'faq',
				'title'  => __( 'FAQ', 'rocket' ),
				'href'   => get_rocket_faq_url(),
			]
		);

		/**
		 * Go to WP Rocket Support.
		 */
		$wp_admin_bar->add_menu(
			[
				'parent' => 'wp-rocket',
				'id'     => 'support',
				'title'  => __( 'Support', 'rocket' ),
				'href'   => rocket_get_external_url(
					'support',
					[
						'utm_source' => 'wp_plugin',
						'utm_medium' => 'wp_rocket',
					]
				),
			]
		);
	}
}
add_action( 'admin_bar_menu', 'rocket_admin_bar', PHP_INT_MAX - 10 );
