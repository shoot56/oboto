<?php

/**
 * Register assets for the Comparison Hero block.
 */
function comparison_hero_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'comparison-hero',
		$theme_uri . '/css/comparison-hero.css',
		array(),
		filemtime( $theme_dir . '/css/comparison-hero.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'comparison_hero_register_assets' );
add_action( 'admin_enqueue_scripts', 'comparison_hero_register_assets' );

function comparison_hero_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/comparison-hero.css' );
}
add_action( 'init', 'comparison_hero_editor_styles' );
