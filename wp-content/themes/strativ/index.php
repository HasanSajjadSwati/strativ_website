<?php get_header(); ?>
<section class="section page-hero">
	<div class="container">
		<h1 class="page-hero__title" data-reveal><?php echo is_search() ? 'Search results' : 'Archive'; ?></h1>
	</div>
</section>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3" data-reveal-group>
				<?php while ( have_posts() ) : the_post(); strativ_post_card( get_the_ID() ); endwhile; ?>
			</div>
		<?php else : ?>
			<p class="empty-state">Nothing here yet — check back soon.</p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
