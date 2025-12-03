<?php

/**
 * Enqueue scripts and styles for block.
 */
function tools_grid_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('tools-grid', get_template_directory_uri() . '/css/tools-grid.css', array(), filemtime(get_template_directory() . '/css/tools-grid.css'));
	// wp_register_script('tools-grid-script', get_template_directory_uri() . '/blocks/tools-grid/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'tools_grid_scripts');


// Setup admin style for block
function tools_grid_admin_style()
{
	wp_register_style('tools-grid', get_template_directory_uri() . '/css/tools-grid.css', array(), filemtime(get_template_directory() . '/css/tools-grid.css'));
}
add_action('admin_enqueue_scripts', 'tools_grid_admin_style');

// Setup editor style for block
function tools_grid_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/tools-grid.css');
}

add_action('init', 'tools_grid_editor_styles');

