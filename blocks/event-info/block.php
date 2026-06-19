<?php

/**
 * Enqueue styles for Event Info block.
 */
function event_info_scripts()
{
    $style_path = get_template_directory() . '/css/event-info.css';

    wp_register_style(
        'event-info',
        get_template_directory_uri() . '/css/event-info.css',
        array(),
        filemtime($style_path)
    );
}
add_action('wp_enqueue_scripts', 'event_info_scripts');

function event_info_admin_style()
{
    $style_path = get_template_directory() . '/css/event-info.css';

    wp_register_style(
        'event-info',
        get_template_directory_uri() . '/css/event-info.css',
        array(),
        filemtime($style_path)
    );
}
add_action('admin_enqueue_scripts', 'event_info_admin_style');

function event_info_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/css/event-info.css');
}
add_action('init', 'event_info_editor_styles');
