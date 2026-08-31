<?php

/**
 * Register assets for the Comparison Matrix block.
 */
function comparison_matrix_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'comparison-matrix',
		$theme_uri . '/css/comparison-matrix.css',
		array(),
		filemtime( $theme_dir . '/css/comparison-matrix.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'comparison_matrix_register_assets' );
add_action( 'admin_enqueue_scripts', 'comparison_matrix_register_assets' );

function comparison_matrix_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/comparison-matrix.css' );
}
add_action( 'init', 'comparison_matrix_editor_styles' );
