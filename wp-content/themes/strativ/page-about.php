<?php
/** About: story, values, team, stats reprise. */
get_header(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal>About Strativ</span>
		<h1 class="page-hero__title" data-reveal>One team, <span class="grad-text">fully accountable</span></h1>
		<p class="page-hero__sub" data-reveal>We're a software house that treats every product like our own — because our name ships with it.</p>
	</div>
</section>

<section class="section">
	<div class="container grid grid--2 about-story">
		<div data-reveal>
			<h2 class="section-title">Built by engineers,<br>run like a product company</h2>
		</div>
		<div data-reveal>
			<p>Strativ started in 2017 with a simple frustration: too much software gets built slowly, handed off badly, and maintained by nobody. We set out to be the opposite — a senior, cross-functional team that designs, builds and operates software end to end.</p>
			<p>Today we ship AI products, point-of-sale platforms, enterprise CMS builds and hospital systems for clients across three continents. Different domains, same discipline.</p>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container">
		<?php strativ_section_heading( 'Our values', 'What we <span class="grad-text">refuse to compromise</span>' ); ?>
		<div class="grid grid--3" data-reveal-group>
			<?php
			$values = array(
				array( 'Ship weekly', 'Working software every week. Demos you can click, not slide decks.' ),
				array( 'Senior only', 'Every project is staffed with senior engineers and designers. No bait-and-switch.' ),
				array( 'Own the outcome', 'We measure ourselves on your metrics — conversion, uptime, resolution rates.' ),
			);
			foreach ( $values as [ $t, $d ] ) : ?>
				<div class="glass-card" data-reveal><h3><?php echo esc_html( $t ); ?></h3><p><?php echo esc_html( $d ); ?></p></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php strativ_section_heading( 'The team', 'People behind the <span class="grad-text">platforms</span>' ); ?>
		<div class="grid grid--4 team-grid" data-reveal-group>
			<?php
			$team = array(
				array( 'Ayaan Malik', 'Founder & CEO' ), array( 'Sara Lindqvist', 'CTO' ),
				array( 'Daniel Okoye', 'Head of AI' ), array( 'Mei Tanaka', 'Design Director' ),
				array( 'Omar Farouk', 'Lead Engineer' ), array( 'Julia Berg', 'Delivery Lead' ),
				array( 'Ravi Sharma', 'Mobile Lead' ), array( 'Lena Novak', 'QA Lead' ),
			);
			foreach ( $team as [ $name, $role ] ) : ?>
				<div class="team-card glass-card" data-reveal>
					<div class="team-card__avatar" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></div>
					<h3><?php echo esc_html( $name ); ?></h3>
					<p><?php echo esc_html( $role ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
