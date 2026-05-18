<?php

/**
 * Register assets for the Landing How It Works block.
 */
function landing_how_it_works_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-how-it-works',
		$theme_uri . '/css/landing-how-it-works.css',
		array(),
		filemtime( $theme_dir . '/css/landing-how-it-works.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'landing_how_it_works_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_how_it_works_register_assets' );

function landing_how_it_works_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-how-it-works.css' );
}
add_action( 'init', 'landing_how_it_works_editor_styles' );
