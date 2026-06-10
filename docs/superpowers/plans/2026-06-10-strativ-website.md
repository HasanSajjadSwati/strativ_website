# Strativ WordPress Website Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a modern animated red/black dark-theme WordPress site for Strativ (software house) with a custom Elementor-compatible theme, portfolio CPT, and full placeholder content, running locally on Laragon.

**Architecture:** WordPress on Laragon (`C:\laragon\www\strativ`, served at `http://strativ.test`). Theme + plugin source live in this repo (`D:\Projects\Strativ\wp-content\...`) and are linked into the WP install via NTFS junctions. A `strativ-core` plugin owns CPTs; the `strativ` classic theme owns all presentation; GSAP drives animations; a WP-CLI seed script creates all placeholder content.

**Tech Stack:** PHP 8 / WordPress latest, WP-CLI, MySQL, GSAP 3 + ScrollTrigger, vanilla JS, CSS custom properties (no build step), ACF free, Elementor free, Contact Form 7.

**Spec:** `docs/superpowers/specs/2026-06-10-strativ-website-design.md` — read it before starting. Visual language: near-black `#0b0b0d` base, red gradient `#c41230 → #ff1e3c`, glassmorphism cards, glow accents, Space Grotesk + Inter.

**Design latitude note:** PHP file contents in this plan are exact. For CSS in Tasks 8+ the plan gives binding design tokens, class contracts, and acceptance criteria, but the executor should use the `document-skills:frontend-design` skill for the detailed CSS authoring — pixel-level choices are delegated, tokens and behaviors are not.

**Verification style:** This is a visual WordPress project with no PHP test harness; each task ends with concrete verification commands (WP-CLI / curl / Playwright) instead of unit tests.

**Known spec deviation:** ACF free has no Gallery field, so the project "gallery" is dropped; project detail pages use featured image + challenge/solution/results sections. (Already approved direction: keep it simple.)

---

### Task 1: Install Laragon stack

**Files:** none (system setup)

- [ ] **Step 1: Install Laragon via winget**

```powershell
winget install --id Laragon.Laragon --accept-source-agreements --accept-package-agreements
```

If the id is not found, run `winget search laragon` and install the matching id. Expected: installs to `C:\laragon`.

- [ ] **Step 2: Verify install layout**

```powershell
Test-Path C:\laragon\laragon.exe; Get-ChildItem C:\laragon\bin\php; Get-ChildItem C:\laragon\bin\mysql
```

Expected: `True`, and at least one `php-8.x` and one `mysql-x` directory.

- [ ] **Step 3: Start Laragon services**

Launch Laragon and ask the user to click **Start All** (Apache + MySQL). Also ask them to enable `Menu > Preferences > General > Auto virtual hosts` if not already on (pattern `{name}.test`).

```powershell
Start-Process C:\laragon\laragon.exe
```

Tell the user: "Laragon is open — please click **Start All**, then tell me when Apache and MySQL are green."

- [ ] **Step 4: Verify Apache and MySQL respond**

```powershell
(Invoke-WebRequest http://localhost -UseBasicParsing).StatusCode
& (Get-ChildItem C:\laragon\bin\mysql\*\bin\mysqladmin.exe | Select-Object -First 1).FullName -u root status
```

