<?php

/**
 * Enqueue scripts and styles for block.
 */
function latest_posts_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('latest-posts', get_template_directory_uri() . '/css/latest-posts.css', array(), filemtime(get_template_directory() . '/css/latest-posts.css'));
	wp_register_script('latest-posts-script', get_template_directory_uri() . '/blocks/latest-posts/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'latest_posts_scripts');


// Setup admin style for block
function latest_posts_admin_style()
{
	wp_register_style('latest-posts', get_template_directory_uri() . '/css/latest-posts.css', array(), filemtime(get_template_directory() . '/css/latest-posts.css'));
}
add_action('admin_enqueue_scripts', 'latest_posts_admin_style');

// Setup editor style for block
function latest_posts_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/latest-posts.css');
}

add_action('init', 'latest_posts_editor_styles');
