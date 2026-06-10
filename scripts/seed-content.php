<?php
/**
 * Seed all placeholder content. Run:
 *   wp eval-file scripts/seed-content.php --path=D:\Projects\Strativ\wp
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Generate a dark red gradient PNG and attach it to a post as featured image. */
function strativ_seed_image( int $post_id, string $label, int $seed ): void {
	if ( has_post_thumbnail( $post_id ) ) return;
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	$w = 1200; $h = 800;
	$im = imagecreatetruecolor( $w, $h );
	mt_srand( $seed );
	$angle_mix = mt_rand( 0, 100 ) / 100;
	for ( $y = 0; $y < $h; $y++ ) {
		$t  = ( $y / $h ) * ( 1 - $angle_mix ) + $angle_mix * 0.5;
		$r  = (int) ( 11 + ( 120 - 11 ) * $t * 0.9 );
		$g  = (int) ( 11 + ( 18 - 11 ) * $t );
		$b  = (int) ( 13 + ( 46 - 13 ) * $t );
		imagefilledrectangle( $im, 0, $y, $w, $y + 1, imagecolorallocate( $im, $r, $g, $b ) );
	}
	// Glow blob
	$cx = mt_rand( 200, 1000 ); $cy = mt_rand( 150, 650 );
	for ( $rad = 260; $rad > 0; $rad -= 4 ) {
		$alpha = (int) ( 110 + ( $rad / 260 ) * 17 );
		$col   = imagecolorallocatealpha( $im, 255, 30, 60, $alpha );
		imagefilledellipse( $im, $cx, $cy, $rad * 2, $rad * 2, $col );
	}
	// Label
	$white = imagecolorallocate( $im, 240, 240, 245 );
	imagestring( $im, 5, 40, $h - 60, strtoupper( $label ), $white );
	$tmp = wp_tempnam( sanitize_title( $label ) . '.png' );
	imagepng( $im, $tmp );
	imagedestroy( $im );
	$att = media_handle_sideload(
		array( 'name' => sanitize_title( $label ) . '.png', 'tmp_name' => $tmp ),
		$post_id
	);
	if ( ! is_wp_error( $att ) ) set_post_thumbnail( $post_id, $att );
}

function strativ_seed_meta( int $post_id, array $meta ): void {
	foreach ( $meta as $k => $v ) {
		if ( function_exists( 'update_field' ) ) update_field( $k, $v, $post_id );
		else update_post_meta( $post_id, $k, $v );
	}
}

function strativ_get_or_create( string $type, string $title, array $args = array() ): int {
	$existing = get_page_by_path( sanitize_title( $title ), OBJECT, $type );
	if ( $existing ) return $existing->ID;
	return wp_insert_post( array_merge( array(
		'post_type'   => $type,
		'post_title'  => $title,
		'post_status' => 'publish',
	), $args ) );
}

// ---------- Pages ----------
$pages = array(
	'Home'     => '',
	'About'    => '',
	'Services' => '',
	'Blog'     => '',
	'Careers'  => '',
	'Contact'  => '',
);
$page_ids = array();
foreach ( $pages as $title => $content ) {
	$page_ids[ $title ] = strativ_get_or_create( 'page', $title, array( 'post_content' => $content ) );
}
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['Home'] );
update_option( 'page_for_posts', $page_ids['Blog'] );
update_option( 'blogdescription', 'Software house & IT services — AI, POS, CMS, HMS' );

// ---------- Project categories ----------
$cats = array( 'AI', 'POS', 'CMS', 'HMS', 'Web', 'Mobile' );
foreach ( $cats as $c ) {
	if ( ! term_exists( $c, 'project_category' ) ) wp_insert_term( $c, 'project_category' );
}

