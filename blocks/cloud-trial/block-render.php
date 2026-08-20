<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'cloud-trial-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'cloud-trial';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

/**
 * Hero.
 */
$hero_badge = trim( (string) get_field( 'hero_badge' ) );
$hero_title = cloud_trial_inline_html( get_field( 'hero_title' ) );
$hero_text  = trim( (string) get_field( 'hero_text' ) );
$hero_note  = trim( (string) get_field( 'hero_note' ) );

$hero_points = array();
$hero_rows   = get_field( 'hero_points' );
if ( is_array( $hero_rows ) ) {
	foreach ( $hero_rows as $row ) {
		$text = is_array( $row ) ? trim( (string) ( $row['text'] ?? '' ) ) : '';
		if ( '' !== $text ) {
			$hero_points[] = $text;
		}
	}
}

/**
 * Form card.
 */
$form_title      = trim( (string) get_field( 'form_title' ) );
$form_text       = trim( (string) get_field( 'form_text' ) );
$form_url        = trim( (string) get_field( 'form_url' ) );
$form_height     = (int) get_field( 'form_height' );
$form_disclaimer = trim( (string) get_field( 'form_disclaimer' ) );

if ( $form_height < 200 ) {
	$form_height = 500;
}

$form_frame_title = '' !== $form_title ? $form_title : __( 'Form', 'oboto' );
$has_form         = ( '' !== $form_title || '' !== $form_text || '' !== $form_url || '' !== $form_disclaimer );

/**
 * Features section.
 */
$features_eyebrow = trim( (string) get_field( 'features_eyebrow' ) );
$features_title   = trim( (string) get_field( 'features_title' ) );

$features     = array();
$feature_rows = get_field( 'features_items' );
if ( is_array( $feature_rows ) ) {
	foreach ( $feature_rows as $row ) {
		$text = is_array( $row ) ? trim( (string) ( $row['text'] ?? '' ) ) : '';
		if ( '' !== $text ) {
			$features[] = $text;
		}
	}
}

/**
 * Callout cards.
 */
$callouts       = array();
$callout_rows   = get_field( 'callouts' );
$callout_colors = array( '#c4b5fd', '#06eaa7', '#4f7ef3' );

if ( is_array( $callout_rows ) ) {
	foreach ( $callout_rows as $index => $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$title = trim( (string) ( $row['title'] ?? '' ) );
		$text  = trim( (string) ( $row['text'] ?? '' ) );
		$icon  = cloud_trial_get_image( $row['icon'] ?? null );

		if ( '' === $title && '' === $text && ! $icon ) {
			continue;
		}

		$color = sanitize_hex_color( (string) ( $row['accent_color'] ?? '' ) );
		if ( ! $color ) {
			$color = $callout_colors[ $index % count( $callout_colors ) ];
		}

		$callouts[] = array(
			'title' => $title,
			'text'  => $text,
			'icon'  => $icon,
			'color' => $color,
			'rgb'   => cloud_trial_hex_to_rgb( $color ),
		);
	}
}

/**
 * Self-hosted CTA.
 */
$selfhost_title  = trim( (string) get_field( 'selfhost_title' ) );
$selfhost_text   = trim( (string) get_field( 'selfhost_text' ) );
$selfhost_note   = trim( (string) get_field( 'selfhost_note' ) );
$selfhost_icon   = cloud_trial_get_image( get_field( 'selfhost_button_icon' ) );
$selfhost_button = get_field( 'selfhost_button' );

if ( is_string( $selfhost_button ) && '' !== trim( $selfhost_button ) ) {
	$selfhost_button = array(
		'url'    => trim( $selfhost_button ),
		'title'  => '',
		'target' => '',
	);
}

$has_selfhost_button = is_array( $selfhost_button ) && ! empty( $selfhost_button['url'] );
$has_selfhost        = ( '' !== $selfhost_title || '' !== $selfhost_text || '' !== $selfhost_note || $has_selfhost_button );

