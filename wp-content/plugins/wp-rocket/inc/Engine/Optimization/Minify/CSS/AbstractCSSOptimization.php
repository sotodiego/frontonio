<?php

namespace WP_Rocket\Engine\Optimization\Minify\CSS;

use WP_Rocket\Admin\Options_Data;
use WP_Rocket\Engine\Optimization\AbstractOptimization;
use WP_Rocket\Engine\Optimization\AssetsLocalCache;

/**
 * Abstract class for CSS Optimization
 *
 * @since  3.1
 */
abstract class AbstractCSSOptimization extends AbstractOptimization {
	const FILE_TYPE = 'css';

	/**
	 * Assets local cache instance
	 *
	 * @var AssetsLocalCache
	 */
	protected $local_cache;

	/**
	 * Creates an instance of inheriting class.
	 *
	 * @since  3.1
	 *
	 * @param Options_Data     $options     Options instance.
	 * @param AssetsLocalCache $local_cache AssetsLocalCache instance.
	 */
	public function __construct( Options_Data $options, AssetsLocalCache $local_cache ) {
		$this->options        = $options;
		$this->local_cache    = $local_cache;
		$this->minify_key     = $this->options->get( 'minify_css_key', create_rocket_uniqid() );
		$this->excluded_files = $this->get_excluded_files();
		$this->init_base_path_and_url();
	}

	/**
	 * Get all files to exclude from minification/concatenation.
	 *
	 * @since  2.11
	 *
	 * @return string
	 */
	protected function get_excluded_files() {
		$excluded_files = $this->options->get( 'exclude_css', [] );

		/**
		 * Filters CSS files to exclude from minification/concatenation.
		 *
		 * @since 2.6
		 *
		 * @param array $excluded_files List of excluded CSS files.
		 */
		$excluded_files = (array) apply_filters( 'rocket_exclude_css', $excluded_files );

		if ( empty( $excluded_files ) ) {
			return '';
		}

		foreach ( $excluded_files as $i => $excluded_file ) {
			$excluded_files[ $i ] = str_replace( '#', '\#', $excluded_file );
		}

		return implode( '|', $excluded_files );
	}

	/**
	 * Returns the CDN zones.
	 *
	 * @since  3.1
	 *
	 * @return array
	 */
	public function get_zones() {
		return [ 'all', 'css_and_js', self::FILE_TYPE ];
	}

	/**
	 * Gets the minify URL
	 *
	 * @since  3.1
	 *
	 * @param string $filename     Minified filename.
	 * @param string $original_url Original URL for this file. Optional.
	 *
	 * @return string
	 */
	protected function get_minify_url( $filename, $original_url = '' ) {
		$minify_url = $this->minify_base_url . $filename;

		/**
		 * Filters CSS file URL with CDN hostname
		 *
		 * @since 2.1
		 *
		 * @param string $minify_url   Minified file URL.
		 * @param string $original_url Original URL for this file.
		 */
		return apply_filters( 'rocket_css_url', $minify_url, $original_url );
	}

	/**
	 * Determines if it is a file excluded from minification
	 *
	 * @since  2.11
	 *
	 * @param array $tag Tag corresponding to a CSS file.
	 *
	 * @return bool True if it is a file excluded, false otherwise
	 */
	protected function is_minify_excluded_file( array $tag ) {
		if ( ! isset( $tag[0], $tag['url'] ) ) {
			return true;
		}

		// File should not be minified.
		if ( false !== strpos( $tag[0], 'data-minify=' ) || false !== strpos( $tag[0], 'data-no-minify=' ) ) {
			return true;
		}

		if ( false !== strpos( $tag[0], 'media=' ) && ! preg_match( '/media=["\'](?:\s*|[^"\']*?\b(all|screen)\b[^"\']*?)["\']/i', $tag[0] ) ) {
			return true;
		}

		$file      = wp_parse_url( $tag['url'] );
		$file_path = isset( $file['path'] ) ? $file['path'] : '';
		$host      = isset( $file['host'] ) ? $file['host'] : '';

		// File extension is not css.
		if ( pathinfo( $file_path, PATHINFO_EXTENSION ) !== self::FILE_TYPE ) {
			return true;
		}

		if ( ! empty( $this->excluded_files ) ) {
			// File is excluded from minification/concatenation.
			if ( preg_match( '#^(' . $this->excluded_files . ')$#', $file_path ) ) {
				return true;
			}

			if ( preg_match( '#^(' . $this->excluded_files . ')$#', $host ) ) {
				return true;
			}

			if ( preg_match( '#^(' . $this->excluded_files . ')$#', $host . $file_path ) ) {
				return true;
			}
		}

		return false;
	}

}
