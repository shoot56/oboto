<?php

/**
 * Register assets for the Comparison block.
 */
function comparison_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'comparison',
		$theme_uri . '/css/comparison.css',
		array(),
		filemtime( $theme_dir . '/css/comparison.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'comparison_register_assets' );
add_action( 'admin_enqueue_scripts', 'comparison_register_assets' );

function comparison_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/comparison.css' );
}
add_action( 'init', 'comparison_editor_styles' );
