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
