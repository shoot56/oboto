<?php

/**
 * Enqueue scripts and styles for block.
 */
function author_blog_scripts()
{
    //  Rgeister blcok style for frontend 
    wp_register_style('author-blog', get_template_directory_uri() . '/css/author-blog.css', array(), filemtime(get_template_directory() . '/css/author-blog.css'));
    wp_register_script('author-blog-script', get_template_directory_uri() . '/blocks/blog/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'author_blog_scripts');

// Setup admin style for block
function author_blog_admin_style()
{
    wp_register_style('author-blog', get_template_directory_uri() . '/css/author-blog.css', array(), filemtime(get_template_directory() . '/css/author-blog.css'));
}
add_action('admin_enqueue_scripts', 'author_blog_admin_style');


// Setup editor style for block
function author_blog_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/css/author-blog.css');
}

add_action('init', 'author_blog_editor_styles');
