<?php

/**
 * Enqueue scripts and styles for block.
 */
function button_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('button', get_template_directory_uri() . '/css/button.css', array(), filemtime(get_template_directory() . '/css/button.css'));
	wp_register_script('button-script', get_template_directory_uri() . '/blocks/button/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'button_scripts');


// Setup admin style for block
function button_admin_style()
{
	wp_register_style('button', get_template_directory_uri() . '/css/button.css', array(), filemtime(get_template_directory() . '/css/button.css'));
}
add_action('admin_enqueue_scripts', 'button_admin_style');
