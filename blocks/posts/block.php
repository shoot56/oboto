<?php

/**
 * Enqueue scripts and styles for block.
 */
function posts_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('posts', get_template_directory_uri() . '/css/posts.css', array(), filemtime(get_template_directory() . '/css/posts.css'));
	wp_register_script('posts-script', get_template_directory_uri() . '/blocks/posts/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'posts_scripts');


// Setup admin style for block
function posts_admin_style()
{
	wp_register_style('posts', get_template_directory_uri() . '/css/posts.css', array(), filemtime(get_template_directory() . '/css/posts.css'));
}
add_action('admin_enqueue_scripts', 'posts_admin_style');

// Setup editor style for block
function posts_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/posts.css');
}

add_action('init', 'posts_editor_styles');


