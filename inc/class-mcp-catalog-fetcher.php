<?php

/**
 * Fetches and caches MCP server catalog from GitHub obot-platform/mcp-catalog.
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

	const TRANSIENT_KEY = 'mcp_catalog_data';
	const CRON_HOOK     = 'mcp_catalog_refresh';
	const GITHUB_OWNER = 'obot-platform';
	const GITHUB_REPO  = 'mcp-catalog';
	const GITHUB_BRANCH = 'main';

	/**
	 * Get catalog data (from cache or by fetching from GitHub).
	 *
	 * @param int $cache_hours Cache TTL in hours. Default 24.
	 * @return array List of server entries with keys: name, short_description, icon, link, categories.
	 */
	public static function get_catalog( $cache_hours = 24 ) {
		$cache_key = self::TRANSIENT_KEY;
		$cached    = get_transient( $cache_key );

		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		$data = self::fetch_from_github();
		if ( ! empty( $data ) ) {
			$ttl = max( 1, (int) $cache_hours ) * HOUR_IN_SECONDS;
			set_transient( $cache_key, $data, $ttl );
		}

		return $data;
	}

	/**
	 * Clear the cached catalog (e.g. before manual refresh).
	 */
	public static function clear_cache() {
		delete_transient( self::TRANSIENT_KEY );
	}

	/**
	 * Fetch catalog from GitHub: list YAML files, then fetch and parse each.
	 *
	 * @return array Normalized server list.
	 */
	public static function fetch_from_github() {
		$files = self::list_yaml_files();
		if ( empty( $files ) ) {
			return array();
		}

		$servers = array();
		$base    = 'https://raw.githubusercontent.com/' . self::GITHUB_OWNER . '/' . self::GITHUB_REPO . '/' . self::GITHUB_BRANCH . '/';

		foreach ( $files as $filename ) {
			$url     = $base . $filename;
			$content = self::fetch_url( $url );
			if ( '' === $content ) {
				continue;
			}

			$parsed = self::parse_yaml_server( $content );
			if ( ! empty( $parsed['name'] ) ) {
				$servers[] = $parsed;
			}
		}

		return $servers;
	}

	/**
	 * List .yaml files in the repo root via GitHub API.
	 *
	 * @return array List of filenames (e.g. ['slack.yaml', 'github.yaml']).
	 */
	private static function list_yaml_files() {
		$url  = sprintf(
			'https://api.github.com/repos/%s/%s/contents/',
			self::GITHUB_OWNER,
			self::GITHUB_REPO
		);
		$body = self::fetch_url( $url, true );
		if ( '' === $body ) {
			return array();
		}

		$json = json_decode( $body, true );
		if ( ! is_array( $json ) ) {
			return array();
		}

		$files = array();
		foreach ( $json as $item ) {
			if ( isset( $item['name'] ) && isset( $item['type'] ) && 'file' === $item['type'] ) {
				$name = $item['name'];
				if ( preg_match( '/\.ya?ml$/i', $name ) ) {
					$files[] = $name;
				}
			}
		}

		return $files;
	}

	/**
	 * Fetch URL with wp_remote_get.
	 *
	 * @param string $url     URL to fetch.
	 * @param bool   $is_json Whether to send Accept: application/json (e.g. for API).
	 * @return string Response body or empty string on failure.
	 */
	private static function fetch_url( $url, $is_json = false ) {
		$args = array(
			'timeout' => 15,
			'headers' => array(
				'User-Agent' => 'Oboto-Theme-MCP-Catalog/1.0',
			),
		);
		if ( $is_json ) {
			$args['headers']['Accept'] = 'application/vnd.github.v3+json';
		}

		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) ) {
			return '';
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return '';
		}

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Parse a single server YAML content into normalized array.
	 *
	 * @param string $content Raw YAML file content.
	 * @return array Keys: name, short_description, icon, link, categories (array of strings).
	 */
	private static function parse_yaml_server( $content ) {
		$name = self::yaml_line_value( $content, 'name' );
		if ( '' === $name ) {
			return array();
		}

		$short_description = self::yaml_line_value( $content, 'shortDescription' );
		$icon              = self::yaml_line_value( $content, 'icon' );
		if ( '' === $icon && preg_match( '/metadata:\s*\n(?:.*\n)*?\s*icon:\s*(.+)$/m', $content, $m ) ) {
			$icon = trim( $m[1] );
		}

		$link = self::yaml_line_value( $content, 'repoURL' );
		if ( '' === $link && preg_match( '/metadata:\s*\n(?:.*\n)*?\s*repoURL:\s*(.+)$/m', $content, $m ) ) {
			$link = trim( $m[1] );
		}

		$categories_str = self::yaml_line_value( $content, 'categories' );
		if ( '' === $categories_str && preg_match( '/metadata:\s*\n(?:.*\n)*?\s*categories:\s*(.+)$/m', $content, $m ) ) {
			$categories_str = trim( $m[1] );
		}
		$categories = array();
		if ( '' !== $categories_str ) {
			$categories = array_map( 'trim', preg_split( '/,\s*/', $categories_str ) );
			$categories = array_filter( $categories );
		}

		return array(
			'name'              => $name,
			'short_description' => $short_description,
			'icon'              => $icon,
			'link'              => $link,
			'categories'        => $categories,
		);
	}

	/**
	 * Extract value of a top-level YAML key (single line).
	 *
	 * @param string $content YAML content.
	 * @param string $key     Key name (e.g. name, shortDescription).
	 * @return string Trimmed value or empty string.
	 */
	private static function yaml_line_value( $content, $key ) {
		$pattern = '/^' . preg_quote( $key, '/' ) . '\s*:\s*(.+)$/m';
		if ( preg_match( $pattern, $content, $m ) ) {
			return trim( $m[1] );
		}
		return '';
	}

	/**
	 * Cron callback: clear cache and re-fetch so next request gets fresh data.
	 */
	public static function cron_refresh() {
		self::clear_cache();
		self::get_catalog( 24 );
	}
}

add_action( MCP_Catalog_Fetcher::CRON_HOOK, array( 'MCP_Catalog_Fetcher', 'cron_refresh' ) );
