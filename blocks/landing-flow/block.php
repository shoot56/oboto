<?php

/**
 * Register assets for the Landing Flow block.
 */
function landing_flow_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-flow',
		$theme_uri . '/css/landing-flow.css',
		array(),
		filemtime( $theme_dir . '/css/landing-flow.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'landing_flow_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_flow_register_assets' );

function landing_flow_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-flow.css' );
}
add_action( 'init', 'landing_flow_editor_styles' );
