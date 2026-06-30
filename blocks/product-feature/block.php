<?php

/**
 * Register assets for the Product Feature block.
 */
function product_feature_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'product-feature',
		$theme_uri . '/css/product-feature.css',
		array(),
		filemtime( $theme_dir . '/css/product-feature.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'product_feature_register_assets' );
add_action( 'admin_enqueue_scripts', 'product_feature_register_assets' );

function product_feature_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/product-feature.css' );
}
add_action( 'init', 'product_feature_editor_styles' );
