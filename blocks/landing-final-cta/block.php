<?php

/**
 * Register assets for the Landing Final CTA block.
 */
function landing_final_cta_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-final-cta',
		$theme_uri . '/css/landing-final-cta.css',
		array(),
		filemtime( $theme_dir . '/css/landing-final-cta.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'landing_final_cta_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_final_cta_register_assets' );

function landing_final_cta_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-final-cta.css' );
}
add_action( 'init', 'landing_final_cta_editor_styles' );
