<?php

/**
 * Fetches, normalizes, and caches the MCP server catalog from GitHub.
 *
 * @package Oboto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MCP_Catalog_Fetcher
 */
class MCP_Catalog_Fetcher {

	const TRANSIENT_KEY          = 'mcp_catalog_data';
	const STALE_OPTION_KEY       = 'mcp_catalog_last_successful_data';
	const STATUS_OPTION_KEY      = 'mcp_catalog_sync_status';
	const REFRESH_LOCK_KEY       = 'mcp_catalog_refresh_lock';
	const CRON_HOOK              = 'mcp_catalog_refresh';
	const ASYNC_REFRESH_HOOK     = 'mcp_catalog_async_refresh';
	const GITHUB_OWNER           = 'obot-platform';
	const GITHUB_REPO            = 'mcp-catalog';
	const GITHUB_BRANCH          = 'main';
	const CATALOG_DIRECTORIES    = array( 'remotes', 'obot-remotes', 'obot-images' );
	const REQUEST_TIMEOUT        = 5;
	const MAX_FETCH_DURATION     = 90;
	const REFRESH_LOCK_TTL       = 300;
	const NORMALIZATION_VERSION = 4;

	/**
	 * Most recent fetch error for the current request.
	 *
	 * @var string
	 */
	private static $last_error = '';

	/**
	 * Get catalog data without blocking a frontend request on GitHub.
	 *
	 * @param int $cache_hours Cache TTL in hours. Default 24.
	 * @return array Normalized MCP server entries.
	 */
	public static function get_catalog( $cache_hours = 24 ) {
		$cached = get_transient( self::TRANSIENT_KEY );

		if ( false !== $cached && is_array( $cached ) && self::uses_current_normalization( $cached ) ) {
			$stale = get_option( self::STALE_OPTION_KEY, array() );
			if ( ! is_array( $stale ) || empty( $stale ) ) {
				update_option( self::STALE_OPTION_KEY, $cached, false );
			}
			return $cached;
		}

		self::schedule_refresh( $cache_hours );

		$stale = get_option( self::STALE_OPTION_KEY, array() );
		if ( is_array( $stale ) && self::uses_current_normalization( $stale ) ) {
			return $stale;
		}

		return array();
	}

