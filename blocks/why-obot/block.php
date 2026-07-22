<?php

/**
 * Register assets for the Why Obot block.
 */
function why_obot_register_assets() {
	$stylesheet_path = get_template_directory() . '/css/why-obot.css';

	wp_register_style(
		'why-obot',
		get_template_directory_uri() . '/css/why-obot.css',
		array(),
		filemtime( $stylesheet_path )
	);
}
add_action( 'wp_enqueue_scripts', 'why_obot_register_assets' );
add_action( 'admin_enqueue_scripts', 'why_obot_register_assets' );

function why_obot_editor_styles() {
	$stylesheet_path = get_template_directory() . '/css/why-obot.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/why-obot.css';

	add_editor_style(
		add_query_arg(
			'ver',
			filemtime( $stylesheet_path ),
			$stylesheet_url
		)
	);
}
add_action( 'init', 'why_obot_editor_styles' );

function why_obot_enqueue_editor_assets() {
	why_obot_register_assets();
	wp_enqueue_style( 'why-obot' );
}
add_action( 'enqueue_block_editor_assets', 'why_obot_enqueue_editor_assets' );
