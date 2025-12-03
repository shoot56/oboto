<?php

/**
 * Enqueue scripts and styles for block.
 */
function oboto_faqs_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('oboto-faqs', get_template_directory_uri() . '/css/oboto-faqs.css', array(), filemtime(get_template_directory() . '/css/oboto-faqs.css'));
	wp_register_script('oboto-faqs-script', get_template_directory_uri() . '/blocks/oboto-faqs/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'oboto_faqs_scripts');


// Setup admin style for block
function oboto_faqs_admin_style()
{
	wp_register_style('oboto-faqs', get_template_directory_uri() . '/css/oboto-faqs.css', array(), filemtime(get_template_directory() . '/css/oboto-faqs.css'));
}
add_action('admin_enqueue_scripts', 'oboto_faqs_admin_style');

// Setup editor style for block
function oboto_faqs_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/oboto-faqs.css');
}

add_action('init', 'oboto_faqs_editor_styles');

