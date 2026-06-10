<?php
/**
 * Strativ theme setup.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'STRATIV_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/acf-fields.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );
	register_nav_menus( array( 'primary' => 'Primary Menu' ) );
	add_image_size( 'strativ-card', 800, 540, true );
	add_image_size( 'strativ-hero', 1600, 900, true );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	wp_enqueue_style(
		'strativ-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'strativ-main', $uri . '/assets/css/main.css', array(), filemtime( $dir . '/assets/css/main.css' ) );

	wp_enqueue_script( 'gsap', $uri . '/assets/js/vendor/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );
	wp_enqueue_script( 'strativ-main', $uri . '/assets/js/main.js', array( 'gsap', 'gsap-scrolltrigger' ), filemtime( $dir . '/assets/js/main.js' ), true );
} );

// Elementor: set a sane default content width.
add_action( 'elementor/theme/register_locations', function ( $manager ) {
	$manager->register_all_core_location();
} );

add_filter( 'excerpt_length', fn() => 22 );
add_filter( 'excerpt_more', fn() => '&hellip;' );
