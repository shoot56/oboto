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

	wp_register_script(
		'product-feature-script',
		$theme_uri . '/blocks/product-feature/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/product-feature/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'product_feature_register_assets' );
add_action( 'admin_enqueue_scripts', 'product_feature_register_assets' );

function product_feature_editor_styles() {
	$stylesheet_path = get_template_directory() . '/css/product-feature.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/product-feature.css';

	add_editor_style(
		add_query_arg(
			'ver',
			filemtime( $stylesheet_path ),
			$stylesheet_url
		)
	);
}
add_action( 'init', 'product_feature_editor_styles' );

function product_feature_enqueue_editor_assets() {
	product_feature_register_assets();
	wp_enqueue_style( 'product-feature' );
	wp_enqueue_script( 'product-feature-script' );
}
add_action( 'enqueue_block_editor_assets', 'product_feature_enqueue_editor_assets' );
