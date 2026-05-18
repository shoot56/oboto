<?php

/**
 * Register assets for the Landing Logos block.
 */
function landing_logos_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-logos',
		$theme_uri . '/css/landing-logos.css',
		array(),
		filemtime( $theme_dir . '/css/landing-logos.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'landing_logos_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_logos_register_assets' );

function landing_logos_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-logos.css' );
}
add_action( 'init', 'landing_logos_editor_styles' );
