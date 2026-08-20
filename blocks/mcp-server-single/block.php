<?php

/**
 * Register MCP Server Single block assets.
 */
function mcp_server_single_register_assets() {
	$stylesheet_path = get_template_directory() . '/css/mcp-server-single.css';

	wp_register_style(
		'mcp-server-single',
		get_template_directory_uri() . '/css/mcp-server-single.css',
		array(),
		file_exists( $stylesheet_path ) ? filemtime( $stylesheet_path ) : wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'mcp_server_single_register_assets' );
add_action( 'admin_enqueue_scripts', 'mcp_server_single_register_assets' );

/**
 * Make the block readable in the editor preview.
 */
function mcp_server_single_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/mcp-server-single.css' );
}
add_action( 'init', 'mcp_server_single_editor_styles' );
