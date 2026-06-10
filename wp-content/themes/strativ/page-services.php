<?php
/** Services: intro hero + six alternating detail blocks + CTA. */
get_header(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal>Services</span>
		<h1 class="page-hero__title" data-reveal>Everything it takes to <span class="grad-text">ship</span></h1>
		<p class="page-hero__sub" data-reveal>Six practices under one roof — so your product is designed, built and operated by a single accountable team.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php
		$services = array(
			array( '◆', 'AI Solutions', 'We build production AI — not demos. Copilots grounded in your data, document-intelligence pipelines, and agentic workflows with human-in-the-loop controls.', array( 'LLM copilots & chat products', 'RAG & document intelligence', 'Agentic workflow automation', 'AI strategy & evaluation' ) ),
			array( '▣', 'Web & SaaS Development', 'High-performance web platforms engineered for scale: multi-tenant SaaS, customer portals and e-commerce with sub-second loads.', array( 'Multi-tenant SaaS architecture', 'Headless e-commerce', 'API design & integrations', 'Performance engineering' ) ),
			array( '▤', 'Mobile Apps', 'Cross-platform apps that feel native. One codebase, two stores, weekly releases.', array( 'Flutter & React Native', 'Offline-first sync', 'Wearable & health integrations', 'Store launch & ASO' ) ),
			array( '▥', 'POS Systems', 'Point-of-sale that never stops selling — offline-first tills, kitchen displays, inventory and multi-branch reporting.', array( 'Retail & restaurant POS', 'Hardware integrations', 'Real-time inventory', 'Multi-branch analytics' ) ),
			array( '▦', 'CMS Platforms', 'Content platforms that let marketing move fast: headless CMS builds, editorial workflows and design systems.', array( 'Headless CMS architecture', 'Editorial workflow design', 'Multi-brand theming', 'Migration from legacy CMS' ) ),
			array( '▧', 'HMS / Healthcare', 'Hospital management systems built around clinical reality: OPD/IPD flows, pharmacy, labs, billing and FHIR-compliant records.', array( 'OPD / IPD management', 'HL7 FHIR interoperability', 'Pharmacy & lab modules', 'Insurance & billing' ) ),
		);
		$index = 1;
		foreach ( $services as [ $icon, $title, $desc, $points ] ) :
			$flip = ( $index % 2 === 0 ) ? ' service-detail--flip' : '';
			?>
			<div class="service-detail<?php echo esc_attr( $flip ); ?>" data-reveal>
				<div class="service-detail__text">
					<span class="service-detail__index" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index ) ); ?></span>
					<h2><?php echo esc_html( $title ); ?></h2>
					<p><?php echo esc_html( $desc ); ?></p>
					<ul class="check-list">
						<?php foreach ( $points as $point ) : ?>
							<li><?php echo esc_html( $point ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="service-detail__visual glass-card" aria-hidden="true">
					<span class="service-detail__icon"><?php echo esc_html( $icon ); ?></span>
				</div>
			</div>
		<?php $index++; endforeach; ?>
	</div>
</section>

<section class="section cta-banner">
	<div class="container">
		<div class="cta-banner__box" data-reveal>
			<div class="glow-orb glow-orb--cta" aria-hidden="true"></div>
			<h2>Not sure which service fits?</h2>
			<p>Describe the problem — we'll architect the answer.</p>
			<?php echo strativ_btn( home_url( '/contact/' ), 'Talk to us' ); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
