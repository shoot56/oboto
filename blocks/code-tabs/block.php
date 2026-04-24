<?php

/**
 * Register styles and scripts for the Code Tabs block.
 */
function code_tabs_register_assets()
{
	$style_path = get_template_directory() . '/css/code-tabs.css';
	$script_path = get_template_directory() . '/blocks/code-tabs/view-script.js';
	$shiki_bundle_path = get_template_directory() . '/blocks/code-tabs/shiki.bundle.js';

	wp_register_style(
		'code-tabs',
		get_template_directory_uri() . '/css/code-tabs.css',
		array(),
		filemtime($style_path)
	);

	wp_register_script(
		'code-tabs-script',
		get_template_directory_uri() . '/blocks/code-tabs/view-script.js',
		array(),
		filemtime($script_path),
		true
	);

	wp_add_inline_script(
		'code-tabs-script',
		'window.codeTabsConfig=window.codeTabsConfig||{};window.codeTabsConfig.shikiUrl=' . wp_json_encode(
			get_template_directory_uri() . '/blocks/code-tabs/shiki.bundle.js?ver=' . filemtime($shiki_bundle_path)
		) . ';',
		'before'
	);
}
add_action('init', 'code_tabs_register_assets');
