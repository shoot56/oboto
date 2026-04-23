<?php

/**
 * Register styles and scripts for the Code Tabs block.
 */
function code_tabs_register_assets()
{
	wp_register_style(
		'code-tabs',
		get_template_directory_uri() . '/css/code-tabs.css',
		array(),
		filemtime(get_template_directory() . '/css/code-tabs.css')
	);

	wp_register_script(
		'code-tabs-script',
		get_template_directory_uri() . '/blocks/code-tabs/view-script.js',
		array(),
		filemtime(get_template_directory() . '/blocks/code-tabs/view-script.js'),
		true
	);
}
add_action('init', 'code_tabs_register_assets');
