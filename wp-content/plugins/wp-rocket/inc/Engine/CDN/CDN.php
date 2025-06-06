<?php
declare(strict_types=1);

namespace WP_Rocket\Engine\CDN;

use WP_Rocket\Admin\Options_Data;

/**
 * CDN class
 *
 * @since 3.4
 */
class CDN {
	/**
	 * WP Rocket Options instance
	 *
	 * @var Options_Data
	 */
	private $options;

	/**
	 * Home URL host
	 *
	 * @var string
	 */
	private $home_host;

	/**
	 * Constructor
	 *
	 * @param Options_Data $options WP Rocket Options instance.
	 */
	public function __construct( Options_Data $options ) {
		$this->options = $options;
	}

	/**
	 * Search & Replace URLs with the CDN URLs in the provided content
	 *
	 * @since 3.4
	 *
	 * @param string $html HTML content.
	 * @return string
	 */
	public function rewrite( $html ) {
		$relative_path_pattern = '';

		$buffer = $html;

		/**
		 * Filters the exclusion of CDN rewritting inside inline scripts
		 *
		 * @since 3.10.5
		 *
		 * @param bool $enable True to exclude, false otherwise.
		 */
		if ( apply_filters( 'rocket_cdn_exclude_inline_scripts', true ) ) {
			$buffer = $this->remove_inline_scripts( $html );
		}

		/**
		 * Filters the CDN rewriting of relative paths
		 *
		 * @since 3.10.5
		 *
		 * @param bool $enable True to enable, false otherwise.
		 */
		if ( apply_filters( 'rocket_cdn_relative_paths', true ) ) {
			$relative_path_pattern = '|\/[^/](?:[^"\')\s>]+\.[[:alnum:]]+)';
		}

		$pattern = '#[("\']\s*(?<url>(?:(?:https?:|)' . preg_quote( $this->get_base_url(), '#' ) . ')\/(?:(?:(?:' . $this->get_allowed_paths() . ')[^"\',)]+))' . $relative_path_pattern . ')\s*["\')]#i';

		if ( ! preg_match_all( $pattern, $buffer, $matches, PREG_SET_ORDER ) ) {
			return $html;
		}

		foreach ( $matches as $match ) {
			$cdn_url = str_replace( $match['url'], $this->rewrite_url( $match['url'] ), $match[0] );
			$html    = str_replace( $match[0], $cdn_url, $html );
		}

		return $html;
	}

	/**
	 * Rewrites URLs in a srcset attribute using the CDN URL
	 *
	 * @since 3.4.0.4
	 *
	 * @param string $html HTML content.
	 * @return string
	 */
	public function rewrite_srcset( $html ) {
		$pattern = '#\s+(?:' . $this->get_srcset_attributes() . ')?srcset\s*=\s*["\']\s*(?<sources>[^"\',\s]+\.[^"\',\s]+(?:\s+\d+[wx])?(?:\s*,\s*[^"\',\s]+\.[^"\',\s]+(?:\s+\d+[wx])?)*)\s*["\']#i';

		if ( ! preg_match_all( $pattern, $html, $srcsets, PREG_SET_ORDER ) ) {
			return $html;
		}
		foreach ( $srcsets as $srcset ) {
			$sources    = explode( ',', $srcset['sources'] );
			$sources    = array_unique( array_map( 'trim', $sources ) );
			$cdn_srcset = $srcset['sources'];
			foreach ( $sources as $source ) {
				$url        = preg_split( '#\s+#', trim( $source ) );
				$cdn_source = str_replace( $url[0], $this->rewrite_url( $url[0] ), $source );
				$cdn_srcset = str_replace( $source, $cdn_source, $cdn_srcset );
			}

			$cdn_srcsets = str_replace( $srcset['sources'], $cdn_srcset, $srcset[0] );
			$html        = str_replace( $srcset[0], $cdn_srcsets, $html );
		}

		return $html;
	}

