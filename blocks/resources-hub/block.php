<?php

/**
 * Enqueue scripts and styles for resources-hub block.
 */
function resources_hub_scripts()
{
    wp_register_style(
        'resources-hub',
        get_template_directory_uri() . '/css/resources-hub.css',
        array(),
        filemtime(get_template_directory() . '/css/resources-hub.css')
    );
}
add_action('wp_enqueue_scripts', 'resources_hub_scripts');

function resources_hub_admin_style()
{
    wp_register_style(
        'resources-hub',
        get_template_directory_uri() . '/css/resources-hub.css',
        array(),
        filemtime(get_template_directory() . '/css/resources-hub.css')
    );
}
add_action('admin_enqueue_scripts', 'resources_hub_admin_style');

function resources_hub_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/css/resources-hub.css');
}
add_action('init', 'resources_hub_editor_styles');
