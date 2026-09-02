<?php

/**
 * Synchronizes MCP catalog entries into read-only WordPress posts.
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
	const DATA_VERSION_OPTION  = 'oboto_mcp_server_data_version';
	const DATA_VERSION         = 'mcp_server_payload_array_v4_automatic_stale_cleanup';
	const REPAIR_LOCK_KEY      = 'oboto_mcp_server_data_repair_lock';
	const REPAIR_LOCK_TTL      = 5 * MINUTE_IN_SECONDS;
	const SYNC_LOCK_OPTION     = 'oboto_mcp_server_post_sync_lock';
	const SYNC_LOCK_TTL        = 10 * MINUTE_IN_SECONDS;
	const REWRITE_OPTION       = 'oboto_mcp_server_rewrite_version';
	const REWRITE_VERSION      = 'mcp_server_rewrite_v1';

	/**
	 * Synchronize posts after a complete catalog refresh.
	 *
	 * @param array $catalog Full normalized catalog.
	 * @param array $catalog_status Catalog synchronization status.
	 * @return bool Whether the synchronization completed without errors.
	 */
	public static function sync_posts( $catalog, $catalog_status = array() ) {
		if ( ! is_array( $catalog ) || empty( $catalog ) ) {
			return false;
		}

		if ( ! self::acquire_sync_lock() ) {
			return false;
		}

		$existing_ids = get_posts(
			array(
				'post_type'              => self::POST_TYPE,
				'post_status'            => array( 'publish', 'draft', 'pending', 'private', 'trash' ),
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
			)
		);
		$existing_by_entry_key   = array();
		$existing_by_source_file = array();
		$existing_by_slug        = array();
		foreach ( $existing_ids as $post_id ) {
			$entry_key   = (string) get_post_meta( $post_id, self::ENTRY_KEY_META_KEY, true );
			$source_file = (string) get_post_meta( $post_id, self::SOURCE_FILE_META_KEY, true );
			$post_slug   = (string) get_post_field( 'post_name', $post_id );
			if ( $entry_key ) {
				$existing_by_entry_key[ $entry_key ][] = (int) $post_id;
			}
			if ( $source_file ) {
				$existing_by_source_file[ $source_file ][] = (int) $post_id;
			}
			if ( $post_slug ) {
				$existing_by_slug[ $post_slug ][] = (int) $post_id;
			}
		}

		$seen_ids = array();
		$errors   = array();
		$created  = 0;
		$updated  = 0;

		foreach ( $catalog as $server ) {
			if ( ! is_array( $server ) ) {
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

			$post_id         = self::find_existing_post_id(
				$slug,
				$entry_key,
				$source_file,
				$existing_by_slug,
				$existing_by_entry_key,
				$existing_by_source_file
			);
			$source_sha      = isset( $server['source_sha'] ) ? (string) $server['source_sha'] : '';
			$stored_sha      = $post_id ? (string) get_post_meta( $post_id, self::SOURCE_SHA_META_KEY, true ) : '';
			$stored_payload  = $post_id ? get_post_meta( $post_id, self::PAYLOAD_META_KEY, true ) : null;
			$payload_matches = is_array( $stored_payload ) && $stored_payload === $server;
			$current_post    = $post_id ? get_post( $post_id ) : null;
			$excerpt         = isset( $server['short_description'] ) ? (string) $server['short_description'] : '';
			$needs_update    = ! $current_post ||
				$stored_sha !== $source_sha ||
				! $payload_matches ||
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

				$payload_saved = update_post_meta( $post_id, self::PAYLOAD_META_KEY, wp_slash( $server ) );
				if ( false === $payload_saved && get_post_meta( $post_id, self::PAYLOAD_META_KEY, true ) !== $server ) {
					$errors[] = sprintf( '%s: Could not save the catalog payload.', $source_file );
				}
				update_post_meta( $post_id, self::ENTRY_KEY_META_KEY, $entry_key );
				update_post_meta( $post_id, self::SOURCE_FILE_META_KEY, $source_file );
				update_post_meta( $post_id, self::SOURCE_SHA_META_KEY, $source_sha );
			}

			$seen_ids[] = $post_id;
		}

		$deleted = 0;
		if ( empty( $errors ) ) {
			foreach ( $existing_ids as $existing_id ) {
				if ( in_array( (int) $existing_id, $seen_ids, true ) ) {
					continue;
				}

				if ( wp_delete_post( (int) $existing_id, true ) ) {
					++$deleted;
				} else {
					$errors[] = sprintf( __( 'Post %d could not be deleted.', 'oboto' ), $existing_id );
				}
			}
		}

		$previous_status = get_option( self::SYNC_STATUS_OPTION, array() );
		$status          = array(
			'state'             => empty( $errors ) ? 'success' : 'partial',
			'last_attempt_gmt'  => gmdate( 'c' ),
			'published_count'   => count( array_unique( $seen_ids ) ),
			'created_count'     => $created,
			'updated_count'     => $updated,
			'deleted_count'     => $deleted,
			'errors'            => $errors,
			'manifest_hash'     => isset( $catalog_status['manifest_hash'] ) ? $catalog_status['manifest_hash'] : '',
		);
		if ( empty( $errors ) ) {
			$status['last_success_gmt'] = $status['last_attempt_gmt'];
		} elseif ( is_array( $previous_status ) && ! empty( $previous_status['last_success_gmt'] ) ) {
			$status['last_success_gmt'] = $previous_status['last_success_gmt'];
		}
		update_option( self::SYNC_STATUS_OPTION, $status, false );
		delete_option( self::SYNC_LOCK_OPTION );

		return empty( $errors );
	}

	/**
	 * Acquire an atomic lock so overlapping refreshes cannot create duplicate posts.
	 *
	 * @return bool Whether the caller owns the lock.
	 */
	private static function acquire_sync_lock() {
		$lock_time = (int) get_option( self::SYNC_LOCK_OPTION, 0 );
		if ( $lock_time && $lock_time > time() - self::SYNC_LOCK_TTL ) {
			return false;
		}

		if ( $lock_time ) {
			delete_option( self::SYNC_LOCK_OPTION );
		}

		return add_option( self::SYNC_LOCK_OPTION, time(), '', false );
	}

	/**
	 * Choose the existing record that should own a catalog entry.
	 *
	 * The exact public slug wins even when it is currently a draft. Publishing that
	 * record restores the canonical URL while the old suffixed record is removed.
	 *
	 * @param string $slug Desired public slug.
	 * @param string $entry_key Catalog entry identity.
	 * @param string $source_file Directory-qualified YAML path.
	 * @param array  $by_slug Existing records grouped by slug.
	 * @param array  $by_entry_key Existing records grouped by entry key.
	 * @param array  $by_source_file Existing records grouped by source file.
	 * @return int Existing post ID or zero.
	 */
	private static function find_existing_post_id( $slug, $entry_key, $source_file, $by_slug, $by_entry_key, $by_source_file ) {
		if ( isset( $by_slug[ $slug ] ) ) {
			return self::prefer_published_post_id( $by_slug[ $slug ] );
		}

		$identity_ids = array();
		if ( $entry_key && isset( $by_entry_key[ $entry_key ] ) ) {
			$identity_ids = array_merge( $identity_ids, $by_entry_key[ $entry_key ] );
		}
		if ( $source_file && isset( $by_source_file[ $source_file ] ) ) {
			$identity_ids = array_merge( $identity_ids, $by_source_file[ $source_file ] );
		}

		return self::prefer_published_post_id( array_unique( array_map( 'intval', $identity_ids ) ) );
	}

	/**
	 * Prefer a published record from a list of possible matches.
	 *
	 * @param array $post_ids Candidate post IDs.
	 * @return int Selected post ID or zero.
	 */
	private static function prefer_published_post_id( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			if ( 'publish' === get_post_status( $post_id ) ) {
				return (int) $post_id;
			}
		}

		return $post_ids ? (int) reset( $post_ids ) : 0;
	}

	/**
	 * Return a normalized payload for an MCP Server post.
	 *
	 * @param int $post_id Post ID.
	 * @return array Catalog payload.
	 */
	public static function get_payload( $post_id ) {
		$stored_payload = get_post_meta( $post_id, self::PAYLOAD_META_KEY, true );
		if ( is_array( $stored_payload ) ) {
			return $stored_payload;
		}

		$json = $stored_payload;
		if ( is_string( $json ) && '' !== $json ) {
			$payload = json_decode( $json, true );
			if ( is_array( $payload ) ) {
				return $payload;
			}

			$payload = json_decode( wp_unslash( $json ), true );
			if ( is_array( $payload ) ) {
				return $payload;
			}
		}

		return self::get_catalog_payload_by_slug( get_post_field( 'post_name', $post_id ) );
	}

	/**
	 * Find a server in the last successful catalog when post meta is unavailable.
	 *
	 * This keeps public pages usable after an interrupted post synchronization and
	 * does not perform a blocking GitHub request during the page render.
	 *
	 * @param string $slug Public MCP server slug.
	 * @return array Catalog payload or an empty array.
	 */
	public static function get_catalog_payload_by_slug( $slug ) {
		$slug = sanitize_title( (string) $slug );
		if ( ! $slug ) {
			return array();
		}

		$catalog = MCP_Catalog_Fetcher::get_catalog( 24 );
		foreach ( $catalog as $server ) {
			if ( ! is_array( $server ) ) {
				continue;
			}

			$server_slug = isset( $server['slug'] ) ? sanitize_title( (string) $server['slug'] ) : '';
			if ( ! $server_slug && ! empty( $server['source_file'] ) ) {
				$server_slug = sanitize_title( str_replace( '_', '-', pathinfo( (string) $server['source_file'], PATHINFO_FILENAME ) ) );
			}

			if ( $slug === $server_slug ) {
				return $server;
			}
		}

		return array();
	}

	/**
	 * Resolve a catalog entry to its published internal MCP URL.
	 *
	 * @param array $server Normalized catalog entry.
	 * @return string Internal URL or an empty string.
	 */
	public static function get_internal_url( $server ) {
		if ( ! is_array( $server ) ) {
			return '';
		}

		$slug = isset( $server['slug'] ) ? sanitize_title( $server['slug'] ) : '';
		if ( ! $slug ) {
			return '';
		}

		$post = get_page_by_path( $slug, OBJECT, self::POST_TYPE );
		if ( $post && 'publish' === $post->post_status ) {
			return get_permalink( $post );
		}

		$identity_meta = array(
			self::ENTRY_KEY_META_KEY   => isset( $server['entry_key'] ) ? (string) $server['entry_key'] : '',
			self::SOURCE_FILE_META_KEY => isset( $server['source_file'] ) ? (string) $server['source_file'] : '',
		);
		foreach ( $identity_meta as $meta_key => $meta_value ) {
			if ( ! $meta_value ) {
				continue;
			}

			$post_ids = get_posts(
				array(
					'post_type'              => self::POST_TYPE,
					'post_status'            => 'publish',
					'posts_per_page'         => 1,
					'fields'                 => 'ids',
					'orderby'                => 'ID',
					'order'                  => 'DESC',
					'meta_key'               => $meta_key,
					'meta_value'             => $meta_value,
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);
			if ( $post_ids ) {
				return get_permalink( (int) reset( $post_ids ) );
			}
		}

		return '';
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
	 * Repair synchronized payloads once after a data-model deployment.
	 *
	 * Existing posts can survive a deployment even when their payload meta was not
	 * written. The catalog cache is already the source used by the listing, so this
	 * repair does not wait for WP-Cron or make a blocking GitHub request.
	 */
	public static function maybe_repair_payloads() {
		if ( self::DATA_VERSION === get_option( self::DATA_VERSION_OPTION ) || get_transient( self::REPAIR_LOCK_KEY ) ) {
			return;
		}

		$catalog = MCP_Catalog_Fetcher::get_catalog( 24 );
		if ( empty( $catalog ) ) {
			return;
		}

		set_transient( self::REPAIR_LOCK_KEY, 1, self::REPAIR_LOCK_TTL );
		$sync_complete = self::sync_posts( $catalog, MCP_Catalog_Fetcher::get_sync_status() );
		if ( ! $sync_complete ) {
			delete_transient( self::REPAIR_LOCK_KEY );
			return;
		}

		$status = get_option( self::SYNC_STATUS_OPTION, array() );
		if ( is_array( $status ) && 'success' === ( isset( $status['state'] ) ? $status['state'] : '' ) ) {
			update_option( self::DATA_VERSION_OPTION, self::DATA_VERSION, false );
			delete_transient( self::REPAIR_LOCK_KEY );
		}
	}

	/**
	 * Add synchronization diagnostics to the read-only MCP Server list.
	 *
	 * @param array $columns Existing admin columns.
	 * @return array Filtered columns.
	 */
	public static function admin_columns( $columns ) {
		return array(
			'title'       => __( 'Server', 'oboto' ),
			'mcp_slug'    => __( 'Slug', 'oboto' ),
			'mcp_payload' => __( 'Payload', 'oboto' ),
			'mcp_source'  => __( 'Source', 'oboto' ),
			'date'        => isset( $columns['date'] ) ? $columns['date'] : __( 'Date', 'oboto' ),
		);
	}

	/**
	 * Render synchronization diagnostics for an MCP Server row.
	 *
	 * @param string $column_name Column key.
	 * @param int    $post_id     MCP Server post ID.
	 */
	public static function render_admin_column( $column_name, $post_id ) {
		if ( 'mcp_slug' === $column_name ) {
			echo '<a href="' . esc_url( get_permalink( $post_id ) ) . '" target="_blank" rel="noopener noreferrer"><code>' . esc_html( get_post_field( 'post_name', $post_id ) ) . '</code></a>';
			return;
		}

		if ( 'mcp_payload' === $column_name ) {
			$stored_payload = get_post_meta( $post_id, self::PAYLOAD_META_KEY, true );
			$payload        = is_array( $stored_payload ) ? $stored_payload : null;
			if ( ! $payload && is_string( $stored_payload ) && '' !== $stored_payload ) {
				$payload = json_decode( $stored_payload, true );
				if ( ! is_array( $payload ) ) {
					$payload = json_decode( wp_unslash( $stored_payload ), true );
				}
			}
			if ( is_array( $payload ) ) {
				$encoded_size = wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
				echo esc_html( sprintf( __( 'Valid (%s)', 'oboto' ), size_format( strlen( (string) $encoded_size ) ) ) );
			} elseif ( is_string( $stored_payload ) && '' !== $stored_payload ) {
				echo '<strong>' . esc_html__( 'Invalid JSON', 'oboto' ) . '</strong>';
			} else {
				echo '<strong>' . esc_html__( 'Missing', 'oboto' ) . '</strong>';
			}
			return;
		}

		if ( 'mcp_source' === $column_name ) {
			echo esc_html( get_post_meta( $post_id, self::SOURCE_FILE_META_KEY, true ) );
		}
	}

	/**
	 * Remove row actions from generated, read-only MCP Server records.
	 *
	 * @param array   $actions Existing row actions.
	 * @param WP_Post $post    Current post.
	 * @return array Empty actions list.
	 */
	public static function remove_admin_row_actions( $actions, $post ) {
		return $post instanceof WP_Post && self::POST_TYPE === $post->post_type ? array() : $actions;
	}

	/**
	 * Remove bulk mutation actions from the read-only MCP Server list.
	 *
	 * @param array $actions Existing bulk actions.
	 * @return array Empty actions list.
	 */
	public static function remove_admin_bulk_actions( $actions ) {
		return array();
	}

	/**
	 * Show the latest catalog and post synchronization state in wp-admin.
	 */
	public static function render_admin_sync_notice() {
		$screen = get_current_screen();
		if ( ! $screen || 'edit-' . self::POST_TYPE !== $screen->id ) {
			return;
		}

		$catalog_status = MCP_Catalog_Fetcher::get_sync_status();
		$post_status    = get_option( self::SYNC_STATUS_OPTION, array() );
		$catalog_state  = isset( $catalog_status['state'] ) ? (string) $catalog_status['state'] : __( 'not run', 'oboto' );
		$post_state     = isset( $post_status['state'] ) ? (string) $post_status['state'] : __( 'not run', 'oboto' );
		$published      = isset( $post_status['published_count'] ) ? (int) $post_status['published_count'] : 0;
		$deleted        = isset( $post_status['deleted_count'] ) ? (int) $post_status['deleted_count'] : 0;
		$errors         = isset( $post_status['errors'] ) && is_array( $post_status['errors'] ) ? $post_status['errors'] : array();
		?>
		<div class="notice notice-info">
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: 1: catalog state, 2: post sync state, 3: published MCP Server count. */
						__( 'Catalog: %1$s. Post sync: %2$s. Published servers: %3$d.', 'oboto' ),
						$catalog_state,
						$post_state,
						$published
					)
				);
				?>
			</p>
			<?php if ( $errors ) : ?>
				<p><strong><?php esc_html_e( 'Latest errors:', 'oboto' ); ?></strong> <?php echo esc_html( implode( ' | ', array_slice( array_map( 'strval', $errors ), 0, 5 ) ) ); ?></p>
			<?php endif; ?>
			<?php if ( $deleted ) : ?>
				<p><?php echo esc_html( sprintf( _n( 'Automatically removed %d stale MCP Server record.', 'Automatically removed %d stale MCP Server records.', $deleted, 'oboto' ), $deleted ) ); ?></p>
			<?php endif; ?>
		</div>
		<?php
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
add_action( 'init', array( 'MCP_Server_Sync', 'maybe_repair_payloads' ), 35 );
add_action( 'init', array( 'MCP_Server_Sync', 'maybe_flush_rewrite_rules' ), 40 );
add_filter( 'manage_mcp-server_posts_columns', array( 'MCP_Server_Sync', 'admin_columns' ) );
add_action( 'manage_mcp-server_posts_custom_column', array( 'MCP_Server_Sync', 'render_admin_column' ), 10, 2 );
add_filter( 'post_row_actions', array( 'MCP_Server_Sync', 'remove_admin_row_actions' ), 10, 2 );
add_filter( 'bulk_actions-edit-mcp-server', array( 'MCP_Server_Sync', 'remove_admin_bulk_actions' ) );
add_action( 'admin_notices', array( 'MCP_Server_Sync', 'render_admin_sync_notice' ) );
