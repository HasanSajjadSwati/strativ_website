<?php
/** Generic page template (also styles blog post content). */
get_header();
while ( have_posts() ) : the_post(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
	</div>
</section>

<section class="section">
	<div class="container container--narrow">
		<div class="entry-content" data-reveal><?php the_content(); ?></div>
	</div>
</section>

<?php endwhile; get_footer(); ?>
