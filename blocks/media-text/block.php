<?php

/**
 * Enqueue scripts and styles for block.
 */
function media_text_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('media-text', get_template_directory_uri() . '/css/media-text.css', array(), filemtime(get_template_directory() . '/css/media-text.css'));
	// wp_register_script('media-text-script', get_template_directory_uri() . '/blocks/footer-get-started/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'media_text_scripts');


// Setup admin style for block
function media_text_admin_style()
{
	wp_register_style('media-text', get_template_directory_uri() . '/css/media-text.css', array(), filemtime(get_template_directory() . '/css/media-text.css'));
}
add_action('admin_enqueue_scripts', 'media_text_admin_style');

// Setup editor style for block
function media_text_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/media-text.css');
}

add_action('init', 'media_text_editor_styles');

