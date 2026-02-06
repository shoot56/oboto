<?php

/**
 * Enqueue scripts and styles for Team Members block.
 */
function team_members_scripts()
{
	// Register block style for frontend
	wp_register_style('team-members', get_template_directory_uri() . '/css/team-members.css', array(), filemtime(get_template_directory() . '/css/team-members.css'));
	wp_register_script('team-members-script', get_template_directory_uri() . '/blocks/team-members/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'team_members_scripts');


// Setup admin style for block
function team_members_admin_style()
{
	wp_register_style('team-members', get_template_directory_uri() . '/css/team-members.css', array(), filemtime(get_template_directory() . '/css/team-members.css'));
}
add_action('admin_enqueue_scripts', 'team_members_admin_style');
