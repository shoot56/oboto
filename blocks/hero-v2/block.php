<?php

/**
 * Register assets for the Hero V2 block.
 */
function hero_v2_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'hero-v2',
		$theme_uri . '/css/hero-v2.css',
		array(),
		filemtime( $theme_dir . '/css/hero-v2.css' )
	);

	wp_register_script(
		'hero-v2-script',
		$theme_uri . '/blocks/hero-v2/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/hero-v2/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hero_v2_register_assets' );
add_action( 'admin_enqueue_scripts', 'hero_v2_register_assets' );

function hero_v2_editor_styles() {
	$stylesheet_path = get_template_directory() . '/css/hero-v2.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/hero-v2.css';

	add_editor_style(
		add_query_arg(
			'ver',
			filemtime( $stylesheet_path ),
			$stylesheet_url
		)
	);
}
add_action( 'init', 'hero_v2_editor_styles' );

function hero_v2_enqueue_editor_assets() {
	hero_v2_register_assets();
	wp_enqueue_style( 'hero-v2' );
	wp_enqueue_script( 'hero-v2-script' );
}
add_action( 'enqueue_block_editor_assets', 'hero_v2_enqueue_editor_assets' );
