<?php

/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @since 1.0.0
 */

/**
 * Add theme support.
 */
function theme_setup()
{
    /*
        * Make theme available for translation.
        * Translations can be filed in the /languages/ directory.
        * If you're building a theme based on nobs, use a find and replace
        * to change 'nobs' to the name of your theme in all the template files.
        */
    load_theme_textdomain(wp_get_theme()->get('Name'), get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
        * Let WordPress manage the document title.
        * By adding theme support, we declare that this theme does not use a
        * hard-coded <title> tag in the document head, and expect WordPress to
        * provide it for us.
        */
    // add_theme_support('title-tag');

    /*
        * Enable support for Post Thumbnails on posts and pages.
        *
        * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
        */
    add_theme_support('post-thumbnails');


    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
        array(
            'main-menu'   => esc_html__('Primary', wp_get_theme()->get('Name')),
            'footer-menu' => esc_html__('Footer', wp_get_theme()->get('Name')),
        )
    );

    /*
        * Switch default core markup for search form, comment form, and comments
        * to output valid HTML5.
        */
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
        'custom-background',
        apply_filters(
            'nobs_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}

add_action('after_setup_theme', 'theme_setup');


//Theme General Settings
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Theme General Settings',
        'menu_title' => 'Theme Settings',
        'menu_slug'  => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect'   => false
    ));
}


/**
 * Helpers Functions
 */
require get_template_directory() . '/inc/helpers.php';

/**
 * Register Custom Post Type
 */
require get_template_directory() . '/inc/custom-post-type.php';

/**
 * Custoom Navigations
 */
require get_template_directory() . '/inc/navigations-functions.php';

/**
 * block free domain sending forms
 */
require get_template_directory() . '/inc/class-alison-contact-forms.php';


/**
 * Enqueue theme scripts and styles.
 */
function theme_scripts()
{
    wp_enqueue_style('theme-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    wp_enqueue_style('style', get_template_directory_uri() . '/css/style.css', [], filemtime(get_template_directory() . '/css/style.css'));
    wp_enqueue_style('swipper-style', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), wp_get_theme()->get('Version'), false);
    wp_enqueue_style('aos-style', 'https://unpkg.com/aos@2.3.1/dist/aos.css', array(), wp_get_theme()->get('Version'), false);

    // Prims
    wp_enqueue_style('prism', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.css', array(), wp_get_theme()->get('Version'), false);




    // JS
    wp_enqueue_script('custom', get_template_directory_uri() . '/js/script.js', array('jquery'), filemtime(get_template_directory() . '/js/script.js'), false, array("in_footer"));

    wp_enqueue_script('lottie-player', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js', array(), null,  array(
        'in_footer' => true,
        'strategy'  => 'defer',
    ));

    wp_enqueue_script('swipper-script', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null,  array(
        'in_footer' => true,
        'strategy'  => 'defer',
    ));

    wp_enqueue_script('aos-script', 'https://unpkg.com/aos@2.3.1/dist/aos.js', array(), null,  array(
        'in_footer' => true,
        'strategy'  => 'defer',
    ));

    // The core GSAP library
    wp_enqueue_script('gsap-js', 'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js', array(), false, true);
    // ScrollTrigger - with gsap.js passed as a dependency
    wp_enqueue_script('gsap-st', 'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/ScrollTrigger.min.js', array('gsap-js'), false, true);


    //Highlight js
    wp_enqueue_script('highlight', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js', array('custom'), null,  array(
        'in_footer' => true,
    ));

    // Prims
    wp_enqueue_script('prism-main', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js', array('custom'), null,  array(
        'in_footer' => true,
    ));

    wp_enqueue_script('prism-autoloader', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js', array('custom', 'prism-main'), null,  array(
        'in_footer' => true,
    ));
}

add_action('wp_enqueue_scripts', 'theme_scripts');

// Setup  admin style
function admin_style()
{
    wp_enqueue_style('admin-styles', get_template_directory_uri() . '/css/admin.css', array(), filemtime(get_template_directory() . '/css/admin.css'));
    wp_enqueue_style('swipper-style', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), wp_get_theme()->get('Version'), false);
}

add_action('admin_enqueue_scripts', 'admin_style');

// ACF Display Custom Fields
add_filter('acf/settings/remove_wp_meta_box', '__return_false');


add_filter('wp_all_import_is_enabled_stream_filter', 'wpai_wp_all_import_is_enabled_stream_filter', 10, 1);
function wpai_wp_all_import_is_enabled_stream_filter($enable_strem_filter)
{
    return FALSE;
}