Expected: `200` and an `Uptime:` status line (Laragon's MySQL root has an empty password).

---

### Task 2: Install WP-CLI

**Files:**
- Create: `C:\laragon\bin\wp-cli.phar`
- Create: `C:\laragon\bin\wp.bat`

- [ ] **Step 1: Download wp-cli.phar**

```powershell
Invoke-WebRequest https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -OutFile C:\laragon\bin\wp-cli.phar
```

- [ ] **Step 2: Create wp.bat wrapper**

Write `C:\laragon\bin\wp.bat`:

```bat
@ECHO OFF
SETLOCAL
FOR /D %%i IN (C:\laragon\bin\php\php-8*) DO SET "PHPDIR=%%i"
"%PHPDIR%\php.exe" "C:\laragon\bin\wp-cli.phar" %*
```

- [ ] **Step 3: Verify**

```powershell
C:\laragon\bin\wp.bat --info
```

Expected: WP-CLI version table with PHP 8.x.

---

### Task 3: Install WordPress + plugins

**Files:** WordPress install at `C:\laragon\www\strativ` (NOT in git)

Use `$wp = 'C:\laragon\bin\wp.bat'` and always pass `--path=C:\laragon\www\strativ`.

- [ ] **Step 1: Download core and create config + database**

```powershell
New-Item -ItemType Directory -Force C:\laragon\www\strativ
C:\laragon\bin\wp.bat core download --path=C:\laragon\www\strativ
C:\laragon\bin\wp.bat config create --path=C:\laragon\www\strativ --dbname=strativ --dbuser=root --dbpass= --dbhost=localhost
C:\laragon\bin\wp.bat db create --path=C:\laragon\www\strativ
```

- [ ] **Step 2: Install site**

```powershell
C:\laragon\bin\wp.bat core install --path=C:\laragon\www\strativ --url=http://strativ.test --title="Strativ — Software That Ships" --admin_user=admin --admin_password=admin --admin_email=swati.hasansajjad@gmail.com --skip-email
```

- [ ] **Step 3: Create the strativ.test vhost**

In Laragon click **Menu > Apache > Reload** (or right-click > Reload All) so auto-vhost picks up the new `strativ` folder and writes the hosts entry. If `http://strativ.test` doesn't resolve afterwards (Laragon not elevated), add the hosts entry from an elevated PowerShell:

```powershell
Add-Content -Path C:\Windows\System32\drivers\etc\hosts -Value "127.0.0.1`tstrativ.test"
```

(If elevation is unavailable, fall back: `wp option update siteurl http://localhost/strativ` + `wp option update home http://localhost/strativ` and use that URL everywhere below.)

- [ ] **Step 4: Permalinks + plugins**

```powershell
C:\laragon\bin\wp.bat rewrite structure '/%postname%/' --hard --path=C:\laragon\www\strativ
C:\laragon\bin\wp.bat plugin install elementor advanced-custom-fields contact-form-7 --activate --path=C:\laragon\www\strativ
C:\laragon\bin\wp.bat plugin delete hello akismet --path=C:\laragon\www\strativ
```

- [ ] **Step 5: Verify**

```powershell
(Invoke-WebRequest http://strativ.test -UseBasicParsing).StatusCode
C:\laragon\bin\wp.bat plugin list --path=C:\laragon\www\strativ
```

Expected: `200`; elementor, advanced-custom-fields, contact-form-7 all `active`.

---

### Task 4: Repo structure + junctions

**Files:**
- Create: `D:\Projects\Strativ\wp-content\themes\strativ\` (empty dir)
- Create: `D:\Projects\Strativ\wp-content\plugins\strativ-core\` (empty dir)
- Create: `D:\Projects\Strativ\scripts\` (empty dir)
- Modify: `D:\Projects\Strativ\.gitignore`

- [ ] **Step 1: Create source dirs and junctions**

```powershell
New-Item -ItemType Directory -Force D:\Projects\Strativ\wp-content\themes\strativ, D:\Projects\Strativ\wp-content\plugins\strativ-core, D:\Projects\Strativ\scripts
New-Item -ItemType Junction -Path C:\laragon\www\strativ\wp-content\themes\strativ -Target D:\Projects\Strativ\wp-content\themes\strativ
New-Item -ItemType Junction -Path C:\laragon\www\strativ\wp-content\plugins\strativ-core -Target D:\Projects\Strativ\wp-content\plugins\strativ-core
```

- [ ] **Step 2: Update .gitignore** — replace contents with:

```gitignore
.superpowers/
node_modules/
```

- [ ] **Step 3: Verify junctions**

```powershell
Get-Item C:\laragon\www\strativ\wp-content\themes\strativ | Select-Object LinkType, Target
```

Expected: `LinkType: Junction`, target pointing at the repo dir.

- [ ] **Step 4: Commit**

```powershell
git add .gitignore; git commit -m "chore: repo structure for theme and plugin source"
```

---

### Task 5: strativ-core plugin (CPTs)

**Files:**
- Create: `wp-content/plugins/strativ-core/strativ-core.php`

- [ ] **Step 1: Write the plugin**

```php
<?php
/**
 * Plugin Name: Strativ Core
 * Description: Custom post types and taxonomies for the Strativ website. Keep active regardless of theme.
 * Version: 1.0.0
 * Author: Strativ
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function strativ_core_register() {
	register_post_type( 'project', array(
		'labels'        => array(
			'name'          => 'Projects',
			'singular_name' => 'Project',
			'add_new_item'  => 'Add New Project',
			'edit_item'     => 'Edit Project',
		),
		'public'        => true,
		'menu_icon'     => 'dashicons-portfolio',
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'portfolio' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'project_category', 'project', array(
		'labels'       => array(
			'name'          => 'Project Categories',
			'singular_name' => 'Project Category',
		),
		'public'       => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'project-category' ),
	) );

	register_post_type( 'career', array(
		'labels'        => array(
			'name'          => 'Careers',
			'singular_name' => 'Position',
			'add_new_item'  => 'Add New Position',
			'edit_item'     => 'Edit Position',
		),
		'public'        => true,
		'menu_icon'     => 'dashicons-businessperson',
		'menu_position' => 6,
		'supports'      => array( 'title', 'editor' ),
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'careers' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'strativ_core_register' );

register_activation_hook( __FILE__, function () {
	strativ_core_register();
	flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
```

- [ ] **Step 2: Activate and verify**

```powershell
C:\laragon\bin\wp.bat plugin activate strativ-core --path=C:\laragon\www\strativ
C:\laragon\bin\wp.bat post-type list --fields=name,public --path=C:\laragon\www\strativ
```

Expected: `project` and `career` listed as public.

- [ ] **Step 3: Commit**

```powershell
git add wp-content/plugins/strativ-core; git commit -m "feat: strativ-core plugin with project and career CPTs"
```

---

### Task 6: Theme base (activate a minimal working theme)

**Files:**
- Create: `wp-content/themes/strativ/style.css`
- Create: `wp-content/themes/strativ/functions.php`
- Create: `wp-content/themes/strativ/inc/helpers.php`
- Create: `wp-content/themes/strativ/inc/acf-fields.php`
- Create: `wp-content/themes/strativ/header.php`
- Create: `wp-content/themes/strativ/footer.php`
- Create: `wp-content/themes/strativ/index.php`
- Create: `wp-content/themes/strativ/template-fullwidth.php`
- Create: `wp-content/themes/strativ/assets/css/main.css` (placeholder shell, filled in Task 8)
- Create: `wp-content/themes/strativ/assets/js/main.js` (empty shell, filled in Task 9)

- [ ] **Step 1: style.css** (theme header only; all real CSS lives in assets/css/main.css)

```css
/*
Theme Name: Strativ
Theme URI: https://strativ.test
Author: Strativ
Description: Custom dark red/black animated theme for Strativ — software house & IT services. Elementor compatible.
Version: 1.0.0
Requires PHP: 8.0
Text Domain: strativ
*/
```

- [ ] **Step 2: functions.php**

```php
<?php
/**
 * Strativ theme setup.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'STRATIV_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/acf-fields.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );
	register_nav_menus( array( 'primary' => 'Primary Menu' ) );
	add_image_size( 'strativ-card', 800, 540, true );
	add_image_size( 'strativ-hero', 1600, 900, true );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	wp_enqueue_style(
		'strativ-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'strativ-main', $uri . '/assets/css/main.css', array(), filemtime( $dir . '/assets/css/main.css' ) );

	wp_enqueue_script( 'gsap', $uri . '/assets/js/vendor/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );
	wp_enqueue_script( 'strativ-main', $uri . '/assets/js/main.js', array( 'gsap', 'gsap-scrolltrigger' ), filemtime( $dir . '/assets/js/main.js' ), true );
} );

// Elementor: set a sane default content width.
add_action( 'elementor/theme/register_locations', function ( $manager ) {
	$manager->register_all_core_location();
} );

add_filter( 'excerpt_length', fn() => 22 );
add_filter( 'excerpt_more', fn() => '&hellip;' );
```

Note: GSAP vendor files don't exist until Task 9 — that's fine, the enqueue 404s harmlessly until then (filemtime guards only the files that exist now: create the two asset shells in Step 6 below).

- [ ] **Step 3: inc/helpers.php**

```php
<?php
/**
 * Template helpers.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Read an ACF field with a plain-meta fallback so templates
 * never fatal if ACF is deactivated.
 */
function strativ_field( string $name, int $post_id = 0 ) {
	$post_id = $post_id ?: get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$v = get_field( $name, $post_id );
		if ( $v !== null && $v !== false && $v !== '' ) return $v;
	}
	return get_post_meta( $post_id, $name, true );
}

