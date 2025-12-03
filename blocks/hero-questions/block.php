<?php

/**
 * Enqueue scripts and styles for block.
 */
function hero_questions_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('hero-questions', get_template_directory_uri() . '/css/hero-questions.css', array(), filemtime(get_template_directory() . '/css/hero-questions.css'));
	wp_register_script('hero-questions-script', get_template_directory_uri() . '/blocks/hero-questions/view-script.js', ['jquery'], wp_get_theme()->get( 'Version' ));

}
add_action('wp_enqueue_scripts', 'hero_questions_scripts');


// Setup admin style for block
function hero_questions_admin_style()
{
	wp_register_style('hero-questions', get_template_directory_uri() . '/css/hero-questions.css', array(), filemtime(get_template_directory() . '/css/hero-questions.css'));
}
add_action('admin_enqueue_scripts', 'hero_questions_admin_style');

// Setup editor style for block
function hero_questions_editor_styles()
{
	add_editor_style(get_template_directory_uri() . '/css/hero-questions.css');
}

add_action('init', 'hero_questions_editor_styles');

