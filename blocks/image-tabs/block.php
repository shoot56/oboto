<?php

/**
 * Enqueue scripts and styles for block.
 */
function image_tabs_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('image-tabs', get_template_directory_uri() . '/css/image-tabs.css', array(), filemtime(get_template_directory() . '/css/image-tabs.css'));
	wp_register_script('image-tabs-script', get_template_directory_uri() . '/blocks/image-tabs/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'image_tabs_scripts');


// Setup admin style for block
function image_tabs_admin_style()
{
	wp_register_style('image-tabs', get_template_directory_uri() . '/css/image-tabs.css', array(), filemtime(get_template_directory() . '/css/image-tabs.css'));
}
add_action('admin_enqueue_scripts', 'image_tabs_admin_style');
