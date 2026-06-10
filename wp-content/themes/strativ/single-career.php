<?php
/** Single career/position detail. */
get_header();
while ( have_posts() ) : the_post(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal><?php echo esc_html( strativ_field( 'department' ) ); ?></span>
		<h1 class="page-hero__title" data-reveal><?php the_title(); ?></h1>
		<p class="page-hero__sub" data-reveal><?php echo esc_html( strativ_field( 'location' ) ); ?> · <?php echo esc_html( strativ_field( 'emp_type' ) ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container container--narrow">
		<div class="entry-content" data-reveal><?php the_content(); ?></div>
	</div>
</section>

<section class="section cta-banner">
	<div class="container">
		<div class="cta-banner__box" data-reveal>
			<div class="glow-orb glow-orb--cta" aria-hidden="true"></div>
			<h2>Sound like you?</h2>
			<p>Send your CV and three things you've shipped to careers@strativ.test</p>
			<?php echo strativ_btn( 'mailto:careers@strativ.test', 'Apply now' ); ?>
		</div>
	</div>
</section>

<?php endwhile; get_footer(); ?>
