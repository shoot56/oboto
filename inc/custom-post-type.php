<?php

/**
 * Register Learning Center Custom Post Type and Taxonomy
 */
function register_learning_center_post_type(){

     // ============= Learning Center =============================
// ============================================================

      // ======Post=====
     //================

     $labels = array(
        "name"                     => esc_html__("Learning Center", "obot"),
        "singular_name"            => esc_html__("Learning Center", "obot"),
        "menu_name"                => esc_html__("Learning Center", "obot"),
        "all_items"                => esc_html__("All Learning Center", "obot"),
        "add_new"                  => esc_html__("Add new Learning Center", "obot"),
        "add_new_item"             => esc_html__("Add new Learning Center", "obot"),
        "edit_item"                => esc_html__("Edit Learning Center", "obot"),
        "new_item"                 => esc_html__("New Learning Center", "obot"),
        "view_item"                => esc_html__("View Learning Center", "obot"),
        "view_items"               => esc_html__("View Learning Center", "obot"),
        "search_items"             => esc_html__("Search Learning Center", "obot"),
        "not_found"                => esc_html__("No Learning Center found", "obot"),
        "not_found_in_trash"       => esc_html__("No Learning Center found in trash", "obot"),
        "parent"                   => esc_html__("Parent Learning Center:", "obot"),
        "featured_image"           => esc_html__("Featured image for this Learning Center", "obot"),
        "set_featured_image"       => esc_html__("Set featured image for this Learning Center", "obot"),
        "remove_featured_image"    => esc_html__("Remove featured image for this Learning Center", "obot"),
        "use_featured_image"       => esc_html__("Use as featured image for this Learning Center", "obot"),
        "archives"                 => esc_html__("Learning Center archives", "obot"),
        "insert_into_item"         => esc_html__("Insert into Learning Center", "obot"),
        "uploaded_to_this_item"    => esc_html__("Upload to this Learning Center", "obot"),
        "filter_items_list"        => esc_html__("Filter Learning Center list", "obot"),
        "items_list_navigation"    => esc_html__("Learning Center list navigation", "obot"),
        "items_list"               => esc_html__("Learning Center list", "obot"),
        "attributes"               => esc_html__("Learning Center attributes", "obot"),
        "name_admin_bar"           => esc_html__("Learning Center", "obot"),
        "item_published"           => esc_html__("Learning Center published", "obot"),
        "item_published_privately" => esc_html__("Learning Center published privately.", "obot"),
        "item_reverted_to_draft"   => esc_html__("Learning Center reverted to draft.", "obot"),
        "item_trashed"             => esc_html__("Learning Center trashed.", "obot"),
        "item_scheduled"           => esc_html__("Learning Center scheduled", "obot"),
        "item_updated"             => esc_html__("Learning Center updated.", "obot"),
        "parent_item_colon"        => esc_html__("Parent Learning Center:", "obot"),
    );

    $args = array(
        'label'                 => esc_html__('Learning Center', 'obot'),
        'labels'                => $labels,
        'description'           => esc_html__('Custom Post Type for Learning Center', 'obot'),
        'public'                => true,
        "publicly_queryable"    => true,
        "show_ui"               => true,
        'show_in_rest'          => true,
        "rest_base"             => "learning-center",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "rest_namespace"        => "wp/v2",
        "has_archive"           => true,
        "show_in_menu"          => true,
        "show_in_nav_menus"     => true,
        "delete_with_user"      => false,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "can_export"            => true,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'author', 'custom-fields'),
        'taxonomies'            => array('learning-center-category'), // Use the custom taxonomy
        'rewrite'               => array(
            'slug'       => 'learning-center',
            'with_front' => true 
        ),
        "query_var"             => true,
        'menu_icon'             => 'dashicons-book',
        "show_in_graphql"       => false,
    );

    register_post_type('learning-center', $args);

     // ======Taxonomy=====
    //====================

     $labels = array(
        'name'              => esc_html__('Learning Center Categories', 'obot'),
        'singular_name'     => esc_html__('Learning Center Category', 'obot'),
        'search_items'      => esc_html__('Search Learning Center Categories', 'obot'),
        'all_items'         => esc_html__('All Learning Center Categories', 'obot'),
        'parent_item'       => esc_html__('Parent Learning Center Category', 'obot'),
        'parent_item_colon' => esc_html__('Parent Learning Center Category:', 'obot'),
        'edit_item'         => esc_html__('Edit Learning Center Category', 'obot'),
        'update_item'       => esc_html__('Update Learning Center Category', 'obot'),
        'add_new_item'      => esc_html__('Add New Learning Center Category', 'obot'),
        'new_item_name'     => esc_html__('New Learning Center Category Name', 'obot'),
        'menu_name'         => esc_html__('Categories', 'obot'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => 'learning-center-category',
        'rewrite'           => array(
            'slug'       => 'resources/learning-center/category',
            'with_front' => false
        ),
    );

    register_taxonomy('learning-center-category', array('learning-center'), $args);


}

add_action('init', 'register_learning_center_post_type');