/** Inline SVG logo. */
function strativ_logo(): string {
	return '<a class="site-logo" href="' . esc_url( home_url( '/' ) ) . '" aria-label="Strativ home">'
		. '<span class="site-logo__text">STRATIV</span><span class="site-logo__dot">.</span></a>';
}

/** Section heading block used across templates. */
function strativ_section_heading( string $eyebrow, string $title, string $sub = '' ): void {
	echo '<div class="section-heading" data-reveal>';
	echo '<span class="eyebrow">' . esc_html( $eyebrow ) . '</span>';
	echo '<h2 class="section-title">' . wp_kses_post( $title ) . '</h2>';
	if ( $sub ) echo '<p class="section-sub">' . esc_html( $sub ) . '</p>';
	echo '</div>';
}

/** Project card used on front page + portfolio archive. */
function strativ_project_card( int $post_id ): void {
	$cats  = get_the_terms( $post_id, 'project_category' );
	$slugs = $cats ? implode( ' ', wp_list_pluck( $cats, 'slug' ) ) : '';
	$names = $cats ? implode( ' · ', wp_list_pluck( $cats, 'name' ) ) : '';
	?>
	<article class="project-card" data-cats="<?php echo esc_attr( $slugs ); ?>" data-reveal>
		<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="project-card__link">
			<div class="project-card__media">
				<?php if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, 'strativ-card' );
				} else {
					echo '<div class="project-card__placeholder"></div>';
				} ?>
				<span class="project-card__arrow" aria-hidden="true">&rarr;</span>
			</div>
			<div class="project-card__body">
				<span class="project-card__cats"><?php echo esc_html( $names ); ?></span>
				<h3 class="project-card__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
				<p class="project-card__excerpt"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
			</div>
		</a>
	</article>
	<?php
}

/** Post (blog) card. */
function strativ_post_card( int $post_id ): void {
	?>
	<article class="post-card" data-reveal>
		<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="post-card__link">
			<div class="post-card__media">
				<?php if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, 'strativ-card' );
				} else {
					echo '<div class="project-card__placeholder"></div>';
				} ?>
			</div>
			<div class="post-card__body">
				<span class="post-card__date"><?php echo esc_html( get_the_date( '', $post_id ) ); ?></span>
				<h3 class="post-card__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
			</div>
		</a>
	</article>
	<?php
}

/** Primary CTA button. */
function strativ_btn( string $url, string $label, string $style = 'primary' ): string {
	return '<a class="btn btn--' . esc_attr( $style ) . ' btn-magnetic" href="' . esc_url( $url ) . '"><span>' . esc_html( $label ) . '</span></a>';
}
```

- [ ] **Step 4: inc/acf-fields.php**

```php
<?php
/**
 * ACF local field groups (code-registered so they're versioned).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	acf_add_local_field_group( array(
		'key'      => 'group_strativ_project',
		'title'    => 'Project Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'project' ) ) ),
		'fields'   => array(
			array( 'key' => 'field_str_client',     'name' => 'client',     'label' => 'Client',            'type' => 'text' ),
			array( 'key' => 'field_str_year',       'name' => 'year',       'label' => 'Year',              'type' => 'text' ),
			array( 'key' => 'field_str_tech_stack', 'name' => 'tech_stack', 'label' => 'Tech Stack (comma-separated)', 'type' => 'text' ),
			array( 'key' => 'field_str_live_url',   'name' => 'live_url',   'label' => 'Live URL',          'type' => 'url' ),
			array( 'key' => 'field_str_challenge',  'name' => 'challenge',  'label' => 'The Challenge',     'type' => 'textarea', 'rows' => 4 ),
			array( 'key' => 'field_str_solution',   'name' => 'solution',   'label' => 'Our Solution',      'type' => 'textarea', 'rows' => 4 ),
			array( 'key' => 'field_str_results',    'name' => 'results',    'label' => 'Results',           'type' => 'textarea', 'rows' => 4 ),
		),
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_strativ_career',
		'title'    => 'Position Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'career' ) ) ),
		'fields'   => array(
			array( 'key' => 'field_str_location',   'name' => 'location',   'label' => 'Location',        'type' => 'text' ),
			array( 'key' => 'field_str_emp_type',   'name' => 'emp_type',   'label' => 'Employment Type', 'type' => 'text' ),
			array( 'key' => 'field_str_department', 'name' => 'department', 'label' => 'Department',      'type' => 'text' ),
		),
	) );
} );
```

- [ ] **Step 5: header.php**

```php
<?php
/**
 * Site header: preloader, glass nav, mobile panel.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="preloader" id="preloader" aria-hidden="true">
	<div class="preloader__logo">STRATIV<span>.</span></div>
</div>

<header class="site-header" id="site-header">
	<div class="container site-header__inner">
		<?php echo strativ_logo(); ?>
		<nav class="site-nav" id="site-nav" aria-label="Primary">
			<?php wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'site-nav__list',
				'fallback_cb'    => false,
				'depth'          => 1,
			) ); ?>
		</nav>
		<div class="site-header__actions">
			<?php echo strativ_btn( home_url( '/contact/' ), 'Get in touch' ); ?>
			<button class="nav-toggle" id="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="site-nav">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</header>

<main id="main">
```

- [ ] **Step 6: footer.php, index.php, template-fullwidth.php, asset shells**

`footer.php`:

```php
<?php
/**
 * Site footer: 4 columns + bottom bar.
 */
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="site-footer__grid">
			<div class="site-footer__col site-footer__brand">
				<?php echo strativ_logo(); ?>
				<p>We design, build and ship intelligent software — from AI products to enterprise platforms.</p>
			</div>
			<div class="site-footer__col">
				<h4>Company</h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
					<li><a href="<?php echo esc_url( home_url( '/portfolio/' ) ); ?>">Portfolio</a></li>
					<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Insights</a></li>
					<li><a href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Careers</a></li>
				</ul>
			</div>
			<div class="site-footer__col">
				<h4>Services</h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">AI Solutions</a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Web &amp; SaaS</a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">POS Systems</a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">HMS Software</a></li>
				</ul>
			</div>
			<div class="site-footer__col">
				<h4>Contact</h4>
				<ul>
					<li><a href="mailto:hello@strativ.test">hello@strativ.test</a></li>
					<li><a href="tel:+15550100">+1 (555) 010-0100</a></li>
					<li>100 Innovation Drive,<br>Tech District</li>
				</ul>
			</div>
		</div>
		<div class="site-footer__bottom">
			<span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Strativ. All rights reserved.</span>
			<span class="site-footer__tag">Software that ships.</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
