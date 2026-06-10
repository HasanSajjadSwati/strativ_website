<?php
/** Contact: info cards + CF7 form. */
get_header(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal>Contact</span>
		<h1 class="page-hero__title" data-reveal>Let's build <span class="grad-text">something real</span></h1>
		<p class="page-hero__sub" data-reveal>Tell us what you're trying to ship. We reply within one business day.</p>
	</div>
</section>

<section class="section">
	<div class="container grid grid--2 contact-layout">
		<div class="contact-info" data-reveal-group>
			<div class="glass-card contact-card" data-reveal>
				<span class="contact-card__label">Email</span>
				<a class="contact-card__value" href="mailto:hello@strativ.test">hello@strativ.test</a>
			</div>
			<div class="glass-card contact-card" data-reveal>
				<span class="contact-card__label">Phone</span>
				<a class="contact-card__value" href="tel:+15550100">+1 (555) 010-0100</a>
			</div>
			<div class="glass-card contact-card" data-reveal>
				<span class="contact-card__label">Office</span>
				<span class="contact-card__value">100 Innovation Drive, Tech District</span>
				<div class="map-placeholder" aria-hidden="true"><span>Map coming soon</span></div>
			</div>
		</div>

		<div class="contact-form glass-card" data-reveal>
			<?php
			$form = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'numberposts' => 1 ) );
			if ( $form ) {
				echo do_shortcode( '[contact-form-7 id="' . $form[0]->ID . '"]' );
			} else {
				echo '<p class="empty-state">Contact form unavailable — email us at hello@strativ.test</p>';
			}
			?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
