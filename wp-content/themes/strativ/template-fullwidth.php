<?php
/**
 * Template Name: Full Width (Elementor)
 * No theme containers — Elementor owns the layout between header and footer.
 */
get_header();
while ( have_posts() ) : the_post();
	the_content();
endwhile;
get_footer();
