<?php

/**
 * Register assets for the Product Feature V2 block.
 */
function product_feature_v2_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'product-feature-v2',
		$theme_uri . '/css/product-feature-v2.css',
		array(),
		filemtime( $theme_dir . '/css/product-feature-v2.css' )
	);

	wp_register_script(
		'product-feature-v2-script',
		$theme_uri . '/blocks/product-feature-v2/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/product-feature-v2/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'product_feature_v2_register_assets' );
add_action( 'admin_enqueue_scripts', 'product_feature_v2_register_assets' );

function product_feature_v2_editor_styles() {
	$stylesheet_path = get_template_directory() . '/css/product-feature-v2.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/product-feature-v2.css';

	add_editor_style(
		add_query_arg(
			'ver',
			filemtime( $stylesheet_path ),
			$stylesheet_url
		)
	);
}
add_action( 'init', 'product_feature_v2_editor_styles' );

function product_feature_v2_enqueue_editor_assets() {
	product_feature_v2_register_assets();
	wp_enqueue_style( 'product-feature-v2' );
	wp_enqueue_script( 'product-feature-v2-script' );
}
add_action( 'enqueue_block_editor_assets', 'product_feature_v2_enqueue_editor_assets' );
