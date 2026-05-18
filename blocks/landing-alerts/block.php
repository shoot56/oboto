<?php

/**
 * Register assets for the Landing Alerts block.
 */
function landing_alerts_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-alerts',
		$theme_uri . '/css/landing-alerts.css',
		array(),
		filemtime( $theme_dir . '/css/landing-alerts.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'landing_alerts_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_alerts_register_assets' );

function landing_alerts_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-alerts.css' );
}
add_action( 'init', 'landing_alerts_editor_styles' );
