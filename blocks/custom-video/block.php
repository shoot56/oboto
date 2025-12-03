<?php

/**
 * Enqueue scripts and styles for block.
 */
function custom_video_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('custom-video', get_template_directory_uri() . '/css/custom-video.css', array(), filemtime(get_template_directory() . '/css/custom-video.css'));
	wp_register_script('custom-video-script', get_template_directory_uri() . '/blocks/custom-video/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'custom_video_scripts');


// Setup admin style for block
function custom_video_admin_style()
{
	wp_register_style('custom-video', get_template_directory_uri() . '/css/custom-video.css', array(), filemtime(get_template_directory() . '/css/custom-video.css'));
}
add_action('admin_enqueue_scripts', 'custom_video_admin_style');

// Setup editor style for block
function custom_video_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/custom-video.css');
}

add_action('init', 'custom_video_editor_styles');

