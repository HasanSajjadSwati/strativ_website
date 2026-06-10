<?php
/** Careers: culture intro, perks, open positions list. */
get_header(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal>Careers</span>
		<h1 class="page-hero__title" data-reveal>Do the best <span class="grad-text">work of your career</span></h1>
		<p class="page-hero__sub" data-reveal>Senior people, real ownership, products that ship. If that sounds like you, let's talk.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php strativ_section_heading( 'Why Strativ', 'Built for people who <span class="grad-text">ship</span>' ); ?>
		<div class="grid grid--3" data-reveal-group>
			<?php
			$perks = array(
				array( 'Remote-friendly', 'Work where you do your best thinking. We optimize for outcomes, not seat time.' ),
				array( 'Learning budget', 'Annual budget for courses, conferences and the tools that make you sharper.' ),
				array( 'Ship real products', 'No endless backlogs. You build things real users touch, every week.' ),
			);
			foreach ( $perks as [ $t, $d ] ) : ?>
				<div class="glass-card" data-reveal><h3><?php echo esc_html( $t ); ?></h3><p><?php echo esc_html( $d ); ?></p></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container">
		<?php strativ_section_heading( 'Open positions', 'Come <span class="grad-text">build with us</span>' ); ?>
		<?php
		$jobs = new WP_Query( array( 'post_type' => 'career', 'posts_per_page' => -1 ) );
		if ( $jobs->have_posts() ) : ?>
			<div class="job-list" data-reveal-group>
				<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
					<a class="job-row glass-card" data-reveal href="<?php the_permalink(); ?>">
						<div>
							<h3><?php the_title(); ?></h3>
							<span class="job-row__meta"><?php echo esc_html( strativ_field( 'department' ) ); ?> · <?php echo esc_html( strativ_field( 'location' ) ); ?> · <?php echo esc_html( strativ_field( 'emp_type' ) ); ?></span>
						</div>
						<span class="job-row__arrow" aria-hidden="true">&rarr;</span>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<p class="empty-state">No open positions right now — but we always want to hear from great people. Email careers@strativ.test</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
