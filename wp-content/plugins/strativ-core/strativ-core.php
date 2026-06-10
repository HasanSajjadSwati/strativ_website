<?php
/**
 * Plugin Name: Strativ Core
 * Description: Custom post types and taxonomies for the Strativ website. Keep active regardless of theme.
 * Version: 1.0.0
 * Author: Strativ
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function strativ_core_register() {
	register_post_type( 'project', array(
		'labels'        => array(
			'name'          => 'Projects',
			'singular_name' => 'Project',
			'add_new_item'  => 'Add New Project',
			'edit_item'     => 'Edit Project',
		),
		'public'        => true,
		'menu_icon'     => 'dashicons-portfolio',
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'portfolio' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'project_category', 'project', array(
		'labels'       => array(
			'name'          => 'Project Categories',
			'singular_name' => 'Project Category',
		),
		'public'       => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'project-category' ),
	) );

	register_post_type( 'career', array(
		'labels'        => array(
			'name'          => 'Careers',
			'singular_name' => 'Position',
			'add_new_item'  => 'Add New Position',
			'edit_item'     => 'Edit Position',
		),
		'public'        => true,
		'menu_icon'     => 'dashicons-businessperson',
		'menu_position' => 6,
		'supports'      => array( 'title', 'editor' ),
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'careers' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'strativ_core_register' );

register_activation_hook( __FILE__, function () {
	strativ_core_register();
	flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