	/**
	 * Check whether every cached entry uses the current normalization contract.
	 *
	 * @param array $catalog Cached catalog entries.
	 * @return bool True when the cache can be used without a refresh.
	 */
	private static function uses_current_normalization( $catalog ) {
		if ( empty( $catalog ) ) {
			return false;
		}

		foreach ( $catalog as $entry ) {
			if (
				! is_array( $entry ) ||
				! isset( $entry['normalization_version'] ) ||
				self::NORMALIZATION_VERSION !== (int) $entry['normalization_version']
			) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the latest catalog synchronization status.
	 *
	 * @return array Status data.
	 */
	public static function get_sync_status() {
		$status = get_option( self::STATUS_OPTION_KEY, array() );
		return is_array( $status ) ? $status : array();
	}

	/**
	 * Schedule a non-blocking catalog refresh.
	 *
	 * @param int $cache_hours Cache TTL in hours.
	 */
	public static function schedule_refresh( $cache_hours = 24 ) {
		$cache_hours = max( 1, (int) $cache_hours );
		$args        = array( $cache_hours );

		if ( ! wp_next_scheduled( self::ASYNC_REFRESH_HOOK, $args ) ) {
			wp_schedule_single_event( time(), self::ASYNC_REFRESH_HOOK, $args );
		}
	}

	/**
	 * Fetch and store fresh data while retaining the last successful fallback.
	 *
	 * @param int $cache_hours Cache TTL in hours.
	 * @return array Fresh catalog data, or an empty array on failure/lock.
	 */
	public static function refresh_catalog( $cache_hours = 24 ) {
		if ( get_transient( self::REFRESH_LOCK_KEY ) ) {
			return array();
		}

		set_transient( self::REFRESH_LOCK_KEY, 1, self::REFRESH_LOCK_TTL );
		self::$last_error = '';
		self::record_attempt( 'syncing' );

		try {
			$data = self::fetch_from_github();
			if ( empty( $data ) ) {
				self::record_failure( self::$last_error ? self::$last_error : 'GitHub returned an empty catalog.' );
				return array();
			}

			$ttl = max( 1, (int) $cache_hours ) * HOUR_IN_SECONDS;
			set_transient( self::TRANSIENT_KEY, $data, $ttl );
			update_option( self::STALE_OPTION_KEY, $data, false );

			$status = self::record_success( $data );
			do_action( 'mcp_catalog_refreshed', $data, $status );

			return $data;
		} finally {
			delete_transient( self::REFRESH_LOCK_KEY );
		}
	}

	/**
	 * Clear the expiring cache while retaining the last successful fallback.
	 */
	public static function clear_cache() {
		delete_transient( self::TRANSIENT_KEY );
	}

	/**
	 * Fetch catalog files from GitHub and normalize their complete payloads.
	 * Unchanged entries are reused by Git blob SHA to avoid repeated downloads.
	 *
	 * @return array Normalized server list.
	 */
	public static function fetch_from_github() {
		if ( ! class_exists( '\\Symfony\\Component\\Yaml\\Yaml' ) ) {
			self::$last_error = 'Symfony YAML is unavailable. Run Composer install for the theme.';
			return array();
		}

		$files = self::list_yaml_files();
		if ( empty( $files ) ) {
			return array();
		}

		$previous         = get_option( self::STALE_OPTION_KEY, array() );
		$previous_by_file = array();
		$servers          = array();
		$started_at       = microtime( true );
		$raw_base         = sprintf(
			'https://raw.githubusercontent.com/%s/%s/%s/',
			self::GITHUB_OWNER,
			self::GITHUB_REPO,
			self::GITHUB_BRANCH
		);

		if ( is_array( $previous ) ) {
			foreach ( $previous as $entry ) {
				if ( is_array( $entry ) && ! empty( $entry['source_file'] ) ) {
					$previous_by_file[ $entry['source_file'] ] = $entry;
				}
			}
		}

		foreach ( $files as $file ) {
			$source_file = $file['path'];
			$sha         = $file['sha'];
			$old         = isset( $previous_by_file[ $source_file ] ) ? $previous_by_file[ $source_file ] : array();

			if (
				! empty( $old ) &&
				isset( $old['source_sha'], $old['normalization_version'] ) &&
				hash_equals( (string) $old['source_sha'], (string) $sha ) &&
				self::NORMALIZATION_VERSION === (int) $old['normalization_version']
			) {
				$servers[] = $old;
				continue;
			}

			$remaining_time = self::MAX_FETCH_DURATION - ( microtime( true ) - $started_at );
			if ( $remaining_time < 1 ) {
				self::$last_error = 'The GitHub catalog refresh exceeded its time budget.';
				return array();
			}

			$url     = $raw_base . self::encode_path( $source_file );
			$timeout = min( self::REQUEST_TIMEOUT, max( 1, (int) ceil( $remaining_time ) ) );
			$content = self::fetch_url( $url, false, $timeout );
			if ( '' === $content ) {
				return array();
			}

			$parsed = self::parse_yaml_server( $content, $source_file, $sha );
			if ( empty( $parsed ) ) {
				if ( ! self::$last_error ) {
					self::$last_error = sprintf( 'Could not normalize %s.', $source_file );
				}
				return array();
			}

			$servers[] = $parsed;
		}

		return $servers;
	}

	/**
	 * List YAML files from every configured catalog directory.
	 *
	 * @return array List of arrays containing repository path and Git blob SHA.
	 */
	private static function list_yaml_files() {
		$files = array();
		foreach ( self::CATALOG_DIRECTORIES as $directory ) {
			$url = sprintf(
				'https://api.github.com/repos/%s/%s/contents/%s?ref=%s',
				self::GITHUB_OWNER,
				self::GITHUB_REPO,
				rawurlencode( $directory ),
				rawurlencode( self::GITHUB_BRANCH )
			);
			$body = self::fetch_url( $url, true, self::REQUEST_TIMEOUT );
			if ( '' === $body ) {
				return array();
			}

			$json = json_decode( $body, true );
			if ( ! is_array( $json ) ) {
				self::$last_error = sprintf( 'GitHub returned an invalid file listing for %s.', $directory );
				return array();
			}

			foreach ( $json as $item ) {
				if (
					is_array( $item ) &&
					isset( $item['name'], $item['sha'], $item['type'] ) &&
					'file' === $item['type'] &&
					preg_match( '/\.ya?ml$/i', $item['name'] )
				) {
					$filename = sanitize_file_name( $item['name'] );
					$files[]  = array(
						'path' => $directory . '/' . $filename,
						'sha'  => sanitize_text_field( $item['sha'] ),
					);
				}
			}
		}

		usort(
			$files,
			static function ( $first, $second ) {
				return strcasecmp( $first['path'], $second['path'] );
			}
		);

		return $files;
	}

	/**
	 * Build a GitHub source URL for a catalog file.
	 *
	 * @param string $source_file Repository-relative source path.
	 * @return string Source file URL, or the catalog repository URL as fallback.
	 */
	public static function get_source_url( $source_file ) {
		$repository_url = sprintf( 'https://github.com/%s/%s', self::GITHUB_OWNER, self::GITHUB_REPO );
		$source_file    = self::sanitize_source_path( $source_file );
		if ( '' === $source_file ) {
			return $repository_url;
		}

		return sprintf(
			'%s/blob/%s/%s',
			$repository_url,
			rawurlencode( self::GITHUB_BRANCH ),
			self::encode_path( $source_file )
		);
	}

	/**
	 * Sanitize a repository-relative catalog path without flattening directories.
	 *
	 * @param string $path Raw repository-relative path.
	 * @return string Sanitized path.
	 */
	public static function sanitize_source_path( $path ) {
		$segments = explode( '/', str_replace( '\\', '/', (string) $path ) );
		$segments = array_filter(
			array_map( 'sanitize_file_name', $segments ),
			static function ( $segment ) {
				return '' !== $segment && '.' !== $segment && '..' !== $segment;
			}
		);

		return implode( '/', $segments );
	}

	/**
	 * Encode each path segment while retaining repository path separators.
	 *
	 * @param string $path Repository-relative path.
	 * @return string URL-encoded path.
	 */
	private static function encode_path( $path ) {
		$path = self::sanitize_source_path( $path );
		return implode( '/', array_map( 'rawurlencode', explode( '/', $path ) ) );
	}

	/**
	 * Fetch a URL through the WordPress HTTP API.
	 *
	 * @param string $url     URL to fetch.
	 * @param bool   $is_json Whether to request JSON.
	 * @param int    $timeout Request timeout in seconds.
	 * @return string Response body or an empty string on failure.
	 */
	private static function fetch_url( $url, $is_json = false, $timeout = self::REQUEST_TIMEOUT ) {
		$args = array(
			'timeout' => max( 1, (int) $timeout ),
			'headers' => array(
				'User-Agent' => 'Oboto-Theme-MCP-Catalog/2.0',
			),
		);
		if ( $is_json ) {
			$args['headers']['Accept'] = 'application/vnd.github+json';
		}

		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) ) {
			self::$last_error = $response->get_error_message();
			return '';
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			self::$last_error = sprintf( 'GitHub request failed with HTTP %d.', $code );
			return '';
		}

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Parse and normalize a catalog YAML document.
	 *
	 * @param string $content  Raw YAML.
	 * @param string $filename Source filename.
	 * @param string $sha      Git blob SHA.
	 * @return array Normalized catalog entry.
	 */
	private static function parse_yaml_server( $content, $filename, $sha ) {
		try {
			$data = \Symfony\Component\Yaml\Yaml::parse( $content );
		} catch ( \Symfony\Component\Yaml\Exception\ParseException $exception ) {
			self::$last_error = sprintf( 'Invalid YAML in %1$s: %2$s', $filename, $exception->getMessage() );
			return array();
		}

		if ( ! is_array( $data ) || empty( $data['name'] ) ) {
			self::$last_error = sprintf( '%s is missing a server name.', $filename );
			return array();
		}

		$metadata       = isset( $data['metadata'] ) && is_array( $data['metadata'] ) ? $data['metadata'] : array();
		$categories_raw = isset( $metadata['categories'] ) ? $metadata['categories'] : array();
		$categories     = self::normalize_categories( $categories_raw );
		$external_url   = isset( $data['repoURL'] ) ? esc_url_raw( (string) $data['repoURL'] ) : '';
		$slug           = sanitize_title( str_replace( '_', '-', pathinfo( $filename, PATHINFO_FILENAME ) ) );

		return array(
			'name'                  => self::string_value( $data, 'name' ),
			'entry_key'             => self::string_value( $data, 'entryKey' ),
			'slug'                  => $slug,
			'server_user_type'      => self::string_value( $data, 'serverUserType' ),
			'short_description'     => self::string_value( $data, 'shortDescription' ),
			'description'           => self::string_value( $data, 'description' ),
			'icon'                  => isset( $data['icon'] ) ? esc_url_raw( (string) $data['icon'] ) : '',
			'external_url'          => $external_url,
			'link'                  => $external_url,
			'categories'            => $categories,
			'env'                   => self::array_value( $data, 'env' ),
			'runtime'               => self::string_value( $data, 'runtime' ),
			'containerized_config'  => self::array_value( $data, 'containerizedConfig' ),
			'remote_config'         => self::array_value( $data, 'remoteConfig' ),
			'tool_preview'          => self::array_value( $data, 'toolPreview' ),
			'metadata'              => self::normalize_value( $metadata ),
			'source_file'           => $filename,
			'source_sha'            => $sha,
			'normalization_version' => self::NORMALIZATION_VERSION,
		);
	}

	/**
	 * Normalize a comma-separated or array category value.
	 *
	 * @param mixed $value Raw categories.
	 * @return array Category labels.
	 */
	private static function normalize_categories( $value ) {
		if ( is_string( $value ) ) {
			$value = preg_split( '/,\s*/', $value );
		}
		if ( ! is_array( $value ) ) {
			return array();
		}

		$categories = array();
		foreach ( $value as $category ) {
			if ( is_scalar( $category ) ) {
				$category = trim( (string) $category );
				if ( '' !== $category ) {
					$categories[] = $category;
				}
			}
		}

		return array_values( array_unique( $categories ) );
	}

	/**
	 * Read a scalar value as a string.
	 *
	 * @param array  $data Source array.
	 * @param string $key  Key to read.
	 * @return string Value or empty string.
	 */
	private static function string_value( $data, $key ) {
		return isset( $data[ $key ] ) && is_scalar( $data[ $key ] ) ? trim( (string) $data[ $key ] ) : '';
	}

	/**
	 * Read and recursively normalize an array value.
	 *
	 * @param array  $data Source array.
	 * @param string $key  Key to read.
	 * @return array Normalized array.
	 */
	private static function array_value( $data, $key ) {
		return isset( $data[ $key ] ) && is_array( $data[ $key ] ) ? self::normalize_value( $data[ $key ] ) : array();
	}

	/**
	 * Limit cached YAML values to arrays, scalars, and null.
	 *
	 * @param mixed $value Raw parsed value.
	 * @return mixed Normalized value.
	 */
	private static function normalize_value( $value ) {
		if ( is_array( $value ) ) {
			$normalized = array();
			foreach ( $value as $key => $item ) {
				$normalized[ $key ] = self::normalize_value( $item );
			}
			return $normalized;
		}

		if ( is_scalar( $value ) || null === $value ) {
			return $value;
		}

		return (string) $value;
	}

	/**
	 * Store a synchronization attempt status.
	 *
	 * @param string $state Current state.
	 */
	private static function record_attempt( $state ) {
		$status                     = self::get_sync_status();
		$status['state']            = $state;
		$status['last_attempt_gmt'] = gmdate( 'c' );
		unset( $status['error'] );
		update_option( self::STATUS_OPTION_KEY, $status, false );
	}

	/**
	 * Store a failed synchronization status without losing success metadata.
	 *
	 * @param string $message Error message.
	 */
	private static function record_failure( $message ) {
		$status                     = self::get_sync_status();
		$status['state']            = 'error';
		$status['last_attempt_gmt'] = gmdate( 'c' );
		$status['error']            = sanitize_text_field( $message );
		update_option( self::STATUS_OPTION_KEY, $status, false );
	}

	/**
	 * Store a successful synchronization status.
	 *
	 * @param array $data Normalized catalog.
	 * @return array Stored status.
	 */
	private static function record_success( $data ) {
		$github_count = 0;
		$manifest     = array();

		foreach ( $data as $entry ) {
			if ( self::is_github_url( isset( $entry['external_url'] ) ? $entry['external_url'] : '' ) ) {
				++$github_count;
			}
			$manifest[] = ( isset( $entry['source_file'] ) ? $entry['source_file'] : '' ) . ':' . ( isset( $entry['source_sha'] ) ? $entry['source_sha'] : '' );
		}

		$status = array(
			'state'            => 'success',
			'last_attempt_gmt' => gmdate( 'c' ),
			'last_success_gmt' => gmdate( 'c' ),
			'server_count'     => count( $data ),
			'github_count'     => $github_count,
			'external_count'   => count( $data ) - $github_count,
			'manifest_hash'    => hash( 'sha256', implode( '|', $manifest ) ),
		);
		update_option( self::STATUS_OPTION_KEY, $status, false );

		return $status;
	}

	/**
	 * Determine whether a catalog resource URL points to GitHub.
	 *
	 * @param string $url Resource URL.
	 * @return bool True for github.com URLs.
	 */
	public static function is_github_url( $url ) {
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		return 'github.com' === $host || 'www.github.com' === $host;
	}

	/**
	 * Cron callback.
	 */
	public static function cron_refresh() {
		self::refresh_catalog( 24 );
	}

	/**
	 * Async callback for refreshes scheduled after a cache miss or manual sync.
	 *
	 * @param int $cache_hours Cache TTL in hours.
	 */
	public static function async_refresh( $cache_hours = 24 ) {
		self::refresh_catalog( $cache_hours );
	}
}

add_action( MCP_Catalog_Fetcher::CRON_HOOK, array( 'MCP_Catalog_Fetcher', 'cron_refresh' ) );
add_action( MCP_Catalog_Fetcher::ASYNC_REFRESH_HOOK, array( 'MCP_Catalog_Fetcher', 'async_refresh' ), 10, 1 );
