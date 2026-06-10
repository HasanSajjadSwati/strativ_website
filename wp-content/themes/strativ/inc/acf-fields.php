<?php
/**
 * ACF local field groups (code-registered so they're versioned).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	acf_add_local_field_group( array(
		'key'      => 'group_strativ_project',
		'title'    => 'Project Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'project' ) ) ),
		'fields'   => array(
			array( 'key' => 'field_str_client',     'name' => 'client',     'label' => 'Client',            'type' => 'text' ),
			array( 'key' => 'field_str_year',       'name' => 'year',       'label' => 'Year',              'type' => 'text' ),
			array( 'key' => 'field_str_tech_stack', 'name' => 'tech_stack', 'label' => 'Tech Stack (comma-separated)', 'type' => 'text' ),
			array( 'key' => 'field_str_live_url',   'name' => 'live_url',   'label' => 'Live URL',          'type' => 'url' ),
			array( 'key' => 'field_str_challenge',  'name' => 'challenge',  'label' => 'The Challenge',     'type' => 'textarea', 'rows' => 4 ),
			array( 'key' => 'field_str_solution',   'name' => 'solution',   'label' => 'Our Solution',      'type' => 'textarea', 'rows' => 4 ),
			array( 'key' => 'field_str_results',    'name' => 'results',    'label' => 'Results',           'type' => 'textarea', 'rows' => 4 ),
		),
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_strativ_career',
		'title'    => 'Position Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'career' ) ) ),
		'fields'   => array(
			array( 'key' => 'field_str_location',   'name' => 'location',   'label' => 'Location',        'type' => 'text' ),
			array( 'key' => 'field_str_emp_type',   'name' => 'emp_type',   'label' => 'Employment Type', 'type' => 'text' ),
			array( 'key' => 'field_str_department', 'name' => 'department', 'label' => 'Department',      'type' => 'text' ),
		),
	) );
} );
