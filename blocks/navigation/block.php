<?php

/**
 * Enqueue scripts and styles for block.
 */
function navigation_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('navigation', get_template_directory_uri() . '/css/navigation.css', array(), filemtime(get_template_directory() . '/css/navigation.css'));
	wp_register_script('navigation-script', get_template_directory_uri() . '/blocks/navigation/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'navigation_scripts');


// Setup admin style for block
function navigation_admin_style()
{
	wp_register_style('navigation', get_template_directory_uri() . '/css/navigation.css', array(), filemtime(get_template_directory() . '/css/navigation.css'));
}
add_action('admin_enqueue_scripts', 'navigation_admin_style');

