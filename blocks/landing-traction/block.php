<?php

/**
 * Register assets for the Landing Traction block.
 */
function landing_traction_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-traction',
		$theme_uri . '/css/landing-traction.css',
		array(),
		filemtime( $theme_dir . '/css/landing-traction.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'landing_traction_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_traction_register_assets' );

function landing_traction_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-traction.css' );
}
add_action( 'init', 'landing_traction_editor_styles' );
