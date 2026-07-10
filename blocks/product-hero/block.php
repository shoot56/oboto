<?php

/**
 * Register assets for the Product Hero block.
 */
function product_hero_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'product-hero',
		$theme_uri . '/css/product-hero.css',
		array(),
		filemtime( $theme_dir . '/css/product-hero.css' )
	);

	wp_register_script(
		'product-hero-script',
		$theme_uri . '/blocks/product-hero/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/product-hero/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'product_hero_register_assets' );
add_action( 'admin_enqueue_scripts', 'product_hero_register_assets' );

function product_hero_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/product-hero.css' );
}
add_action( 'init', 'product_hero_editor_styles' );
