<?php

/**
 * Enqueue scripts and styles for MCP List block.
 */
function mcp_list_scripts() {
	wp_register_style(
		'mcp-list',
		get_template_directory_uri() . '/css/mcp-list.css',
		array(),
		filemtime( get_template_directory() . '/css/mcp-list.css' )
	);
	wp_register_script(
		'mcp-list-script',
		get_template_directory_uri() . '/blocks/mcp-list/view-script.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'mcp_list_scripts' );

/**
 * Admin/editor styles for MCP List block.
 */
function mcp_list_admin_style() {
	wp_register_style(
		'mcp-list',
		get_template_directory_uri() . '/css/mcp-list.css',
		array(),
		filemtime( get_template_directory() . '/css/mcp-list.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'mcp_list_admin_style' );

/**
 * Editor style for block preview.
 */
function mcp_list_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/mcp-list.css' );
}
add_action( 'init', 'mcp_list_editor_styles' );

/**
 * Schedule WP Cron to refresh MCP catalog cache hourly (if not already scheduled).
 */
function mcp_list_schedule_cron() {
	if ( ! wp_next_scheduled( MCP_Catalog_Fetcher::CRON_HOOK ) ) {
		wp_schedule_event( time(), 'hourly', MCP_Catalog_Fetcher::CRON_HOOK );
	}
}
add_action( 'init', 'mcp_list_schedule_cron' );

/**
 * AJAX handler: clear MCP catalog cache (for "Refresh Cache" in editor).
 * Requires capability to edit posts. Redirects back if redirect param present.
 */
function mcp_list_ajax_refresh_cache() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
	}
	if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'mcp_list_refresh_cache' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid nonce' ), 403 );
	}

	MCP_Catalog_Fetcher::clear_cache();

	$redirect = isset( $_REQUEST['redirect'] ) ? sanitize_url( wp_unslash( $_REQUEST['redirect'] ) ) : '';
	if ( '' !== $redirect && wp_validate_redirect( $redirect, false ) ) {
		wp_safe_redirect( add_query_arg( 'mcp_cache_refreshed', '1', $redirect ) );
		exit;
	}

	wp_send_json_success( array( 'message' => 'Cache cleared.' ) );
}
add_action( 'wp_ajax_mcp_list_refresh_cache', 'mcp_list_ajax_refresh_cache' );
