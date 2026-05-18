<?php

/**
 * Register assets for the Landing Capabilities block.
 */
function landing_capabilities_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-capabilities',
		$theme_uri . '/css/landing-capabilities.css',
		array(),
		filemtime( $theme_dir . '/css/landing-capabilities.css' )
	);

	wp_register_script(
		'landing-capabilities-script',
		$theme_uri . '/blocks/landing-capabilities/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/landing-capabilities/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'landing_capabilities_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_capabilities_register_assets' );

function landing_capabilities_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-capabilities.css' );
}
add_action( 'init', 'landing_capabilities_editor_styles' );
