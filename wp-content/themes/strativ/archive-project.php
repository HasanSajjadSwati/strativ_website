<?php
/** Portfolio archive with JS category filter. */
get_header(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal>Portfolio</span>
		<h1 class="page-hero__title" data-reveal>Work that <span class="grad-text">ships</span></h1>
		<p class="page-hero__sub" data-reveal>A selection of platforms, products and systems we've taken from idea to production.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="filter-bar" data-reveal role="group" aria-label="Filter projects">
			<button class="filter-btn is-active" data-filter="all">All</button>
			<?php foreach ( get_terms( array( 'taxonomy' => 'project_category', 'hide_empty' => true ) ) as $term ) : ?>
				<button class="filter-btn" data-filter="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
			<?php endforeach; ?>
		</div>

		<?php
		$all = new WP_Query( array( 'post_type' => 'project', 'posts_per_page' => -1 ) );
		if ( $all->have_posts() ) : ?>
			<div class="grid grid--2 portfolio-grid" data-reveal-group>
				<?php while ( $all->have_posts() ) : $all->the_post(); strativ_project_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<p class="empty-state">No projects published yet — check back soon.</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