add_filter('wp_all_import_csv_to_xml_remove_non_ascii_characters', 'wpai_wp_all_import_csv_to_xml_remove_non_ascii_characters', 10, 1);
function wpai_wp_all_import_csv_to_xml_remove_non_ascii_characters($remove_non_ascii_characters)
{
    return FALSE;
}

remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);


// Git updater override dot org
add_filter('gu_ignore_dot_org', '__return_true');

// Flush the rewrite rules
// flush_rewrite_rules();


// Custom JS for blocks styles
function mytheme_register_block_styles()
{
    wp_enqueue_script(
        'theme-block-styles',
        get_stylesheet_directory_uri() . '/js/block-styles.js',
        array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'),
        filemtime(get_stylesheet_directory() . '/js/block-styles.js')
    );
}
add_action('enqueue_block_editor_assets', 'mytheme_register_block_styles');



/**
 * Learning Center URLs
 *
 * The Learning Center now uses the dedicated CPT `learning-center` with the rewrite
 * slug `resources/learning-center`. Old category-based rewrite rules would hijack
 * those URLs and cause 404s, so they are removed.
 *
 * We also flush rewrite rules once per "rewrite version" after this change.
 */
add_action('init', function () {
    // Bump this string whenever Learning Center CPT/taxonomy rewrites change.
    // This avoids having to manually visit Permalinks settings.
    $rewrite_version = 'lc_rewrite_v3_resources_learning_center_and_taxonomy';

    if (get_option('oboto_learning_center_rewrite_flushed') === $rewrite_version) {
        return;
    }

    flush_rewrite_rules(false);
    update_option('oboto_learning_center_rewrite_flushed', $rewrite_version);
}, 20);

/**
 * Learning Center taxonomy fallback routing
 *
 * Some environments may keep stale rewrite rules despite a flush. This ensures
 * Learning Center category URLs keep working:
 * /resources/learning-center/category/{term}/
 */
add_filter('option_rewrite_rules', function ($rules) {
    // Ensure Learning Center taxonomy archives work even if DB rewrite rules are stale.
    if (!is_array($rules)) {
        return $rules;
    }

    $lc_rules = [
        // /resources/learning-center/category/{term}/
        'resources/learning-center/category/([^/]+)/?$' => 'index.php?learning-center-category=$matches[1]',
    ];

    // Prepend so it takes priority over more generic rules.
    return $lc_rules + $rules;
});


add_filter('wpcf7_skip_mail', 'disable_cf7_email_sending', 10, 2);
function disable_cf7_email_sending($skip_mail, $contact_form)
{
    // Replace 96b77a0 with your actual form ID or check by title
    if ($contact_form->id() == '96b77a0' || $contact_form->title() == 'Subscribe Form') {
        return true; // Skip sending email
    }
    return $skip_mail;
}

// Rewrite rules blog
function custom_blog_permalink($permalink, $post)
{
    // Skip previews
    if (is_preview() || get_post_status($post) !== 'publish') {
        return $permalink;
    }

    if (get_post_type($post) === 'post') {
        $categories = get_the_category($post->ID);
        foreach ($categories as $category) {
            if ($category->slug === 'blog') {
                return home_url('blog/' . $post->post_name . '/');
            }
        }
    }
    return $permalink;
}
add_filter('post_link', 'custom_blog_permalink', 10, 2);


function custom_blog_rewrite_rules($rules)
{
    $new_rules = [
        'blog/([^/]+)/?$' => 'index.php?name=$matches[1]&category_name=blog',
    ];
    return $new_rules + $rules;
}
add_filter('rewrite_rules_array', 'custom_blog_rewrite_rules');

/**
 * Override Yoast canonical for Learning Center posts
 */
add_filter('wpseo_canonical', function ($canonical) {
    // Skip in preview
    if (is_preview()) {
        return $canonical;
    }

    if (is_single() && in_category('blog')) {
        global $post;
        return home_url('blog/' . $post->post_name . '/');
    }
    return $canonical;
});

/**
 * Redirect old post URLs to /blog/post-name/ if the post is in the Blog category
 */
add_action('template_redirect', function () {

    if (is_preview()) {
        return;
    }

    if (!is_single()) {
        return;
    }

    global $post;

    // Only redirect posts in category "blog"
    if (!in_category('blog', $post)) {
        return;
    }

    $new_url = home_url('blog/' . $post->post_name . '/');
    $current_url = home_url($_SERVER['REQUEST_URI']);

    // Prevent redirect loop
    if (trailingslashit($current_url) === trailingslashit($new_url)) {
        return;
    }

    wp_redirect($new_url, 301);
    exit;
});
