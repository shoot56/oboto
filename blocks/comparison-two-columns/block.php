<?php

/**
 * Register assets for the Comparison Two Columns block.
 */
function comparison_two_columns_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'comparison-two-columns',
		$theme_uri . '/css/comparison-two-columns.css',
		array(),
		filemtime( $theme_dir . '/css/comparison-two-columns.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'comparison_two_columns_register_assets' );
add_action( 'admin_enqueue_scripts', 'comparison_two_columns_register_assets' );

function comparison_two_columns_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/comparison-two-columns.css' );
}
add_action( 'init', 'comparison_two_columns_editor_styles' );
