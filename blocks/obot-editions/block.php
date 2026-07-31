<?php

/**
 * Register assets for the Obot Editions block.
 */
function obot_editions_register_assets() {
	$stylesheet_path = get_template_directory() . '/css/obot-editions.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/obot-editions.css';

	wp_register_style(
		'obot-editions',
		$stylesheet_url,
		array(),
		filemtime( $stylesheet_path )
	);
}
add_action( 'wp_enqueue_scripts', 'obot_editions_register_assets' );
add_action( 'admin_enqueue_scripts', 'obot_editions_register_assets' );

function obot_editions_editor_styles() {
	$stylesheet_path = get_template_directory() . '/css/obot-editions.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/obot-editions.css';

	add_editor_style(
		add_query_arg(
			'ver',
			filemtime( $stylesheet_path ),
			$stylesheet_url
		)
	);
}
add_action( 'init', 'obot_editions_editor_styles' );

function obot_editions_enqueue_editor_assets() {
	obot_editions_register_assets();
	wp_enqueue_style( 'obot-editions' );
}
add_action( 'enqueue_block_editor_assets', 'obot_editions_enqueue_editor_assets' );
