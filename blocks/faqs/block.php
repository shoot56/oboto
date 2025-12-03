<?php

/**
 * Enqueue scripts and styles for block.
 */
function faqs_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('faqs', get_template_directory_uri() . '/css/faqs.css', array(), filemtime(get_template_directory() . '/css/faqs.css'));
	wp_register_script('faqs-script', get_template_directory_uri() . '/blocks/faqs/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'faqs_scripts');


// Setup admin style for block
function faqs_admin_style()
{
	wp_register_style('faqs', get_template_directory_uri() . '/css/faqs.css', array(), filemtime(get_template_directory() . '/css/faqs.css'));
}
add_action('admin_enqueue_scripts', 'faqs_admin_style');

// Setup editor style for block
function faqs_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/faqs.css');
}

add_action('init', 'faqs_editor_styles');

