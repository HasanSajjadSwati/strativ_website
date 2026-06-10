<?php get_header(); ?>
<section class="section error-404">
	<div class="glow-orb glow-orb--1" aria-hidden="true"></div>
	<div class="container" style="text-align:center;">
		<h1 class="error-404__code grad-text" data-reveal>404</h1>
		<p class="page-hero__sub" data-reveal style="margin-inline:auto;">This page shipped to the wrong address.</p>
		<div data-reveal><?php echo strativ_btn( home_url( '/' ), 'Back to home' ); ?></div>
	</div>
</section>
<?php get_footer(); ?>
