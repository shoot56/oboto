<?php

/**
 * Enqueue scripts and styles for block.
 */
function cards_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('cards', get_template_directory_uri() . '/css/cards.css', array(), filemtime(get_template_directory() . '/css/cards.css'));
	wp_register_script('cards-script', get_template_directory_uri() . '/blocks/cards/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'cards_scripts');


// Setup admin style for block
function cards_admin_style()
{
	wp_register_style('cards', get_template_directory_uri() . '/css/cards.css', array(), filemtime(get_template_directory() . '/css/cards.css'));
}
add_action('admin_enqueue_scripts', 'cards_admin_style');