$has_hero     = ( '' !== $hero_badge || '' !== $hero_title || '' !== $hero_text || $hero_points || '' !== $hero_note || $has_form );
$has_features = ( $features || '' !== $features_title || '' !== $features_eyebrow );
$has_any      = ( $has_hero || $has_features || $callouts || $has_selfhost );

?>
<div id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>

	<?php if ( $has_hero || $has_features ) : ?>
	<?php /* Hero and features share one `is-style-has-overlay` glow so there is no seam between them. */ ?>
	<div class="cloud-trial__glow is-style-has-overlay">
	<?php endif; ?>

	<?php if ( $has_hero ) : ?>
		<section class="cloud-trial__hero">
			<div class="cloud-trial__hero-inner">

				<div class="cloud-trial__hero-copy">
					<?php if ( '' !== $hero_badge ) : ?>
						<p class="cloud-trial__badge">
							<span class="cloud-trial__badge-dot" aria-hidden="true"></span>
							<span><?php echo esc_html( $hero_badge ); ?></span>
						</p>
					<?php endif; ?>

					<?php if ( '' !== $hero_title ) : ?>
						<h1 class="cloud-trial__title"><?php echo $hero_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
					<?php endif; ?>

					<?php if ( '' !== $hero_text ) : ?>
						<p class="cloud-trial__lead"><?php echo nl2br( esc_html( $hero_text ) ); ?></p>
					<?php endif; ?>

					<?php if ( $hero_points ) : ?>
						<ul class="cloud-trial__points">
							<?php foreach ( $hero_points as $point ) : ?>
								<li class="cloud-trial__point">
									<span class="cloud-trial__point-icon" aria-hidden="true"><?php echo cloud_trial_get_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									<span><?php echo esc_html( $point ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( '' !== $hero_note ) : ?>
						<p class="cloud-trial__note">
							<span class="cloud-trial__note-icon" aria-hidden="true"><?php echo cloud_trial_get_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span><?php echo esc_html( $hero_note ); ?></span>
						</p>
					<?php endif; ?>
				</div>

				<?php if ( $has_form ) : ?>
					<div class="cloud-trial__form-card">
						<?php if ( '' !== $form_title ) : ?>
							<h2 class="cloud-trial__form-title"><?php echo esc_html( $form_title ); ?></h2>
						<?php endif; ?>

						<?php if ( '' !== $form_text ) : ?>
							<p class="cloud-trial__form-text"><?php echo nl2br( esc_html( $form_text ) ); ?></p>
						<?php endif; ?>

						<?php if ( '' !== $form_url && ! $is_preview ) : ?>
							<iframe
								class="cloud-trial__form-frame"
								src="<?php echo esc_url( $form_url ); ?>"
								title="<?php echo esc_attr( $form_frame_title ); ?>"
								style="height:<?php echo esc_attr( $form_height ); ?>px;"
								loading="lazy"
								referrerpolicy="no-referrer-when-downgrade"
							></iframe>
						<?php elseif ( '' !== $form_url ) : ?>
							<div class="cloud-trial__form-frame cloud-trial__form-frame--preview" style="height:<?php echo esc_attr( $form_height ); ?>px;">
								<span><?php esc_html_e( 'The form loads on the front end:', 'oboto' ); ?></span>
								<code><?php echo esc_html( $form_url ); ?></code>
							</div>
						<?php elseif ( $is_preview ) : ?>
							<div class="cloud-trial__form-frame cloud-trial__form-frame--preview" style="height:<?php echo esc_attr( $form_height ); ?>px;">
								<span><?php esc_html_e( 'Add the form URL in the block fields to show the form here.', 'oboto' ); ?></span>
							</div>
						<?php endif; ?>

						<?php if ( '' !== $form_disclaimer ) : ?>
							<div class="cloud-trial__form-disclaimer"><?php echo wp_kses_post( $form_disclaimer ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</section>
	<?php endif; ?>

	<?php if ( $has_features ) : ?>
		<section class="cloud-trial__features">
			<div class="cloud-trial__inner">
				<?php if ( '' !== $features_eyebrow || '' !== $features_title ) : ?>
					<header class="cloud-trial__features-header">
						<?php if ( '' !== $features_eyebrow ) : ?>
							<p class="cloud-trial__features-eyebrow"><?php echo esc_html( $features_eyebrow ); ?></p>
						<?php endif; ?>

						<?php if ( '' !== $features_title ) : ?>
							<h2 class="cloud-trial__features-title"><?php echo esc_html( $features_title ); ?></h2>
						<?php endif; ?>
					</header>
				<?php endif; ?>

				<?php if ( $features ) : ?>
					<ul class="cloud-trial__features-grid">
						<?php foreach ( $features as $feature ) : ?>
							<li class="cloud-trial__feature">
								<span class="cloud-trial__point-icon" aria-hidden="true"><?php echo cloud_trial_get_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span><?php echo esc_html( $feature ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $has_hero || $has_features ) : ?>
	</div>
	<?php endif; ?>

	<?php if ( $callouts ) : ?>
		<section class="cloud-trial__callouts">
			<div class="cloud-trial__inner">
				<div class="cloud-trial__callouts-grid">
					<?php foreach ( $callouts as $callout ) : ?>
						<?php
						$callout_style = sprintf(
							'--cloud-trial-callout: %s; --cloud-trial-callout-rgb: %s;',
							$callout['color'],
							$callout['rgb']
						);
						?>
						<article class="cloud-trial__callout" style="<?php echo esc_attr( $callout_style ); ?>">
							<div class="cloud-trial__callout-head">
								<?php if ( $callout['icon'] ) : ?>
									<span class="cloud-trial__callout-icon" aria-hidden="true"><?php echo cloud_trial_render_image( $callout['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php endif; ?>

								<?php if ( '' !== $callout['title'] ) : ?>
									<h3 class="cloud-trial__callout-title"><?php echo esc_html( $callout['title'] ); ?></h3>
								<?php endif; ?>
							</div>

							<?php if ( '' !== $callout['text'] ) : ?>
								<p class="cloud-trial__callout-text"><?php echo esc_html( $callout['text'] ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $has_selfhost ) : ?>
		<section class="cloud-trial__selfhost">
			<div class="cloud-trial__selfhost-inner">
				<?php if ( '' !== $selfhost_title ) : ?>
					<h2 class="cloud-trial__selfhost-title"><?php echo esc_html( $selfhost_title ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $selfhost_text ) : ?>
					<p class="cloud-trial__selfhost-text"><?php echo nl2br( esc_html( $selfhost_text ) ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $selfhost_note ) : ?>
					<p class="cloud-trial__selfhost-note"><?php echo esc_html( $selfhost_note ); ?></p>
				<?php endif; ?>

				<?php if ( $has_selfhost_button ) : ?>
					<?php
					$button_title  = ! empty( $selfhost_button['title'] ) ? $selfhost_button['title'] : __( 'Learn more', 'oboto' );
					$button_target = ! empty( $selfhost_button['target'] ) ? $selfhost_button['target'] : '';
					?>
					<a
						class="cloud-trial__selfhost-button"
						href="<?php echo esc_url( $selfhost_button['url'] ); ?>"
						<?php echo $button_target ? 'target="' . esc_attr( $button_target ) . '"' : ''; ?>
						<?php echo '_blank' === $button_target ? 'rel="noopener noreferrer"' : ''; ?>
					>
						<?php if ( $selfhost_icon ) : ?>
							<span class="cloud-trial__selfhost-button-icon" aria-hidden="true"><?php echo cloud_trial_render_image( $selfhost_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php endif; ?>
						<span><?php echo esc_html( $button_title ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! $has_any && $is_preview ) : ?>
		<div class="cloud-trial__placeholder">
			<?php esc_html_e( 'Obot Cloud Trial — fill in the block fields in the sidebar to build the page.', 'oboto' ); ?>
		</div>
	<?php endif; ?>

</div>
