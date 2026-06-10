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
