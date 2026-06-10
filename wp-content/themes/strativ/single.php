<?php
/** Single blog post. */
get_header();
while ( have_posts() ) : the_post(); ?>

<section class="page-hero page-hero--post">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal><?php echo esc_html( get_the_date() ); ?></span>
		<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
	</div>
</section>

<?php if ( has_post_thumbnail() ) : ?>
<section class="project-hero-media" data-reveal>
	<div class="container"><?php the_post_thumbnail( 'strativ-hero' ); ?></div>
</section>
<?php endif; ?>

<section class="section">
	<div class="container container--narrow">
		<div class="entry-content" data-reveal><?php the_content(); ?></div>
	</div>
</section>

<?php endwhile; get_footer(); ?>
