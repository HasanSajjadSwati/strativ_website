<?php
/**
 * Front page: hero, marquee, services, work, stats, process, testimonials, blog, CTA.
 */
get_header();
?>

<section class="hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="glow-orb glow-orb--2" aria-hidden="true"></div>
	<div class="glow-orb glow-orb--3" aria-hidden="true"></div>
	<div class="container hero__inner">
		<span class="hero__badge" data-reveal>Software house &amp; IT services</span>
		<h1 class="hero__title" data-reveal>Precision software for<br><span class="grad-text">ambitious businesses</span></h1>
		<p class="hero__sub" data-reveal>From AI products to enterprise POS, CMS and HMS platforms — designed, engineered and shipped by one accountable team.</p>
		<div class="hero__actions" data-reveal>
			<?php echo strativ_btn( home_url( '/services/' ), 'Explore services' ); ?>
			<?php echo strativ_btn( home_url( '/portfolio/' ), 'View portfolio', 'ghost' ); ?>
		</div>
	</div>
</section>

<section class="marquee" aria-hidden="true">
	<div class="marquee__track">
		<?php for ( $i = 0; $i < 2; $i++ ) : ?>
			<span>AI Solutions</span><span>•</span><span>POS Systems</span><span>•</span><span>Enterprise CMS</span><span>•</span><span>Healthcare HMS</span><span>•</span><span>Web &amp; SaaS</span><span>•</span><span>Mobile Apps</span><span>•</span>
		<?php endfor; ?>
	</div>
</section>

<section class="section" id="services">
	<div class="container">
		<?php strativ_section_heading( 'What we do', 'Capabilities that cover the <span class="grad-text">full stack</span>', 'Six practices, one standard: software that ships and scales.' ); ?>
		<div class="grid grid--3" data-reveal-group>
			<?php
			$services = array(
				array( '◆', 'AI Solutions', 'LLM copilots, document intelligence and agentic workflows that turn AI hype into measurable output.' ),
				array( '▣', 'Web & SaaS Development', 'High-performance platforms and SaaS products built on modern stacks with sub-second loads.' ),
				array( '▤', 'Mobile Apps', 'Cross-platform iOS and Android apps that feel native, ship fast and hold a 4.8★ average.' ),
				array( '▥', 'POS Systems', 'Offline-first retail and restaurant point-of-sale with real-time inventory and reporting.' ),
				array( '▦', 'CMS Platforms', 'Headless and enterprise CMS builds that let marketing move without engineering tickets.' ),
				array( '▧', 'HMS / Healthcare', 'Hospital management systems with FHIR-compliant records, labs, pharmacy and billing.' ),
			);
			foreach ( $services as [ $icon, $title, $desc ] ) : ?>
				<div class="glass-card service-card" data-reveal>
					<span class="service-card__icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
					<h3><?php echo esc_html( $title ); ?></h3>
					<p><?php echo esc_html( $desc ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--alt" id="work">
	<div class="container">
		<?php strativ_section_heading( 'Selected work', 'Projects that <span class="grad-text">moved the needle</span>' ); ?>
		<div class="grid grid--2" data-reveal-group>
			<?php
			$work = new WP_Query( array( 'post_type' => 'project', 'posts_per_page' => 4 ) );
			while ( $work->have_posts() ) : $work->the_post();
				strativ_project_card( get_the_ID() );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<div class="section-cta" data-reveal><?php echo strativ_btn( home_url( '/portfolio/' ), 'See all projects', 'ghost' ); ?></div>
	</div>
</section>

<section class="section stats">
	<div class="container">
		<div class="grid grid--4 stats__grid" data-reveal-group>
			<div class="stat" data-reveal><span class="stat__num" data-counter="120" data-suffix="+">0</span><span class="stat__label">Projects delivered</span></div>
			<div class="stat" data-reveal><span class="stat__num" data-counter="40" data-suffix="+">0</span><span class="stat__label">Active clients</span></div>
			<div class="stat" data-reveal><span class="stat__num" data-counter="9" data-suffix="">0</span><span class="stat__label">Years in business</span></div>
			<div class="stat" data-reveal><span class="stat__num" data-counter="35" data-suffix="+">0</span><span class="stat__label">Engineers &amp; designers</span></div>
		</div>
	</div>
</section>

<section class="section section--alt" id="process">
	<div class="container">
		<?php strativ_section_heading( 'How we work', 'A process built for <span class="grad-text">shipping</span>' ); ?>
		<div class="grid grid--4 process" data-reveal-group>
			<?php
			$steps = array(
				array( '01', 'Discover', 'We map your goals, users and constraints into a sharp problem definition.' ),
				array( '02', 'Design', 'Prototypes and architecture you can react to within the first two weeks.' ),
				array( '03', 'Build', 'Senior engineers ship in weekly increments with demos you can click.' ),
				array( '04', 'Scale', 'Launch, measure, harden — and grow the product with real usage data.' ),
			);
			foreach ( $steps as [ $num, $title, $desc ] ) : ?>
				<div class="process__step glass-card" data-reveal>
					<span class="process__num"><?php echo esc_html( $num ); ?></span>
					<h3><?php echo esc_html( $title ); ?></h3>
					<p><?php echo esc_html( $desc ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section" id="testimonials">
	<div class="container">
		<?php strativ_section_heading( 'Client words', 'Trusted by teams that <span class="grad-text">demand results</span>' ); ?>
		<div class="grid grid--3" data-reveal-group>
			<?php
			$quotes = array(
				array( 'Strativ rebuilt our POS across 32 branches with zero downtime. The team simply does not miss.', 'Operations Director', 'UrbanMart' ),
				array( 'The AI copilot they shipped resolves most of our support volume. It paid for itself in a quarter.', 'Head of CX', 'FinEdge' ),
				array( 'Our hospital went paperless in nine months. Their HMS is the backbone of how we operate.', 'CIO', 'St. Auburn Hospital' ),
			);
			foreach ( $quotes as [ $quote, $role, $org ] ) : ?>
				<figure class="glass-card quote-card" data-reveal>
					<blockquote>&ldquo;<?php echo esc_html( $quote ); ?>&rdquo;</blockquote>
					<figcaption><strong><?php echo esc_html( $role ); ?></strong> — <?php echo esc_html( $org ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--alt" id="insights">
	<div class="container">
		<?php strativ_section_heading( 'Latest insights', 'Thinking out <span class="grad-text">loud</span>' ); ?>
		<div class="grid grid--3" data-reveal-group>
			<?php
			$blog = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3 ) );
			while ( $blog->have_posts() ) : $blog->the_post();
				strativ_post_card( get_the_ID() );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

<section class="section cta-banner">
	<div class="container">
		<div class="cta-banner__box" data-reveal>
			<div class="glow-orb glow-orb--cta" aria-hidden="true"></div>
			<h2>Have an idea worth building?</h2>
			<p>Tell us what you're trying to ship. We'll tell you exactly how we'd build it.</p>
			<?php echo strativ_btn( home_url( '/contact/' ), 'Start a project' ); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
