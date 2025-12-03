<?php

/**
 * Enqueue scripts and styles for block.
 */
function quote_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('quote', get_template_directory_uri() . '/css/quote.css', array(), filemtime(get_template_directory() . '/css/quote.css'));
	wp_register_script('quote-script', get_template_directory_uri() . '/blocks/quote/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'quote_scripts');


// Setup admin style for block
function quote_admin_style()
{
	wp_register_style('quote', get_template_directory_uri() . '/css/quote.css', array(), filemtime(get_template_directory() . '/css/quote.css'));
}
add_action('admin_enqueue_scripts', 'quote_admin_style');