```

`index.php` (generic fallback):

```php
<?php get_header(); ?>
<section class="section page-hero">
	<div class="container">
		<h1 class="page-hero__title" data-reveal><?php echo is_search() ? 'Search results' : 'Archive'; ?></h1>
	</div>
</section>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3" data-reveal-group>
				<?php while ( have_posts() ) : the_post(); strativ_post_card( get_the_ID() ); endwhile; ?>
			</div>
		<?php else : ?>
			<p class="empty-state">Nothing here yet — check back soon.</p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
```

`template-fullwidth.php` (Elementor canvas):

```php
<?php
/**
 * Template Name: Full Width (Elementor)
 * No theme containers — Elementor owns the layout between header and footer.
 */
get_header();
while ( have_posts() ) : the_post();
	the_content();
endwhile;
get_footer();
```

Asset shells so `filemtime` doesn't fatal:

```powershell
New-Item -ItemType Directory -Force D:\Projects\Strativ\wp-content\themes\strativ\assets\css, D:\Projects\Strativ\wp-content\themes\strativ\assets\js\vendor
Set-Content D:\Projects\Strativ\wp-content\themes\strativ\assets\css\main.css "/* filled in Task 8 */" -Encoding utf8
Set-Content D:\Projects\Strativ\wp-content\themes\strativ\assets\js\main.js "/* filled in Task 9 */" -Encoding utf8
```

- [ ] **Step 7: Activate theme and verify**

```powershell
C:\laragon\bin\wp.bat theme activate strativ --path=C:\laragon\www\strativ
(Invoke-WebRequest http://strativ.test -UseBasicParsing).StatusCode
C:\laragon\bin\wp.bat eval "echo function_exists('strativ_field') ? 'helpers-ok' : 'FAIL';" --path=C:\laragon\www\strativ
```

Expected: theme activates without PHP errors, homepage returns 200, prints `helpers-ok`.

- [ ] **Step 8: Commit**

```powershell
git add wp-content/themes/strativ; git commit -m "feat: theme base — setup, helpers, ACF fields, header/footer"
```

---

### Task 7: Seed placeholder content

**Files:**
- Create: `scripts/seed-content.php`

- [ ] **Step 1: Write the seed script** (idempotent: re-running won't duplicate; it checks by slug/title)

```php
<?php
/**
 * Seed all placeholder content. Run:
 *   wp eval-file scripts/seed-content.php --path=C:\laragon\www\strativ
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
```

- [ ] **Step 2: Run it**

```powershell
C:\laragon\bin\wp.bat eval-file D:\Projects\Strativ\scripts\seed-content.php --path=C:\laragon\www\strativ
```

Expected: `Success: Seed complete: 8 projects, 3 careers, 3 posts.`

- [ ] **Step 3: Verify counts and front page**

```powershell
C:\laragon\bin\wp.bat post list --post_type=project --format=count --path=C:\laragon\www\strativ   # 8
C:\laragon\bin\wp.bat post list --post_type=career --format=count --path=C:\laragon\www\strativ    # 3
C:\laragon\bin\wp.bat post list --post_type=page --format=count --path=C:\laragon\www\strativ      # >= 6 (sample page may exist)
C:\laragon\bin\wp.bat option get page_on_front --path=C:\laragon\www\strativ                        # Home page ID
```

- [ ] **Step 4: Commit**

```powershell
git add scripts/seed-content.php; git commit -m "feat: idempotent placeholder content seed script"
```

---

### Task 8: Design system CSS (`assets/css/main.css`)

**Files:**
- Modify: `wp-content/themes/strativ/assets/css/main.css` (replace shell)

Use the **document-skills:frontend-design** skill for this task. The following are **binding contracts**:

- [ ] **Step 1: Write the design tokens and base layer** — must begin with exactly these tokens:

```css
:root {
  --bg: #0b0b0d;
  --bg-2: #121214;
  --surface: rgba(255, 255, 255, 0.04);
  --border: rgba(255, 255, 255, 0.07);
  --red: #ff1e3c;
  --red-deep: #c41230;
  --red-grad: linear-gradient(135deg, #c41230, #ff1e3c);
  --glow: rgba(255, 30, 60, 0.35);
  --text: #f2f2f5;
  --muted: #8a8a92;
  --font-head: "Space Grotesk", sans-serif;
  --font-body: "Inter", sans-serif;
  --radius: 14px;
  --container: 1200px;
  --section-pad: clamp(72px, 10vw, 130px);
}
```

- [ ] **Step 2: Implement components.** Every selector below must exist and match the behavior described (these class names are already used by PHP templates):

| Selector(s) | Requirements |
|---|---|
| base (`body`, headings, `a`, `::selection`) | dark bg, Inter body, Space Grotesk headings, red selection |
| `.container` | max-width var(--container), side padding |
| `.section`, `.section--alt` | vertical pad var(--section-pad); alt uses --bg-2 |
| `.section-heading`, `.eyebrow`, `.section-title`, `.section-sub` | eyebrow = small caps red letter-spaced label; title clamp 28–44px |
| `.btn`, `.btn--primary`, `.btn--ghost` | primary = red gradient pill, glow shadow, hover lift + glow intensify; ghost = 1px border |
| `.preloader`, `.preloader__logo` | fixed full-screen black overlay, centered logo, JS removes it |
| `.site-header` (+ `.is-scrolled` state), `.site-header__inner`, `.site-logo`, `.site-logo__dot` | fixed glass nav: transparent at top, blur + border-bottom when `.is-scrolled`; red logo dot |
| `.site-nav__list` (+ `li a` hover underline grow), `.current-menu-item` | red underline animation |
| `.nav-toggle` + `.site-nav.is-open` | hamburger hidden ≥960px; below, slide-in full overlay panel |
| `.grid`, `.grid--2`, `.grid--3`, `.grid--4` | responsive CSS grid collapsing to 1 col mobile |
| `.glass-card` | surface bg, border, radius, backdrop-blur, hover: red border glow + translateY(-4px) |
| `.project-card` (+ `__media img` zoom on hover, `__arrow`, `__cats`, `__title`, `__excerpt`, `__placeholder`) | media aspect 3/2, overflow hidden, img scale 1.06 on hover, arrow slides in |
| `.post-card` (+ `__media`, `__date`, `__title`) | like project-card, simpler |
| `.glow-orb` | absolutely positioned blurred radial red circles (used in heroes) |
| `.page-hero`, `.page-hero__title` | inner-page hero: big title, orb background, top padding clears fixed header |
| `.empty-state` | centered muted message |
| `.site-footer` and all `site-footer__*` | 4-col grid → 1 col mobile, muted links with red hover |
| `[data-reveal]` | `opacity: 0` ONLY inside `html.js` scope (`html.js [data-reveal] { opacity: 0; }`) so content is never hidden without JS; GSAP animates in |
| `@media (prefers-reduced-motion: reduce)` | kill transitions/animations, force `[data-reveal]` visible |

Breakpoints: 640px, 960px, 1200px. Mobile-first.

- [ ] **Step 3: Verify** — load the site and confirm styled header/footer:

```powershell
(Invoke-WebRequest http://strativ.test -UseBasicParsing).Content -match 'main\.css' 
```

Expected: `True`, and visual check via Playwright screenshot (or browser) shows dark styled page.

- [ ] **Step 4: Commit**

```powershell
git add wp-content/themes/strativ/assets/css; git commit -m "feat: design system CSS — tokens, nav, cards, footer"
```

---

### Task 9: GSAP + main.js animation engine

**Files:**
- Create: `wp-content/themes/strativ/assets/js/vendor/gsap.min.js`
- Create: `wp-content/themes/strativ/assets/js/vendor/ScrollTrigger.min.js`
- Modify: `wp-content/themes/strativ/assets/js/main.js` (replace shell)

- [ ] **Step 1: Download GSAP locally**

```powershell
$v = 'D:\Projects\Strativ\wp-content\themes\strativ\assets\js\vendor'
Invoke-WebRequest https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js -OutFile "$v\gsap.min.js"
Invoke-WebRequest https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js -OutFile "$v\ScrollTrigger.min.js"
```

- [ ] **Step 2: Write main.js**

```js
/* Strativ animations & interactions. */
(function () {
  "use strict";

  document.documentElement.classList.add("js");

  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var hasGsap = typeof gsap !== "undefined";
  if (hasGsap && typeof ScrollTrigger !== "undefined") gsap.registerPlugin(ScrollTrigger);

  /* ---------- Preloader ---------- */
  window.addEventListener("load", function () {
    var pre = document.getElementById("preloader");
    if (!pre) return;
    if (reduced || !hasGsap) { pre.remove(); return; }
    gsap.to(pre, { opacity: 0, duration: 0.5, delay: 0.3, onComplete: function () { pre.remove(); } });
  });

  /* ---------- Sticky header state ---------- */
  var header = document.getElementById("site-header");
  function onScroll() {
    if (header) header.classList.toggle("is-scrolled", window.scrollY > 24);
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* ---------- Mobile nav ---------- */
  var toggle = document.getElementById("nav-toggle");
  var nav = document.getElementById("site-nav");
  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      toggle.classList.toggle("is-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      document.body.classList.toggle("nav-locked", open);
    });
    nav.addEventListener("click", function (e) {
      if (e.target.closest("a")) {
        nav.classList.remove("is-open");
        toggle.classList.remove("is-open");
        document.body.classList.remove("nav-locked");
      }
    });
  }

  if (!hasGsap || reduced) {
    document.querySelectorAll("[data-reveal]").forEach(function (el) { el.style.opacity = 1; });
    initCounters(true);
    initFilter();
    return;
  }

  /* ---------- Scroll reveals ---------- */
  document.querySelectorAll("[data-reveal-group]").forEach(function (group) {
    var items = group.querySelectorAll("[data-reveal]");
    gsap.fromTo(items, { opacity: 0, y: 36 }, {
      opacity: 1, y: 0, duration: 0.8, stagger: 0.12, ease: "power3.out",
      scrollTrigger: { trigger: group, start: "top 82%" }
    });
  });
  document.querySelectorAll("[data-reveal]").forEach(function (el) {
    if (el.closest("[data-reveal-group]")) return;
    gsap.fromTo(el, { opacity: 0, y: 36 }, {
      opacity: 1, y: 0, duration: 0.9, ease: "power3.out",
      scrollTrigger: { trigger: el, start: "top 85%" }
    });
  });

  /* ---------- Hero orbs: float + mouse parallax ---------- */
  document.querySelectorAll(".glow-orb").forEach(function (orb, i) {
    gsap.to(orb, {
      y: i % 2 ? 40 : -40, x: i % 2 ? -25 : 25,
      duration: 6 + i * 1.5, repeat: -1, yoyo: true, ease: "sine.inOut"
    });
  });
  var hero = document.querySelector(".hero");
  if (hero) {
    hero.addEventListener("mousemove", function (e) {
      var rx = (e.clientX / window.innerWidth - 0.5) * 2;
      var ry = (e.clientY / window.innerHeight - 0.5) * 2;
      document.querySelectorAll(".glow-orb").forEach(function (orb, i) {
        gsap.to(orb, { xPercent: rx * (6 + i * 4), yPercent: ry * (6 + i * 4), duration: 1.2, ease: "power2.out" });
      });
    });
  }

  /* ---------- Magnetic buttons ---------- */
  document.querySelectorAll(".btn-magnetic").forEach(function (btn) {
    btn.addEventListener("mousemove", function (e) {
      var r = btn.getBoundingClientRect();
      gsap.to(btn, { x: (e.clientX - r.left - r.width / 2) * 0.25, y: (e.clientY - r.top - r.height / 2) * 0.25, duration: 0.3 });
    });
    btn.addEventListener("mouseleave", function () {
      gsap.to(btn, { x: 0, y: 0, duration: 0.4, ease: "elastic.out(1, 0.4)" });
    });
  });

  initCounters(false);
  initFilter();

  /* ---------- Stat counters ---------- */
  function initCounters(instant) {
    document.querySelectorAll("[data-counter]").forEach(function (el) {
      var target = parseInt(el.getAttribute("data-counter"), 10) || 0;
      var suffix = el.getAttribute("data-suffix") || "";
      if (instant) { el.textContent = target + suffix; return; }
      var obj = { v: 0 };
      gsap.to(obj, {
        v: target, duration: 1.8, ease: "power2.out",
        scrollTrigger: { trigger: el, start: "top 85%", once: true },
        onUpdate: function () { el.textContent = Math.round(obj.v) + suffix; }
      });
    });
  }

  /* ---------- Portfolio filter ---------- */
  function initFilter() {
    var bar = document.querySelector(".filter-bar");
    if (!bar) return;
    var cards = document.querySelectorAll(".project-card");
    bar.addEventListener("click", function (e) {
      var btn = e.target.closest(".filter-btn");
      if (!btn) return;
      bar.querySelectorAll(".filter-btn").forEach(function (b) { b.classList.remove("is-active"); });
      btn.classList.add("is-active");
      var cat = btn.getAttribute("data-filter");
      cards.forEach(function (card) {
        var show = cat === "all" || (card.getAttribute("data-cats") || "").split(" ").indexOf(cat) !== -1;
        if (hasGsap && !reduced) {
          gsap.to(card, { opacity: show ? 1 : 0, scale: show ? 1 : 0.96, duration: 0.35, onComplete: function () { card.style.display = show ? "" : "none"; gsap.set(card, { clearProps: "scale" }); if (show) gsap.to(card, { opacity: 1, duration: 0.2 }); } });
        } else {
          card.style.display = show ? "" : "none";
          card.style.opacity = 1;
        }
      });
      if (hasGsap && typeof ScrollTrigger !== "undefined") setTimeout(function () { ScrollTrigger.refresh(); }, 450);
    });
  }
})();
```

- [ ] **Step 3: Verify assets load**

```powershell
(Invoke-WebRequest http://strativ.test/wp-content/themes/strativ/assets/js/vendor/gsap.min.js -UseBasicParsing).StatusCode
(Invoke-WebRequest http://strativ.test/wp-content/themes/strativ/assets/js/main.js -UseBasicParsing).StatusCode
```

Expected: `200`, `200`. Then use Playwright (webapp-testing skill) to load `http://strativ.test` and assert zero console errors.

- [ ] **Step 4: Commit**

```powershell
git add wp-content/themes/strativ/assets/js; git commit -m "feat: GSAP animation engine — reveals, orbs, counters, filters, magnetic buttons"
```

---

### Task 10: Front page (`front-page.php`)

**Files:**
- Create: `wp-content/themes/strativ/front-page.php`
- Modify: `wp-content/themes/strativ/assets/css/main.css` (append section styles)

- [ ] **Step 1: Write front-page.php** — all nine sections, exact markup:

```php
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
		<div class="process" data-reveal-group>
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
```

- [ ] **Step 2: Append front-page CSS to main.css** (frontend-design skill; binding requirements):
  - `.hero`: min-height ~92vh, centered content, orbs positioned (top-right large, bottom-left medium, center small), badge = pill border red tint, title clamp 40–76px Space Grotesk 700, `.grad-text` = red gradient text via background-clip with subtle glow text-shadow.
  - `.marquee`: border-top/bottom 1px var(--border), uppercase muted text, infinite CSS `@keyframes` translateX loop (~30s), pause on hover.
  - `.service-card`: icon in red, padding ~36px.
  - `.stats`/`.stat__num`: 48–64px Space Grotesk red gradient numbers, centered.
  - `.process`: 4-col grid → 2 → 1; numbered steps with large ghost numerals.
  - `.quote-card`: blockquote italic-free, footer muted.
  - `.cta-banner__box`: centered, red-tinted radial bg, border, radius 24px, overflow hidden, contained orb.

- [ ] **Step 3: Verify**

```powershell
(Invoke-WebRequest http://strativ.test -UseBasicParsing).Content -match 'hero__title'
```

Expected: `True`. Playwright screenshot desktop 1440px + mobile 390px — all 9 sections render, hero orbs visible.

- [ ] **Step 4: Commit**

```powershell
git add wp-content/themes/strativ; git commit -m "feat: animated front page with nine sections"
```

---

### Task 11: Inner pages — About, Services, Contact, Careers

**Files:**
- Create: `wp-content/themes/strativ/page-about.php`
- Create: `wp-content/themes/strativ/page-services.php`
- Create: `wp-content/themes/strativ/page-contact.php`
- Create: `wp-content/themes/strativ/page-careers.php`
- Create: `wp-content/themes/strativ/page.php`
- Create: `wp-content/themes/strativ/single-career.php`
- Modify: `wp-content/themes/strativ/assets/css/main.css` (append)

Slug-based templates auto-apply (pages were created with matching slugs in Task 7).

- [ ] **Step 1: page-about.php**

```php
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
```

- [ ] **Step 2: page-services.php** — page hero (eyebrow "Services", title "Everything it takes to <span class=\"grad-text\">ship</span>") followed by six alternating two-column blocks (`.service-detail`, modifier `.service-detail--flip` on odd). Each block: large ghost index numeral, h2 title, paragraph, `ul.check-list` of 4 bullets, and a `.service-detail__visual` glass panel with the service icon. Use this data array (same six services as front page, with longer copy):

```php
$services = array(
	array( 'AI Solutions', 'We build production AI — not demos. Copilots grounded in your data, document-intelligence pipelines, and agentic workflows with human-in-the-loop controls.', array( 'LLM copilots & chat products', 'RAG & document intelligence', 'Agentic workflow automation', 'AI strategy & evaluation' ) ),
	array( 'Web & SaaS Development', 'High-performance web platforms engineered for scale: multi-tenant SaaS, customer portals and e-commerce with sub-second loads.', array( 'Multi-tenant SaaS architecture', 'Headless e-commerce', 'API design & integrations', 'Performance engineering' ) ),
	array( 'Mobile Apps', 'Cross-platform apps that feel native. One codebase, two stores, weekly releases.', array( 'Flutter & React Native', 'Offline-first sync', 'Wearable & health integrations', 'Store launch & ASO' ) ),
	array( 'POS Systems', 'Point-of-sale that never stops selling — offline-first tills, kitchen displays, inventory and multi-branch reporting.', array( 'Retail & restaurant POS', 'Hardware integrations', 'Real-time inventory', 'Multi-branch analytics' ) ),
	array( 'CMS Platforms', 'Content platforms that let marketing move fast: headless CMS builds, editorial workflows and design systems.', array( 'Headless CMS architecture', 'Editorial workflow design', 'Multi-brand theming', 'Migration from legacy CMS' ) ),
	array( 'HMS / Healthcare', 'Hospital management systems built around clinical reality: OPD/IPD flows, pharmacy, labs, billing and FHIR-compliant records.', array( 'OPD / IPD management', 'HL7 FHIR interoperability', 'Pharmacy & lab modules', 'Insurance & billing' ) ),
);
```

End with the same `.cta-banner` section as the front page (copy: "Not sure which service fits? / Describe the problem — we'll architect the answer.").

- [ ] **Step 3: page-contact.php** — page hero ("Contact", "Let's build <span class=\"grad-text\">something real</span>"), then `.grid.grid--2`: left column three `.glass-card.contact-card`s (Email hello@strativ.test, Phone +1 (555) 010-0100, Office address + "Map coming soon" placeholder panel `.map-placeholder`); right column the CF7 form rendered via:

```php
$form = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'numberposts' => 1 ) );
if ( $form ) {
	echo do_shortcode( '[contact-form-7 id="' . $form[0]->ID . '"]' );
} else {
	echo '<p class="empty-state">Contact form unavailable — email us at hello@strativ.test</p>';
}
```

CSS: style `.wpcf7 input, .wpcf7 textarea` as dark fields (surface bg, border, red focus ring) and `.wpcf7-submit` as `.btn--primary` equivalent.

- [ ] **Step 4: page-careers.php** — page hero ("Careers", "Do the best <span class=\"grad-text\">work of your career</span>"), a 3-card perks row (Remote-friendly / Learning budget / Ship real products), then open positions list:

```php
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
<?php endif;
```

- [ ] **Step 5: single-career.php** — page hero with job title + meta line, `.entry-content` with `the_content()`, and a `.cta-banner` ("Sound like you? / Send your CV and three things you've shipped to careers@strativ.test").

- [ ] **Step 6: page.php** (generic) — page hero with `the_title()`, then `.entry-content` `the_content()` in a ~760px column. Style `.entry-content` typography: muted paragraphs, white headings, red links, styled lists/blockquotes (this also serves blog posts in Task 13).

- [ ] **Step 7: Verify all four pages**

```powershell
foreach ($p in 'about','services','contact','careers') { "$p : " + (Invoke-WebRequest "http://strativ.test/$p/" -UseBasicParsing).StatusCode }
```

Expected: all `200`. Playwright screenshots of each.

- [ ] **Step 8: Commit**

```powershell
git add wp-content/themes/strativ; git commit -m "feat: about, services, contact, careers templates"
```

---

### Task 12: Portfolio templates

**Files:**
- Create: `wp-content/themes/strativ/archive-project.php`
- Create: `wp-content/themes/strativ/single-project.php`
- Modify: `wp-content/themes/strativ/assets/css/main.css` (append)

- [ ] **Step 1: archive-project.php**

```php
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
```

- [ ] **Step 2: single-project.php**

```php
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
```

- [ ] **Step 3: Append CSS** (frontend-design skill): `.filter-bar` pill buttons (ghost; `.is-active` = red gradient), `.portfolio-grid`, `.project-hero-media img` (radius, border, max-height ~560px object-fit cover), `.project-layout` (sidebar 340px + fluid body → stack on mobile), `.project-meta__row` (label/value rows with dividers), `.tech-tag` (small red-tinted chips), `.project-section h2` spacing, `.project-nav` (two columns, next right-aligned).

- [ ] **Step 4: Verify**

```powershell
(Invoke-WebRequest http://strativ.test/portfolio/ -UseBasicParsing).StatusCode
(Invoke-WebRequest http://strativ.test/portfolio/nexus-copilot/ -UseBasicParsing).StatusCode
```

Expected: `200`, `200`. Playwright: click a filter button, assert non-matching cards hidden.

- [ ] **Step 5: Commit**

```powershell
git add wp-content/themes/strativ; git commit -m "feat: portfolio archive with filters and project case-study template"
```

---

### Task 13: Blog + remaining templates

**Files:**
- Create: `wp-content/themes/strativ/home.php`
- Create: `wp-content/themes/strativ/single.php`
- Create: `wp-content/themes/strativ/404.php`
- Create: `wp-content/themes/strativ/searchform.php`
- Modify: `wp-content/themes/strativ/assets/css/main.css` (append)

- [ ] **Step 1: home.php** (blog index)

```php
<?php
/** Blog index. */
get_header(); ?>

<section class="page-hero">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container">
		<span class="eyebrow" data-reveal>Insights</span>
		<h1 class="page-hero__title" data-reveal>Notes from the <span class="grad-text">build floor</span></h1>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3" data-reveal-group>
				<?php while ( have_posts() ) : the_post(); strativ_post_card( get_the_ID() ); endwhile; ?>
			</div>
			<div class="pagination" data-reveal><?php the_posts_pagination( array( 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?></div>
		<?php else : ?>
			<p class="empty-state">No articles yet — check back soon.</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
```

- [ ] **Step 2: single.php** (blog post)

```php
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
```

- [ ] **Step 3: 404.php**

```php
<?php get_header(); ?>
<section class="section error-404">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container" style="text-align:center;">
		<h1 class="error-404__code grad-text" data-reveal>404</h1>
		<p class="page-hero__sub" data-reveal>This page shipped to the wrong address.</p>
		<div data-reveal><?php echo strativ_btn( home_url( '/' ), 'Back to home' ); ?></div>
	</div>
</section>
<?php get_footer(); ?>
```

- [ ] **Step 4: searchform.php**

```php
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" class="search-form__input" placeholder="Search&hellip;" value="<?php echo get_search_query(); ?>" name="s">
	<button type="submit" class="btn btn--primary"><span>Search</span></button>
</form>
```

- [ ] **Step 5: Append CSS**: `.container--narrow` (max-width 760px), `.pagination` (centered, red current page), `.error-404__code` (font-size clamp 100–180px), `.search-form` row layout.

- [ ] **Step 6: Verify**

```powershell
foreach ($u in 'blog','blog/why-2026-is-the-year-of-agentic-software','nonexistent-xyz') { "$u : " + (Invoke-WebRequest "http://strativ.test/$u/" -UseBasicParsing -SkipHttpErrorCheck).StatusCode }
```

Note: Windows PowerShell 5.1 lacks `-SkipHttpErrorCheck` — wrap the 404 check in try/catch and read `$_.Exception.Response.StatusCode`. Expected: 200, 200, 404 (styled).

- [ ] **Step 7: Commit**

```powershell
git add wp-content/themes/strativ; git commit -m "feat: blog templates, 404 and search form"
```

---

### Task 14: Full-site verification & polish

**Files:** possibly small fixes anywhere in theme

- [ ] **Step 1: Playwright sweep** — use the **document-skills:webapp-testing** skill. For each URL — `/`, `/about/`, `/services/`, `/portfolio/`, `/portfolio/nexus-copilot/`, `/blog/`, first blog post, `/careers/`, first career post, `/contact/`, `/nonexistent/` — capture:
  - Desktop 1440×900 full-page screenshot
  - Mobile 390×844 full-page screenshot (verify hamburger menu opens)
  - Console errors (must be zero)

- [ ] **Step 2: Interaction checks** (Playwright): portfolio filter hides/shows cards; stats counters animate to final values; mobile nav opens/closes; contact form renders fields.

- [ ] **Step 3: Elementor compatibility check**

```powershell
C:\laragon\bin\wp.bat post create --post_type=page --post_title='Elementor Test' --post_status=publish --page_template=template-fullwidth.php --path=C:\laragon\www\strativ
```

Open `http://strativ.test/wp-admin/` (admin/admin), edit the page with Elementor, confirm the editor loads and the page renders header/footer with Elementor content area. Then delete the test page.

- [ ] **Step 4: Fix anything found** — visual bugs, console errors, overflow issues. Re-screenshot after fixes.

- [ ] **Step 5: Final commit**

```powershell
git add -A; git commit -m "fix: post-verification polish"
```

- [ ] **Step 6: Report to user** — site URL, admin URL + credentials (admin/admin), where theme source lives, how to edit content, how Elementor fits in.

---

## Execution notes

- WP-CLI on this machine: always `C:\laragon\bin\wp.bat ... --path=C:\laragon\www\strativ`.
- The repo (`D:\Projects\Strativ`) holds theme/plugin/scripts/docs only; WordPress core is not committed.
- Junctions mean edits in the repo are live immediately — no copy step.
- If `strativ.test` doesn't resolve at any point, check the hosts file entry first.
- Commit after every task; never batch multiple tasks into one commit.
