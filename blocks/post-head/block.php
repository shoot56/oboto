<?php

/**
 * Enqueue scripts and styles for block.
 */
function post_head_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('post-head', get_template_directory_uri() . '/css/post-head.css', array(), filemtime(get_template_directory() . '/css/post-head.css'));
	// wp_register_script('post-head-script', get_template_directory_uri() . '/blocks/post-head/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'post_head_scripts');

// Setup admin style for block
function post_head_admin_style()
{
	wp_register_style('post-head', get_template_directory_uri() . '/css/post-head.css', array(), filemtime(get_template_directory() . '/css/post-head.css'));
}
add_action('admin_enqueue_scripts', 'post_head_admin_style');


// Setup editor style for block
function post_head_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/post-head.css');
}

add_action('init', 'post_head_editor_styles');