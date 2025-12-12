<?php

/**
 * Enqueue scripts and styles for learning-center-single block.
 */
function learning_center_single_scripts()
{
    // Register block style for frontend 
    wp_register_style('learning-center', get_template_directory_uri() . '/css/learning-center.css', array(), filemtime(get_template_directory() . '/css/learning-center.css'));
}
add_action('wp_enqueue_scripts', 'learning_center_single_scripts');

// Setup admin style for block
function learning_center_single_admin_style()
{
    wp_register_style('learning-center', get_template_directory_uri() . '/css/learning-center.css', array(), filemtime(get_template_directory() . '/css/learning-center.css'));
}
add_action('admin_enqueue_scripts', 'learning_center_single_admin_style');

// Setup editor style for block
function learning_center_single_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/css/learning-center.css');
}
add_action('init', 'learning_center_single_editor_styles');