// ---------- Projects ----------
$projects = array(
	array( 'Nexus Copilot', 'AI', 'An AI customer-support copilot that resolves 70% of tickets autonomously for a fintech scale-up.',
		array( 'client' => 'FinEdge', 'year' => '2025', 'tech_stack' => 'Python, Claude API, React, PostgreSQL', 'live_url' => 'https://example.com',
			'challenge' => 'FinEdge support agents were drowning in 4,000+ repetitive tickets a week, with response times stretching past 24 hours.',
			'solution'  => 'We built an LLM-powered copilot with retrieval over their knowledge base, confidence-gated autonomy, and seamless human handoff.',
			'results'   => '70% autonomous resolution, median response under 90 seconds, CSAT up 18 points within one quarter.' ) ),
	array( 'DocuMind', 'AI', 'A document-intelligence platform that extracts, classifies and summarizes contracts at enterprise scale.',
		array( 'client' => 'LexCorp Legal', 'year' => '2025', 'tech_stack' => 'Python, FastAPI, Vue, Elasticsearch', 'live_url' => 'https://example.com',
			'challenge' => 'Manual contract review consumed 1,200 paralegal hours per month and missed critical clauses.',
			'solution'  => 'A pipeline combining OCR, layout-aware parsing and LLM extraction with a human-in-the-loop review UI.',
			'results'   => 'Review time cut by 83%, clause-detection recall of 96%, ROI in under five months.' ) ),
	array( 'VendorPoint POS', 'POS', 'A cloud retail POS suite with offline-first sales, inventory and multi-branch reporting.',
		array( 'client' => 'UrbanMart', 'year' => '2024', 'tech_stack' => 'Flutter, Node.js, PostgreSQL, Redis', 'live_url' => 'https://example.com',
			'challenge' => 'UrbanMart ran 32 branches on aging desktop tills with no central visibility and frequent sync failures.',
			'solution'  => 'An offline-first POS with conflict-free sync, real-time dashboards, and integrated barcode + payment hardware.',
			'results'   => 'Checkout 2.4x faster, shrinkage down 31%, all 32 branches migrated with zero downtime.' ) ),
	array( 'DineFlow', 'POS', 'A restaurant POS with table management, kitchen display system and delivery integrations.',
		array( 'client' => 'Saffron Group', 'year' => '2024', 'tech_stack' => 'React Native, NestJS, MySQL', 'live_url' => 'https://example.com',
			'challenge' => 'Order errors between front-of-house and kitchen were costing the group an estimated 6% of revenue.',
			'solution'  => 'A unified POS + KDS with course timing, split billing, and direct integrations to three delivery platforms.',
			'results'   => 'Order errors down 92%, table turnover up 19%, rolled out across 11 restaurants.' ) ),
	array( 'Meridian CMS', 'CMS', 'A headless enterprise CMS powering 40+ brand sites from a single editorial hub.',
		array( 'client' => 'Meridian Media', 'year' => '2025', 'tech_stack' => 'Next.js, GraphQL, Node.js, AWS', 'live_url' => 'https://example.com',
			'challenge' => 'Forty brand sites on six different platforms made every campaign launch a multi-week ordeal.',
			'solution'  => 'A headless CMS with shared component libraries, granular workflows and per-brand theming.',
			'results'   => 'Campaign launch time from 3 weeks to 2 days; infrastructure costs down 40%.' ) ),
	array( 'CareOS HMS', 'HMS', 'A hospital management system covering OPD, IPD, pharmacy, labs and billing for a 400-bed hospital.',
		array( 'client' => 'St. Auburn Hospital', 'year' => '2024', 'tech_stack' => 'Laravel, Vue, MySQL, HL7 FHIR', 'live_url' => 'https://example.com',
			'challenge' => 'Paper-based workflows caused medication errors and a 40-minute average patient registration time.',
			'solution'  => 'A modular HMS with e-prescriptions, FHIR-compliant records, lab integrations and role-based dashboards.',
			'results'   => 'Registration under 5 minutes, medication errors down 78%, full digital transition in 9 months.' ) ),
	array( 'Aurelia Commerce', 'Web', 'A high-performance e-commerce platform with sub-second page loads and AI product recommendations.',
		array( 'client' => 'Aurelia', 'year' => '2025', 'tech_stack' => 'Next.js, Shopify Hydrogen, Tailwind', 'live_url' => 'https://example.com',
			'challenge' => 'A legacy storefront with 6-second loads was bleeding mobile conversions.',
			'solution'  => 'A rebuilt headless storefront with edge rendering, smart caching and an AI recommendation engine.',
			'results'   => 'LCP at 0.8s, mobile conversion up 46%, AOV up 12% from recommendations.' ) ),
	array( 'PulseFit', 'Mobile', 'A fitness companion app with AI workout planning, wearable sync and social challenges.',
		array( 'client' => 'PulseFit Inc.', 'year' => '2024', 'tech_stack' => 'Flutter, Firebase, TensorFlow Lite', 'live_url' => 'https://example.com',
			'challenge' => 'PulseFit needed to go from idea to App Store in four months to hit an investor milestone.',
			'solution'  => 'Cross-platform Flutter build with on-device AI plans, HealthKit/Google Fit sync and social features.',
			'results'   => 'Shipped in 14 weeks, 4.8★ rating, 120k downloads in the first quarter.' ) ),
);
$i = 1;
foreach ( $projects as [ $title, $cat, $excerpt, $meta ] ) {
	$id = strativ_get_or_create( 'project', $title, array(
		'post_excerpt' => $excerpt,
		'post_content' => '<p>' . $meta['challenge'] . '</p><p>' . $meta['solution'] . '</p>',
	) );
	wp_set_object_terms( $id, $cat, 'project_category' );
	strativ_seed_meta( $id, $meta );
	strativ_seed_image( $id, $title, $i * 97 );
	$i++;
}

