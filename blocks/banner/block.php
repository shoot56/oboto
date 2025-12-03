<?php

/**
 * Enqueue scripts and styles for block.
 */
function banner_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('banner', get_template_directory_uri() . '/css/banner.css', array(), filemtime(get_template_directory() . '/css/banner.css'));
	wp_register_script('banner-script', get_template_directory_uri() . '/blocks/banner/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'banner_scripts');


// Setup admin style for block
function banner_admin_style()
{
	wp_register_style('banner', get_template_directory_uri() . '/css/banner.css', array(), filemtime(get_template_directory() . '/css/banner.css'));
}
add_action('admin_enqueue_scripts', 'banner_admin_style');
