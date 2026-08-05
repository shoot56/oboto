<?php

/**
 * Enqueue scripts and styles for block.
 */
function banner_scripts()
{
	//  Rgeister blcok style for frontend 
	wp_register_style('banner', get_template_directory_uri() . '/css/banner.css', array(), filemtime(get_template_directory() . '/css/banner.css'));
	wp_register_script(
		'banner-script',
		get_template_directory_uri() . '/blocks/banner/view-script.js',
		array(),
		filemtime(get_template_directory() . '/blocks/banner/view-script.js'),
		false
	);
	wp_enqueue_script('banner-script');
}
add_action('wp_enqueue_scripts', 'banner_scripts');

/**
 * Prevent performance plugins from delaying the banner interaction script.
 *
 * @param string $tag    Script HTML tag.
 * @param string $handle Registered script handle.
 * @return string
 */
function banner_script_tag($tag, $handle)
{
	if ('banner-script' !== $handle) {
		return $tag;
	}

	return str_replace('<script ', '<script data-cfasync="false" nowprocket ', $tag);
}
add_filter('script_loader_tag', 'banner_script_tag', 10, 2);


// Setup admin style for block
function banner_admin_style()
{
	wp_register_style('banner', get_template_directory_uri() . '/css/banner.css', array(), filemtime(get_template_directory() . '/css/banner.css'));
}
add_action('admin_enqueue_scripts', 'banner_admin_style');
