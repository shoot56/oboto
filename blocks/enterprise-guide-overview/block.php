<?php
$register_assets = static function () {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    if ( ! wp_style_is( 'enterprise-guide', 'registered' ) ) {
        wp_register_style( 'enterprise-guide', $theme_uri . '/css/enterprise-guide.css', array(), filemtime( $theme_dir . '/css/enterprise-guide.css' ) );
    }
};
add_action( 'wp_enqueue_scripts', $register_assets );
add_action( 'admin_enqueue_scripts', $register_assets );
