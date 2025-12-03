<?php

/**
 * Enqueue scripts and styles for block.
 */
function releated_posts_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('releated-posts', get_template_directory_uri() . '/css/releated-posts.css', array(), filemtime(get_template_directory() . '/css/releated-posts.css'));
	// wp_register_script('releated-posts-script', get_template_directory_uri() . '/blocks/releated-posts/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'releated_posts_scripts');


// Setup editor style for block
function releated_posts_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/releated-posts.css');
}

add_action('init', 'releated_posts_editor_styles');