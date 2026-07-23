<?php

/**
 * Register assets for the How Obot Works block.
 */
function how_obot_works_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'how-obot-works',
		$theme_uri . '/css/how-obot-works.css',
		array(),
		filemtime( $theme_dir . '/css/how-obot-works.css' )
	);

	wp_register_script(
		'how-obot-works-script',
		$theme_uri . '/blocks/how-obot-works/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/how-obot-works/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'how_obot_works_register_assets' );
add_action( 'admin_enqueue_scripts', 'how_obot_works_register_assets' );

function how_obot_works_editor_styles() {
	$stylesheet_path = get_template_directory() . '/css/how-obot-works.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/how-obot-works.css';

	add_editor_style(
		add_query_arg(
			'ver',
			filemtime( $stylesheet_path ),
			$stylesheet_url
		)
	);
}
add_action( 'init', 'how_obot_works_editor_styles' );

function how_obot_works_enqueue_editor_assets() {
	how_obot_works_register_assets();
	wp_enqueue_style( 'how-obot-works' );
	wp_enqueue_script( 'how-obot-works-script' );
}
add_action( 'enqueue_block_editor_assets', 'how_obot_works_enqueue_editor_assets' );