// ---------- Careers ----------
$careers = array(
	array( 'Senior Full-Stack Engineer', array( 'location' => 'Remote / Hybrid', 'emp_type' => 'Full-time', 'department' => 'Engineering' ),
		'<p>We are looking for a senior full-stack engineer to lead delivery on client platforms — from architecture to production. You will work across modern stacks (TypeScript, PHP, cloud) and mentor a pod of three engineers.</p><h3>What you bring</h3><ul><li>6+ years building production web applications</li><li>Depth in at least one of React, Vue, or Laravel</li><li>A shipping mindset and clear written communication</li></ul>' ),
	array( 'AI/ML Engineer', array( 'location' => 'Remote', 'emp_type' => 'Full-time', 'department' => 'AI Lab' ),
		'<p>Join our AI Lab to design and ship LLM-powered products — copilots, document intelligence, and agentic workflows for real clients.</p><h3>What you bring</h3><ul><li>Hands-on experience with LLM APIs, RAG and evaluation</li><li>Strong Python and data fundamentals</li><li>Pragmatism: you ship working systems, not just notebooks</li></ul>' ),
	array( 'UI/UX Designer', array( 'location' => 'On-site', 'emp_type' => 'Full-time', 'department' => 'Design' ),
		'<p>Shape the look and feel of products used by thousands. You will own discovery-to-handoff design across web and mobile projects.</p><h3>What you bring</h3><ul><li>4+ years of product design with a strong portfolio</li><li>Fluency in Figma, design systems and motion</li><li>Comfort working directly with clients and engineers</li></ul>' ),
);
foreach ( $careers as [ $title, $meta, $content ] ) {
	$id = strativ_get_or_create( 'career', $title, array( 'post_content' => $content ) );
	strativ_seed_meta( $id, $meta );
}

// ---------- Blog posts ----------
$posts = array(
	array( 'Why 2026 Is the Year of Agentic Software', 'AI agents stopped being demos and started closing tickets, reconciling invoices and shipping code. Here is what that shift means for how businesses should plan their next build.' ),
	array( 'POS Systems Are Quietly Becoming AI Platforms', 'Modern point-of-sale is no longer a till — it is a data platform that forecasts demand, prevents shrinkage and personalizes checkout. A look at where retail tech is heading.' ),
	array( 'Choosing Between Custom Software and Off-the-Shelf in 2026', 'The build-vs-buy calculus changed when AI cut custom development costs. A practical framework for deciding which path fits your business.' ),
);
$i = 50;
foreach ( $posts as [ $title, $body ] ) {
	$id = strativ_get_or_create( 'post', $title, array(
		'post_content' => '<p>' . $body . '</p><p>' . $body . '</p><h2>The bottom line</h2><p>' . $body . '</p>',
		'post_excerpt' => $body,
	) );
	strativ_seed_image( $id, $title, $i * 131 );
	$i++;
}

// ---------- Menu ----------
$menu = wp_get_nav_menu_object( 'Primary' );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( 'Primary' );
	foreach ( array( 'Home', 'About', 'Services' ) as $t ) {
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title' => $t, 'menu-item-object' => 'page', 'menu-item-object-id' => $page_ids[ $t ],
			'menu-item-type' => 'post_type', 'menu-item-status' => 'publish',
		) );
	}
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title' => 'Portfolio', 'menu-item-url' => home_url( '/portfolio/' ),
		'menu-item-type' => 'custom', 'menu-item-status' => 'publish',
	) );
	foreach ( array( 'Blog', 'Careers' ) as $t ) {
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title' => $t, 'menu-item-object' => 'page', 'menu-item-object-id' => $page_ids[ $t ],
			'menu-item-type' => 'post_type', 'menu-item-status' => 'publish',
		) );
	}
	set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu_id ) );
}

flush_rewrite_rules();
WP_CLI::success( 'Seed complete: ' . count( $projects ) . ' projects, ' . count( $careers ) . ' careers, ' . count( $posts ) . ' posts.' );
