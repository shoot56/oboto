<?php

/**
 * Register MCP List block assets (style + view script).
 * Called on both frontend and admin so block.json "style" and "viewScript" handles exist.
 */
function mcp_list_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'mcp-list',
		$theme_uri . '/css/mcp-list.css',
		array(),
		filemtime( $theme_dir . '/css/mcp-list.css' )
	);
	wp_register_script(
		'mcp-list-script',
		$theme_uri . '/blocks/mcp-list/view-script.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}

add_action( 'wp_enqueue_scripts', 'mcp_list_register_assets' );
add_action( 'admin_enqueue_scripts', 'mcp_list_register_assets' );

/**
 * Enqueue MCP List script in editor content (preview iframe) so the refresh button works.
 * As of WordPress 6.3, enqueue_block_assets runs for the iframed editor content too.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/enqueueing-assets-in-the-editor/
 */
function mcp_list_editor_content_assets() {
	if ( ! is_admin() ) {
		return;
	}
	wp_enqueue_script( 'mcp-list-script' );
	wp_enqueue_style( 'mcp-list' );
}
add_action( 'enqueue_block_assets', 'mcp_list_editor_content_assets' );

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
 * AJAX handler: clear MCP catalog cache (for "Refresh catalog" in editor).
 * Requires capability to edit posts. Always returns JSON (no redirect).
 */
function mcp_list_ajax_refresh_cache() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
	}
	if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'mcp_list_refresh_cache' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid nonce' ), 403 );
	}

	MCP_Catalog_Fetcher::clear_cache();

	wp_send_json_success( array( 'message' => __( 'Cache cleared.', 'oboto' ) ) );
}
add_action( 'wp_ajax_mcp_list_refresh_cache', 'mcp_list_ajax_refresh_cache' );
