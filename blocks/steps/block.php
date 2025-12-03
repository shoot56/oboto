<?php

/**
 * Enqueue scripts and styles for block.
 */
function steps_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('steps', get_template_directory_uri() . '/css/steps.css', array(), filemtime(get_template_directory() . '/css/steps.css'));
	wp_register_script('steps-script', get_template_directory_uri() . '/blocks/steps/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'steps_scripts');


// Setup admin style for block
function steps_admin_style()
{
	wp_register_style('steps', get_template_directory_uri() . '/css/steps.css', array(), filemtime(get_template_directory() . '/css/steps.css'));
}
add_action('admin_enqueue_scripts', 'steps_admin_style');
