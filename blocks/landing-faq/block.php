<?php

/**
 * Register assets for the Landing FAQ block.
 */
function landing_faq_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-faq',
		$theme_uri . '/css/landing-faq.css',
		array(),
		filemtime( $theme_dir . '/css/landing-faq.css' )
	);

	wp_register_script(
		'landing-faq-script',
		$theme_uri . '/blocks/landing-faq/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/landing-faq/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'landing_faq_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_faq_register_assets' );

function landing_faq_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-faq.css' );
}
add_action( 'init', 'landing_faq_editor_styles' );