	/**
	 * Rewrites an URL with the CDN URL
	 *
	 * @since 3.4
	 *
	 * @param string $url Original URL.
	 * @return string
	 */
	public function rewrite_url( $url ) {
		if ( ! $this->options->get( 'cdn', 0 ) ) {
			return $url;
		}

		if ( $this->is_excluded( $url ) ) {
			return $url;
		}

		$cdn_urls = $this->get_cdn_urls( $this->get_zones_for_url( $url ) );

		if ( ! $cdn_urls ) {
			return $url;
		}

		$parsed_url = wp_parse_url( $url );
		$cdn_url    = untrailingslashit( $cdn_urls[ ( abs( crc32( $parsed_url['path'] ) ) % count( $cdn_urls ) ) ] );

		if ( ! isset( $parsed_url['host'] ) ) {
			return rocket_add_url_protocol( $cdn_url . '/' . ltrim( $url, '/' ) );
		}

		$home_host = $this->get_home_host();

		if ( ! isset( $parsed_url['scheme'] ) ) {
			return str_replace( $home_host, rocket_remove_url_protocol( $cdn_url ), $url );
		}

		$home_url = [
			'http://' . $home_host,
			'https://' . $home_host,
		];

		return str_replace( $home_url, rocket_add_url_protocol( $cdn_url ), $url );
	}

	/**
	 * Rewrites URLs to CDN URLs in CSS content
	 *
	 * @since 3.4
	 *
	 * @param string $content CSS content.
	 * @return string
	 */
	public function rewrite_css_properties( $content ) {
		if ( ! preg_match_all( '#url\(\s*(\'|")?\s*(?![\'"]?data)(?<url>(?:https?:|)' . preg_quote( $this->get_base_url(), '#' ) . '\/[^"|\'|\)|\s]+)\s*#i', $content, $matches, PREG_SET_ORDER ) ) {
			return $content;
		}

		foreach ( $matches as $property ) {
			/**
			 * Filters the URL of the CSS property
			 *
			 * @since 2.8
			 *
			 * @param string $url URL of the CSS property.
			 */
			$cdn_url     = $this->rewrite_url( apply_filters( 'rocket_cdn_css_properties_url', $property['url'] ) );
			$replacement = str_replace( $property['url'], $cdn_url, $property[0] );
			$content     = str_replace( $property[0], $replacement, $content );
		}

		return $content;
	}

	/**
	 * Get all CDN URLs for one or more zones.
	 *
	 * @since 2.1
	 * @since 3.0 Don't check for WP Rocket CDN option activated to be able to use the function on Hosting with CDN auto-enabled.
	 *
	 * @param  array $zones List of zones. Default is [ 'all' ].
	 * @return array
	 */
	public function get_cdn_urls( $zones = [ 'all' ] ) {
		$hosts    = [];
		$zones    = (array) $zones;
		$cdn_urls = $this->options->get( 'cdn_cnames', [] );

		if ( $cdn_urls ) {
			$cdn_zones = $this->options->get( 'cdn_zone', [] );

			foreach ( $cdn_urls as $k => $urls ) {
				if ( ! in_array( $cdn_zones[ $k ], $zones, true ) ) {
					continue;
				}

				$urls = explode( ',', $urls );
				$urls = array_map( 'trim', $urls );

				foreach ( $urls as $url ) {
					$hosts[] = $url;
				}
			}
		}

		/**
		 * Filter all CDN URLs.
		 *
		 * @since 2.7
		 * @since 3.4 Added $zone parameter.
		 *
		 * @param array $hosts List of CDN URLs.
		 * @param array $zones List of zones. Default is [ 'all' ].
		 */
		$hosts = (array) apply_filters( 'rocket_cdn_cnames', $hosts, $zones );
		$hosts = array_filter( $hosts );
		$hosts = array_flip( array_flip( $hosts ) );
		$hosts = array_values( $hosts );

		return $hosts;
	}

	/**
	 * Gets the base URL for the website
	 *
	 * @since 3.4
	 *
	 * @return string
	 */
	private function get_base_url() {
		return '//' . $this->get_home_host();
	}

	/**
	 * Gets the allowed paths as a regex pattern for the CDN rewrite
	 *
	 * @since 3.4
	 *
	 * @return string
	 */
	private function get_allowed_paths() {
		$wp_content_dirname  = ltrim( trailingslashit( wp_parse_url( content_url(), PHP_URL_PATH ) ), '/' );
		$wp_includes_dirname = ltrim( trailingslashit( wp_parse_url( includes_url(), PHP_URL_PATH ) ), '/' );

		$upload_dirname = '';
		$uploads_info   = wp_upload_dir();

		if ( ! empty( $uploads_info['baseurl'] ) ) {
			$upload_dirname = '|' . ltrim( trailingslashit( wp_parse_url( $uploads_info['baseurl'], PHP_URL_PATH ) ), '/' );
		}

		return $wp_content_dirname . $upload_dirname . '|' . $wp_includes_dirname;
	}

