<?php

/**
 * Register assets for the Landing Hero block.
 */
function landing_hero_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-hero',
		$theme_uri . '/css/landing-hero.css',
		array(),
		filemtime( $theme_dir . '/css/landing-hero.css' )
	);
	wp_register_script(
		'landing-hero-script',
		$theme_uri . '/blocks/landing-hero/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/landing-hero/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'landing_hero_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_hero_register_assets' );

function landing_hero_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-hero.css' );
}
add_action( 'init', 'landing_hero_editor_styles' );
