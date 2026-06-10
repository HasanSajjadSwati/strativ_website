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
