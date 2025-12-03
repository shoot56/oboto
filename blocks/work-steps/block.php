<?php

/**
 * Enqueue scripts and styles for block.
 */
function work_steps_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('work-steps', get_template_directory_uri() . '/css/work-steps.css', array(), filemtime(get_template_directory() . '/css/work-steps.css'));
	wp_register_script('work-steps-script', get_template_directory_uri() . '/blocks/work-steps/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'work_steps_scripts');


// Setup admin style for block
function work_steps_admin_style()
{
	wp_register_style('work-steps', get_template_directory_uri() . '/css/work-steps.css', array(), filemtime(get_template_directory() . '/css/work-steps.css'));
}
add_action('admin_enqueue_scripts', 'work_steps_admin_style');
