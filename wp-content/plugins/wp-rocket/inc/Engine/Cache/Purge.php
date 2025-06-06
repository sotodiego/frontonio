<?php

namespace WP_Rocket\Engine\Cache;

use WP_Rocket\Engine\Preload\Database\Queries\Cache;
use DirectoryIterator;
use Exception;
use WP_Term;
use WP_Post;

/**
 * Cache Purge handling class
 */
class Purge {
	/**
	 * Filesystem instance
	 *
	 * @var WP_Filesystem_Direct
	 */
	private $filesystem;

	/**
	 * Cache Query
	 *
	 * @var Cache
	 */
	protected $query;

	/**
	 * Initialize the class.
	 *
	 * @param WP_Filesystem_Direct $filesystem Filesystem instance.
	 * @param Cache                $query Cache query instance.
	 */
	public function __construct( $filesystem, Cache $query ) {
		$this->filesystem = $filesystem;
		$this->query      = $query;
	}

	/**
	 * Purges cache for the dates archives of a post
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function purge_dates_archives( $post ) {
		foreach ( $this->get_dates_archives( $post ) as $url ) {
			$this->purge_url( $url, true );
		}
	}

	/**
	 * Purge URL cache.
	 *
	 * @param string  $url        URL to be purged.
	 * @param boolean $pagination Purge also pagination.
	 * @return void
	 */
	public function purge_url( $url, $pagination = false ) {
		if ( ! is_string( $url ) ) {
			return;
		}

		global $wp_rewrite;

		$parsed_url = $this->parse_url( $url );

		foreach ( _rocket_get_cache_dirs( $parsed_url['host'] ) as $dir ) {
			$path = $dir . $parsed_url['path'];

			if ( ! $this->filesystem->exists( $path ) ) {
				continue;
			}

			foreach ( $this->get_iterator( $path ) as $item ) {
				if ( $item->isFile() ) {
					$this->filesystem->delete( $item->getPathname() );
				}
			}

			if ( $pagination ) {
				$this->maybe_remove_dir( $path . DIRECTORY_SEPARATOR . $wp_rewrite->pagination_base );
			}
		}
	}

