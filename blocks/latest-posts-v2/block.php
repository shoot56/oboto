<?php

/**
 * Register assets for the Latest Posts V2 block.
 */
function latest_posts_v2_register_assets() {
	$stylesheet_path = get_template_directory() . '/css/latest-posts-v2.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/latest-posts-v2.css';

	wp_register_style(
		'latest-posts-v2',
		$stylesheet_url,
		array(),
		filemtime( $stylesheet_path )
	);
}
add_action( 'wp_enqueue_scripts', 'latest_posts_v2_register_assets' );
add_action( 'admin_enqueue_scripts', 'latest_posts_v2_register_assets' );

function latest_posts_v2_editor_styles() {
	$stylesheet_path = get_template_directory() . '/css/latest-posts-v2.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/latest-posts-v2.css';

	add_editor_style(
		add_query_arg(
			'ver',
			filemtime( $stylesheet_path ),
			$stylesheet_url
		)
	);
}
add_action( 'init', 'latest_posts_v2_editor_styles' );

function latest_posts_v2_enqueue_editor_assets() {
	latest_posts_v2_register_assets();
	wp_enqueue_style( 'latest-posts-v2' );
}
add_action( 'enqueue_block_editor_assets', 'latest_posts_v2_enqueue_editor_assets' );
