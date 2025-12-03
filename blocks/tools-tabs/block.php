<?php

/**
 * Enqueue scripts and styles for block.
 */
function tools_tabs_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('tools-tabs', get_template_directory_uri() . '/css/tools-tabs.css', array(), filemtime(get_template_directory() . '/css/tools-tabs.css'));
	wp_register_script('tools-tabs-script', get_template_directory_uri() . '/blocks/tools-tabs/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'tools_tabs_scripts');


// Setup admin style for block
function tools_tabs_admin_style()
{
	wp_register_style('tools-tabs', get_template_directory_uri() . '/css/tools-tabs.css', array(), filemtime(get_template_directory() . '/css/tools-tabs.css'));
}
add_action('admin_enqueue_scripts', 'tools_tabs_admin_style');

// Setup editor style for block
function tools_tabs_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/tools-tabs.css');
}

add_action('init', 'tools_tabs_editor_styles');