	/**
	 * Gets the dates archives URLs for the provided post
	 *
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	private function get_dates_archives( $post ) {
		$time = get_the_time( 'Y-m-d', $post );

		if ( empty( $time ) ) {
			return [];
		}

		$date = explode( '-', $time );
		$urls = [
			get_year_link( $date[0] ),
			get_month_link( $date[0], $date[1] ),
			get_day_link( $date[0], $date[1], $date[2] ),
		];

		/**
		 * Filter the list of dates URLs.
		 *
		 * @since 1.1.0
		 *
		 * @param array $urls List of dates URLs.
		*/
		return (array) apply_filters( 'rocket_post_dates_urls', $urls );
	}

	/**
	 * Parses URL and return the parts array
	 *
	 * @since 3.6.1
	 *
	 * @param string $url URL to parse.
	 * @return array
	 */
	private function parse_url( $url ) {
		$parsed_url = get_rocket_parse_url( $url );

		/** This filter is documented in inc/front/htaccess.php */
		if ( apply_filters( 'rocket_url_no_dots', false ) ) {
			$parsed_url['host'] = str_replace( '.', '_', $parsed_url['host'] );
		}

		return $parsed_url;
	}

	/**
	 * Gets the iterator for the given path
	 *
	 * @since 3.6.1
	 *
	 * @param string $path Absolute path.
	 * @return DirectoryIterator|array
	 */
	private function get_iterator( $path ) {
		try {
			$iterator = new DirectoryIterator( $path );
		} catch ( Exception $e ) {
			// No action required, as logging not enabled.
			$iterator = [];
		}

		return $iterator;
	}

	/**
	 * Recursively remove the provided directory and its content
	 *
	 * @since 3.6.1
	 *
	 * @param string $dir Absolute path for the directory.
	 * @return void
	 */
	private function maybe_remove_dir( $dir ) {
		if ( $this->filesystem->is_dir( $dir ) ) {
			rocket_rrmdir( $dir, [], $this->filesystem );
		}
	}

	/**
	 * Purge all terms archives urls associated to a specific post.
	 *
	 * @since 3.6.1
	 *
	 * @param WP_Post $post Post object.
	 */
	public function purge_post_terms_urls( WP_Post $post ) {
		$urls = $this->get_post_terms_urls( $post );
		foreach ( $urls as $url ) {
			$this->purge_url( $url, true );
		}
		/**
		 * Action to preload urls after cleaning cache.
		 *
		 * @param array urls to preload.
		 */
		do_action( 'rocket_after_clean_terms', $urls );
	}

	/**
	 * Get all terms archives urls associated to a specific post.
	 *
	 * @since 3.6.1
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return array $urls    List of taxonomies URLs
	 */
	private function get_post_terms_urls( WP_Post $post ) {
		$urls       = [];
		$taxonomies = get_object_taxonomies( get_post_type( $post->ID ), 'objects' );
		/**
		 * Filters the taxonomies excluded from post purge
		 *
		 * @since 3.9.1
		 *
		 * @param array $excluded_taxonomies Array of excluded taxonomies names.
		 */
		$excluded_taxonomies = apply_filters( 'rocket_exclude_post_taxonomy', [] );

		foreach ( $taxonomies as $taxonomy ) {
			if (
				! $taxonomy->public
				||
				in_array( $taxonomy->name, $excluded_taxonomies, true )
			) {
				continue;
			}

			// Get the terms related to post.
			$terms = get_the_terms( $post->ID, $taxonomy->name );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				continue;
			}
			foreach ( $terms as $term ) {
				$term_url = get_term_link( $term->slug, $taxonomy->name );
				if ( ! is_wp_error( $term_url ) ) {
					$urls[] = $term_url;
				}
				if ( ! is_taxonomy_hierarchical( $taxonomy->name ) ) {
					continue;
				}
				$ancestors = (array) get_ancestors( $term->term_id, $taxonomy->name );
				foreach ( $ancestors as $ancestor ) {
					$ancestor_object = get_term( $ancestor, $taxonomy->name );
					if ( ! $ancestor_object instanceof WP_Term ) {
						continue;
					}
					$ancestor_term_url = get_term_link( $ancestor_object->slug, $taxonomy->name );
					if ( ! is_wp_error( $ancestor_term_url ) ) {
						$urls[] = $ancestor_term_url;
					}
				}
			}
		}

		// Remove entries with empty values in array.
		$urls = array_filter( $urls, 'is_string' );

		/**
		 * Filter the list of taxonomies URLs
		 *
		 * @since 1.1.0
		 *
		 * @param array $urls List of taxonomies URLs
		*/
		return apply_filters( 'rocket_post_terms_urls', $urls );
	}

	/**
	 * Purge single cache file(s) added in the Never Cache URL(s).
	 *
	 * @param array $old_value An array of previous values for the settings.
	 * @param array $value An array of submitted values for the settings.
	 * @return void
	 */
	public function purge_cache_reject_uri_partially( array $old_value, array $value ): void {
		// Bail out if cache_reject_uri key is not in the settings array.
		if ( ! array_key_exists( 'cache_reject_uri', $old_value ) || ! array_key_exists( 'cache_reject_uri', $value ) ) {
			return;
		}

		// Get change in uris.
		$diff = array_diff( $value['cache_reject_uri'], $old_value['cache_reject_uri'] );

		// Bail out if values has not changed.
		if ( empty( $diff ) ) {
			return;
		}

		$urls     = [];
		$wildcard = '(.*)';
		foreach ( $diff as $path ) {
			// Check if string is a path or pattern.
			if ( strpos( $path, $wildcard ) !== false ) {
				$pattern = preg_replace( '#\(\.\*\).*#', '*', $path );
				$results = $this->query->query(
					[
						'search'         => $pattern,
						'search_columns' => [ 'url' ],
						'status'         => 'completed',
					]
				);

				foreach ( $results as $result ) {
					$urls[] = $result->url;
				}

				continue;
			}

			// Get full url of never cache path.
			$urls[] = home_url( $path );
		}
		rocket_clean_files( array_unique( $urls ) );
	}
}
