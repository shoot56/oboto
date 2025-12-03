<?php

/**
 * Enqueue scripts and styles for block.
 */
function hero_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('hero', get_template_directory_uri() . '/css/hero.css', array(), filemtime(get_template_directory() . '/css/hero.css'));
	wp_register_script('hero-script', get_template_directory_uri() . '/blocks/hero/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'hero_scripts');


// Setup admin style for block
function hero_admin_style()
{
	wp_register_style('hero', get_template_directory_uri() . '/css/hero.css', array(), filemtime(get_template_directory() . '/css/hero.css'));
}
add_action('admin_enqueue_scripts', 'hero_admin_style');

