<?php
add_editor_style( 'css/enterprise-guide.css' );
$register_assets = static function () {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    if ( ! wp_style_is( 'enterprise-guide', 'registered' ) ) {
        wp_register_style( 'enterprise-guide', $theme_uri . '/css/enterprise-guide.css', array(), filemtime( $theme_dir . '/css/enterprise-guide.css' ) );
    }
    wp_register_script( 'enterprise-guide-hubspot', 'https://js.hsforms.net/forms/embed/v2.js', array(), null, true );
    wp_register_script( 'enterprise-guide-form', $theme_uri . '/blocks/enterprise-guide-hero/view-script.js', array( 'enterprise-guide-hubspot' ), filemtime( $theme_dir . '/blocks/enterprise-guide-hero/view-script.js' ), true );
};
add_action( 'wp_enqueue_scripts', $register_assets );
add_action( 'admin_enqueue_scripts', $register_assets );
