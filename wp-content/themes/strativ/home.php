<?php
/** Blog index. */
get_header(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal>Insights</span>
		<h1 class="page-hero__title" data-reveal>Notes from the <span class="grad-text">build floor</span></h1>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3" data-reveal-group>
				<?php while ( have_posts() ) : the_post(); strativ_post_card( get_the_ID() ); endwhile; ?>
			</div>
			<div class="pagination" data-reveal><?php the_posts_pagination( array( 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?></div>
		<?php else : ?>
			<p class="empty-state">No articles yet — check back soon.</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
