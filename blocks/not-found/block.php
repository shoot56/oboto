<?php

/**
 * Enqueue scripts and styles for block.
 */
function not_found_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('not-found', get_template_directory_uri() . '/css/not-found.css', array(), filemtime(get_template_directory() . '/css/not-found.css'));
	wp_register_script('not-found-script', get_template_directory_uri() . '/blocks/not-found/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'not_found_scripts');


// Setup admin style for block
function not_found_admin_style()
{
	wp_register_style('not-found', get_template_directory_uri() . '/css/not-found.css', array(), filemtime(get_template_directory() . '/css/not-found.css'));
}
add_action('admin_enqueue_scripts', 'not_found_admin_style');

// Setup editor style for block
function not_found_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/not-found.css');
}

add_action('init', 'not_found_editor_styles');

