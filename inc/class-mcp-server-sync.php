<?php

/**
 * Synchronizes GitHub-backed MCP catalog entries into read-only WordPress posts.
 *
 * @package Oboto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MCP_Server_Sync
 */
class MCP_Server_Sync {

	const POST_TYPE            = 'mcp-server';
	const PAYLOAD_META_KEY     = '_mcp_catalog_payload';
	const ENTRY_KEY_META_KEY   = '_mcp_catalog_entry_key';
	const SOURCE_FILE_META_KEY = '_mcp_catalog_source_file';
	const SOURCE_SHA_META_KEY  = '_mcp_catalog_source_sha';
	const SYNC_STATUS_OPTION   = 'mcp_server_post_sync_status';
	const REWRITE_OPTION       = 'oboto_mcp_server_rewrite_version';
	const REWRITE_VERSION      = 'mcp_server_rewrite_v1';

	/**
	 * Synchronize posts after a complete catalog refresh.
	 *
	 * @param array $catalog Full normalized catalog.
	 * @param array $catalog_status Catalog synchronization status.
	 */
	public static function sync_posts( $catalog, $catalog_status = array() ) {
		if ( ! is_array( $catalog ) || empty( $catalog ) ) {
			return;
		}

		$existing_ids = get_posts(
			array(
				'post_type'              => self::POST_TYPE,
				'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
			)
		);
		$existing_by_identity = array();
		foreach ( $existing_ids as $post_id ) {
			$entry_key   = (string) get_post_meta( $post_id, self::ENTRY_KEY_META_KEY, true );
			$source_file = (string) get_post_meta( $post_id, self::SOURCE_FILE_META_KEY, true );
			$identity    = $entry_key ? $entry_key : $source_file;
			if ( $identity ) {
				$existing_by_identity[ $identity ] = (int) $post_id;
			}
		}

		$seen_ids = array();
		$errors   = array();
		$created  = 0;
		$updated  = 0;

		foreach ( $catalog as $server ) {
			if ( ! is_array( $server ) || ! MCP_Catalog_Fetcher::is_github_url( isset( $server['external_url'] ) ? $server['external_url'] : '' ) ) {
				continue;
			}

			$entry_key   = isset( $server['entry_key'] ) ? (string) $server['entry_key'] : '';
			$source_file = isset( $server['source_file'] ) ? (string) $server['source_file'] : '';
			$identity    = $entry_key ? $entry_key : $source_file;
			$slug        = isset( $server['slug'] ) ? sanitize_title( $server['slug'] ) : '';
			$name        = isset( $server['name'] ) ? trim( (string) $server['name'] ) : '';

			if ( ! $identity || ! $slug || ! $name ) {
				$errors[] = $source_file ? $source_file : $name;
				continue;
			}

			$post_id      = isset( $existing_by_identity[ $identity ] ) ? $existing_by_identity[ $identity ] : 0;
			$source_sha   = isset( $server['source_sha'] ) ? (string) $server['source_sha'] : '';
			$stored_sha   = $post_id ? (string) get_post_meta( $post_id, self::SOURCE_SHA_META_KEY, true ) : '';
			$payload_json = wp_json_encode( $server, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			$stored_json  = $post_id ? (string) get_post_meta( $post_id, self::PAYLOAD_META_KEY, true ) : '';
			$current_post = $post_id ? get_post( $post_id ) : null;
			$excerpt      = isset( $server['short_description'] ) ? (string) $server['short_description'] : '';
			$needs_update = ! $current_post ||
				$stored_sha !== $source_sha ||
				$stored_json !== $payload_json ||
				'publish' !== $current_post->post_status ||
				$current_post->post_name !== $slug ||
				$current_post->post_title !== $name ||
				$current_post->post_excerpt !== $excerpt;

			if ( $needs_update ) {
				$post_data = array(
					'post_type'    => self::POST_TYPE,
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $name,
					'post_excerpt' => $excerpt,
				);
				if ( $post_id ) {
					$post_data['ID'] = $post_id;
				}

				$saved_post_id = wp_insert_post( wp_slash( $post_data ), true );
				if ( is_wp_error( $saved_post_id ) ) {
					$errors[] = sprintf( '%1$s: %2$s', $source_file, $saved_post_id->get_error_message() );
					if ( $post_id ) {
						$seen_ids[] = $post_id;
					}
					continue;
				}

				$post_id = (int) $saved_post_id;
				if ( $current_post ) {
					++$updated;
				} else {
					++$created;
				}

				update_post_meta( $post_id, self::PAYLOAD_META_KEY, wp_slash( $payload_json ) );
				update_post_meta( $post_id, self::ENTRY_KEY_META_KEY, $entry_key );
				update_post_meta( $post_id, self::SOURCE_FILE_META_KEY, $source_file );
				update_post_meta( $post_id, self::SOURCE_SHA_META_KEY, $source_sha );
			}

			$seen_ids[] = $post_id;
		}

		$deactivated = 0;
		foreach ( $existing_ids as $existing_id ) {
			if ( ! in_array( (int) $existing_id, $seen_ids, true ) && 'draft' !== get_post_status( $existing_id ) ) {
				$result = wp_update_post(
					array(
						'ID'          => (int) $existing_id,
						'post_status' => 'draft',
					),
					true
				);
				if ( is_wp_error( $result ) ) {
					$errors[] = sprintf( 'Post %1$d: %2$s', $existing_id, $result->get_error_message() );
				} else {
					++$deactivated;
				}
			}
		}

		$previous_status = get_option( self::SYNC_STATUS_OPTION, array() );
		$status          = array(
			'state'             => empty( $errors ) ? 'success' : 'partial',
			'last_attempt_gmt'  => gmdate( 'c' ),
			'published_count'   => count( $seen_ids ),
			'created_count'     => $created,
			'updated_count'     => $updated,
			'deactivated_count' => $deactivated,
			'errors'            => $errors,
			'manifest_hash'     => isset( $catalog_status['manifest_hash'] ) ? $catalog_status['manifest_hash'] : '',
		);
		if ( empty( $errors ) ) {
			$status['last_success_gmt'] = $status['last_attempt_gmt'];
		} elseif ( is_array( $previous_status ) && ! empty( $previous_status['last_success_gmt'] ) ) {
			$status['last_success_gmt'] = $previous_status['last_success_gmt'];
		}
		update_option( self::SYNC_STATUS_OPTION, $status, false );
	}

	/**
	 * Return a normalized payload for an MCP Server post.
	 *
	 * @param int $post_id Post ID.
	 * @return array Catalog payload.
	 */
	public static function get_payload( $post_id ) {
		$json = get_post_meta( $post_id, self::PAYLOAD_META_KEY, true );
		if ( ! is_string( $json ) || '' === $json ) {
			return array();
		}

		$payload = json_decode( $json, true );
		return is_array( $payload ) ? $payload : array();
	}

	/**
	 * Resolve a catalog entry to its published internal MCP URL.
	 *
	 * @param array $server Normalized catalog entry.
	 * @return string Internal URL or an empty string.
	 */
	public static function get_internal_url( $server ) {
		if ( ! is_array( $server ) || ! MCP_Catalog_Fetcher::is_github_url( isset( $server['external_url'] ) ? $server['external_url'] : '' ) ) {
			return '';
		}

		$slug = isset( $server['slug'] ) ? sanitize_title( $server['slug'] ) : '';
		if ( ! $slug ) {
			return '';
		}

		$post = get_page_by_path( $slug, OBJECT, self::POST_TYPE );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return '';
		}

		return get_permalink( $post );
	}

	/**
	 * Schedule the first full synchronization after this feature is deployed.
	 */
	public static function maybe_schedule_initial_sync() {
		$status = get_option( self::SYNC_STATUS_OPTION, array() );
		if ( ! is_array( $status ) || empty( $status['last_success_gmt'] ) ) {
			MCP_Catalog_Fetcher::schedule_refresh( 24 );
		}
	}

	/**
	 * Flush rewrite rules once when the MCP public URL contract changes.
	 */
	public static function maybe_flush_rewrite_rules() {
		if ( self::REWRITE_VERSION === get_option( self::REWRITE_OPTION ) ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( self::REWRITE_OPTION, self::REWRITE_VERSION, false );
	}
}

add_action( 'mcp_catalog_refreshed', array( 'MCP_Server_Sync', 'sync_posts' ), 10, 2 );
add_action( 'init', array( 'MCP_Server_Sync', 'maybe_schedule_initial_sync' ), 30 );
add_action( 'init', array( 'MCP_Server_Sync', 'maybe_flush_rewrite_rules' ), 40 );