	/**
	 * Checks if the provided URL can be rewritten with the CDN URL
	 *
	 * @since 3.4
	 *
	 * @param string $url URL to check.
	 * @return boolean
	 */
	public function is_excluded( $url ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );

		$excluded_extensions = [
			'php',
			'html',
			'htm',
			'cfm',
		];

		if ( in_array( pathinfo( $path, PATHINFO_EXTENSION ), $excluded_extensions, true ) ) {
			return true;
		}

		if ( ! $path ) {
			return true;
		}

		if ( '/' === $path ) {
			return true;
		}

		if ( preg_match( '#^(' . $this->get_excluded_files( '#' ) . ')$#', $path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Gets the home URL host
	 *
	 * @since 3.5.5
	 *
	 * @return string
	 */
	private function get_home_host() {
		if ( empty( $this->home_host ) ) {
			$this->home_host = wp_parse_url( home_url(), PHP_URL_HOST );
		}

		return $this->home_host;
	}

	/**
	 * Gets the CDN zones for the provided URL
	 *
	 * @since 3.4
	 *
	 * @param string $url URL to check.
	 * @return array
	 */
	private function get_zones_for_url( $url ) {
		$zones = [ 'all' ];

		$ext = pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION );

		$image_types = [
			'jpg',
			'jpeg',
			'jpe',
			'png',
			'gif',
			'webp',
			'bmp',
			'tiff',
			'svg',
		];

		if ( 'css' === $ext || 'js' === $ext ) {
			$zones[] = 'css_and_js';
		}

		if ( 'css' === $ext ) {
			$zones[] = 'css';
		}

		if ( 'js' === $ext ) {
			$zones[] = 'js';
		}

		if ( in_array( $ext, $image_types, true ) ) {
			$zones[] = 'images';
		}

		return $zones;
	}

	/**
	 * Get all files we don't allow to get in CDN.
	 *
	 * @since 2.5
	 *
	 * @param string $delimiter RegEx delimiter.
	 * @return string A pipe-separated list of excluded files.
	 */
	private function get_excluded_files( $delimiter ) {
		$files = $this->options->get( 'cdn_reject_files', [] );

		/**
			* Filter the excluded files.
			*
			* @since 2.5
			*
			* @param array $files List of excluded files.
		*/
		$files = (array) apply_filters( 'rocket_cdn_reject_files', $files );
		$files = array_filter( $files );

		if ( ! $files ) {
			return '';
		}

		$files = array_flip( array_flip( $files ) );
		$files = array_map(
			function ( $file ) use ( $delimiter ) {
				return str_replace( $delimiter, '\\' . $delimiter, $file );
			},
			$files
		);

		return implode( '|', $files );
	}

	/**
	 * Get srcset attributes to rewrite to the CDN.
	 *
	 * @since 3.8.7
	 *
	 * @return string A pipe-separated list of srcset attributes.
	 */
	private function get_srcset_attributes() {
		/**
		 * Filter the srcset attributes.
		 *
		 * @since 3.8.7
		 *
		 * @param array $srcset_attributes List of srcset attributes.
		 */
		$srcset_attributes = (array) apply_filters(
			'rocket_cdn_srcset_attributes',
			[
				'data-lazy-',
				'data-',
			]
		);
		return implode( '|', $srcset_attributes );
	}

	/**
	 * Removes inline scripts from the HTML
	 *
	 * @since 3.10.5
	 *
	 * @param string $html HTML content.
	 *
	 * @return string
	 */
	private function remove_inline_scripts( $html ): string {
		if ( ! preg_match_all( '#<script(?:[^>]*)>(?<content>[\s\S]*?)</script>#msi', $html, $matches, PREG_SET_ORDER ) ) {
			return $html;
		}

		if ( empty( $matches ) ) {
			return $html;
		}

		foreach ( $matches as $inline_js ) {
			if ( empty( $inline_js['content'] ) ) {
				continue;
			}

			$html = str_replace( $inline_js[0], '', $html );
		}

		return $html;
	}
}
