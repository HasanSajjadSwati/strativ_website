<?php
/** Project detail: hero, meta sidebar, case study, prev/next. */
get_header();
while ( have_posts() ) : the_post();
	$cats = get_the_terms( get_the_ID(), 'project_category' );
?>

<section class="page-hero page-hero--project">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal><?php echo $cats ? esc_html( implode( ' · ', wp_list_pluck( $cats, 'name' ) ) ) : 'Case study'; ?></span>
		<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
		<p class="page-hero__sub" data-reveal><?php echo esc_html( get_the_excerpt() ); ?></p>
	</div>
</section>

<?php if ( has_post_thumbnail() ) : ?>
<section class="project-hero-media" data-reveal>
	<div class="container"><?php the_post_thumbnail( 'strativ-hero' ); ?></div>
</section>
<?php endif; ?>

<section class="section">
	<div class="container project-layout">
		<aside class="project-meta glass-card" data-reveal>
			<?php
			$meta = array(
				'Client'  => strativ_field( 'client' ),
				'Year'    => strativ_field( 'year' ),
				'Stack'   => strativ_field( 'tech_stack' ),
			);
			foreach ( $meta as $label => $value ) :
				if ( ! $value ) continue;
				if ( 'Stack' === $label ) : ?>
					<div class="project-meta__row"><span class="label"><?php echo esc_html( $label ); ?></span>
						<div class="tech-tags"><?php foreach ( array_map( 'trim', explode( ',', $value ) ) as $tag ) : ?><span class="tech-tag"><?php echo esc_html( $tag ); ?></span><?php endforeach; ?></div>
					</div>
				<?php else : ?>
					<div class="project-meta__row"><span class="label"><?php echo esc_html( $label ); ?></span><span><?php echo esc_html( $value ); ?></span></div>
				<?php endif;
			endforeach;
			$url = strativ_field( 'live_url' );
			if ( $url ) echo '<a class="btn btn--ghost project-meta__link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">Visit live site &nearr;</a>';
			?>
		</aside>

		<div class="project-body">
			<?php
			$sections = array(
				'The Challenge' => strativ_field( 'challenge' ),
				'Our Solution'  => strativ_field( 'solution' ),
				'Results'       => strativ_field( 'results' ),
			);
			foreach ( $sections as $heading => $text ) :
				if ( ! $text ) continue; ?>
				<div class="project-section" data-reveal>
					<h2><span class="grad-text"><?php echo esc_html( $heading ); ?></span></h2>
					<p><?php echo esc_html( $text ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section project-nav-section">
	<div class="container project-nav" data-reveal>
		<?php
		$prev = get_previous_post();
		$next = get_next_post();
		echo '<div>' . ( $prev ? '<a class="project-nav__link" href="' . esc_url( get_permalink( $prev ) ) . '"><span class="label">&larr; Previous</span><strong>' . esc_html( get_the_title( $prev ) ) . '</strong></a>' : '' ) . '</div>';
		echo '<div class="project-nav__next">' . ( $next ? '<a class="project-nav__link" href="' . esc_url( get_permalink( $next ) ) . '"><span class="label">Next &rarr;</span><strong>' . esc_html( get_the_title( $next ) ) . '</strong></a>' : '' ) . '</div>';
		?>
	</div>
</section>

<?php endwhile; get_footer(); ?>
