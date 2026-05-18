<?php

/**
 * Register assets for the Landing Video block.
 */
function landing_video_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-video',
		$theme_uri . '/css/landing-video.css',
		array(),
		filemtime( $theme_dir . '/css/landing-video.css' )
	);

	wp_register_script(
		'landing-video-script',
		$theme_uri . '/blocks/landing-video/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/landing-video/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'landing_video_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_video_register_assets' );

function landing_video_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-video.css' );
}
add_action( 'init', 'landing_video_editor_styles' );
