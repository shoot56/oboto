<?php

/**
 * Register assets for the Product Resources block.
 */
function product_resources_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'product-resources',
		$theme_uri . '/css/product-resources.css',
		array(),
		filemtime( $theme_dir . '/css/product-resources.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'product_resources_register_assets' );
add_action( 'admin_enqueue_scripts', 'product_resources_register_assets' );

function product_resources_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/product-resources.css' );
}
add_action( 'init', 'product_resources_editor_styles' );
