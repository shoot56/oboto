<?php

/**
 * Register assets for the Landing Compact Trial block.
 */
function landing_compact_trial_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-compact-trial',
		$theme_uri . '/css/landing-compact-trial.css',
		array(),
		filemtime( $theme_dir . '/css/landing-compact-trial.css' )
	);

	wp_register_script(
		'landing-compact-trial-script',
		$theme_uri . '/blocks/landing-compact-trial/view-script.js',
		array(),
		filemtime( $theme_dir . '/blocks/landing-compact-trial/view-script.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'landing_compact_trial_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_compact_trial_register_assets' );

function landing_compact_trial_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-compact-trial.css' );
}
add_action( 'init', 'landing_compact_trial_editor_styles' );
