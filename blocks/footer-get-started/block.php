<?php

/**
 * Enqueue scripts and styles for block.
 */
function footer_get_started_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('footer-get-started', get_template_directory_uri() . '/css/footer-get-started.css', array(), filemtime(get_template_directory() . '/css/footer-get-started.css'));
	// wp_register_script('footer-get-started-script', get_template_directory_uri() . '/blocks/footer-get-started/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'footer_get_started_scripts');


// Setup admin style for block
function footer_get_started_admin_style()
{
	wp_register_style('footer-get-started', get_template_directory_uri() . '/css/footer-get-started.css', array(), filemtime(get_template_directory() . '/css/footer-get-started.css'));
}
add_action('admin_enqueue_scripts', 'footer_get_started_admin_style');

// Setup editor style for block
function footer_get_started_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/footer-get-started.css');
}

add_action('init', 'footer_get_started_editor_styles');

