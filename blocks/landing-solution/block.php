<?php

/**
 * Register assets for the Landing Solution block.
 */
function landing_solution_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'landing-solution',
		$theme_uri . '/css/landing-solution.css',
		array(),
		filemtime( $theme_dir . '/css/landing-solution.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'landing_solution_register_assets' );
add_action( 'admin_enqueue_scripts', 'landing_solution_register_assets' );

function landing_solution_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/landing-solution.css' );
}
add_action( 'init', 'landing_solution_editor_styles' );
