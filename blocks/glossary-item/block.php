<?php

/**
 * Enqueue scripts and styles for block.
 */
function glossary_item_scripts()
{
	// Register block style for frontend
	wp_register_style(
		'glossary-item',
		get_template_directory_uri() . '/css/glossary-item.css',
		array(),
		filemtime(get_template_directory() . '/css/glossary-item.css')
	);

	wp_register_script(
		'glossary-item-script',
		get_template_directory_uri() . '/blocks/glossary-item/view-script.js',
		['jquery'],
		wp_get_theme()->get('Version')
	);
}
add_action('wp_enqueue_scripts', 'glossary_item_scripts');

/**
 * Setup admin style for block.
 */
function glossary_item_admin_style()
{
	wp_register_style(
		'glossary-item',
		get_template_directory_uri() . '/css/glossary-item.css',
		array(),
		filemtime(get_template_directory() . '/css/glossary-item.css')
	);
}
add_action('admin_enqueue_scripts', 'glossary_item_admin_style');

/**
 * Setup editor style for block.
 */
function glossary_item_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/glossary-item.css');
}
add_action('init', 'glossary_item_editor_styles');

