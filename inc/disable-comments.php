<?php

/**
 * Disable WordPress comments globally.
 */

/**
 * Close comments and trackbacks on the frontend for all post types.
 */
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

/**
 * Hide existing comments on the frontend.
 */
add_filter('comments_array', '__return_empty_array', 10, 2);

/**
 * Disable all comment notification emails.
 */
add_filter('notify_post_author', '__return_false');
add_filter('notify_moderator', '__return_false');

/**
 * Disable comments and ping support for every post type.
 */
function oboto_disable_comments_post_type_support()
{
    $post_types = get_post_types([], 'names');

    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
        }

        if (post_type_supports($post_type, 'trackbacks')) {
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'oboto_disable_comments_post_type_support');

/**
 * Remove comments menu and screen from wp-admin.
 */
function oboto_disable_comments_admin_pages()
{
    remove_menu_page('edit-comments.php');

    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'oboto_disable_comments_admin_pages');

/**
 * Remove comments dashboard metabox.
 */
function oboto_disable_comments_dashboard_metabox()
{
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'oboto_disable_comments_dashboard_metabox');

/**
 * Remove comments icon from admin bar.
 */
function oboto_disable_comments_admin_bar()
{
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'oboto_disable_comments_admin_bar');

/**
 * Disable comment endpoints in REST API.
 */
function oboto_disable_comments_rest_endpoints($endpoints)
{
    $comments_endpoints = [
        '/wp/v2/comments',
        '/wp/v2/comments/(?P<id>[\\d]+)',
    ];

    foreach ($comments_endpoints as $endpoint) {
        if (isset($endpoints[$endpoint])) {
            unset($endpoints[$endpoint]);
        }
    }

    return $endpoints;
}
add_filter('rest_endpoints', 'oboto_disable_comments_rest_endpoints');

/**
 * Disable XML-RPC pingback methods related to comments.
 */
function oboto_disable_comments_xmlrpc_methods($methods)
{
    if (isset($methods['pingback.ping'])) {
        unset($methods['pingback.ping']);
    }

    if (isset($methods['pingback.extensions.getPingbacks'])) {
        unset($methods['pingback.extensions.getPingbacks']);
    }

    return $methods;
}
add_filter('xmlrpc_methods', 'oboto_disable_comments_xmlrpc_methods');
