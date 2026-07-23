<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'how-obot-works-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-how-obot-works';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$eyebrow   = trim( (string) get_field( 'eyebrow' ) );
$title      = trim( (string) get_field( 'title' ) );
$text       = trim( (string) get_field( 'text' ) );
$rows       = get_field( 'steps' );
$steps      = array();
$fx_classes = array(
	'zoom-in',
	'pan-right',
	'zoom-out',
	'pan-up',
	'pop',
	'pan-diagonal',
	'corner-zoom',
	'flip-in',
);

if ( is_array( $rows ) ) {
	foreach ( $rows as $row ) {
		$step_name  = trim( (string) ( $row['name'] ?? '' ) );
		$step_title = trim( (string) ( $row['title'] ?? '' ) );
		$step_text  = trim( (string) ( $row['text'] ?? '' ) );
		$bottom     = trim( (string) ( $row['bottom_text'] ?? '' ) );
		$browser    = trim( (string) ( $row['browser_url'] ?? '' ) );
		$image      = $row['image'] ?? null;
		$image_id   = 0;
		$image_url  = '';
		$image_alt  = '';

		if ( is_array( $image ) ) {
			$image_id  = absint( $image['ID'] ?? $image['id'] ?? 0 );
			$image_url = (string) ( $image['url'] ?? '' );
			$image_alt = (string) ( $image['alt'] ?? '' );
		} elseif ( is_numeric( $image ) ) {
			$image_id  = absint( $image );
			$image_url = (string) wp_get_attachment_image_url( $image_id, 'full' );
			$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $image ) ) {
			$image_url = $image;
		}

		if ( '' === $image_alt && $step_title ) {
			$image_alt = $step_title;
		}

		if ( ! $step_name && ! $step_title && ! $step_text && ! $bottom && ! $browser && ! $image_url ) {
			continue;
		}

		$steps[] = array(
			'name'        => $step_name,
			'title'       => $step_title,
			'text'        => $step_text,
			'bottom_text' => $bottom,
			'browser_url' => $browser,
			'image_id'    => $image_id,
			'image_url'   => $image_url,
			'image_alt'   => $image_alt,
		);
	}
}

$step_count = count( $steps );
if ( 1 === $step_count ) {
	$wrapper_classes .= ' obot-how-obot-works--single';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'                  => $wrapper_classes,
		'data-how-obot-works'    => 'true',
		'data-how-obot-interval' => '8000',
	)
);

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-how-obot-works__inner">
		<?php if ( $eyebrow || $title || $text ) : ?>
			<header class="obot-how-obot-works__header">
				<?php if ( $eyebrow ) : ?>
					<div class="obot-how-obot-works__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
						<span class="obot-how-obot-works__eyebrow-dot" aria-hidden="true"></span>
						<span><?php echo esc_html( $eyebrow ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="obot-how-obot-works__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $text ) : ?>
					<p class="obot-how-obot-works__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo nl2br( esc_html( $text ) ); ?></p>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $steps ) : ?>
			<div class="obot-how-obot-works__tabs" role="tablist" aria-label="<?php esc_attr_e( 'How Obot works steps', 'oboto' ); ?>">
				<?php foreach ( $steps as $index => $step ) : ?>
					<?php
					$step_number = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
					$tab_id      = $id . '-tab-' . $step_number;
					$panel_id    = $id . '-panel-' . $step_number;
					?>
					<button
						id="<?php echo esc_attr( $tab_id ); ?>"
						class="obot-how-obot-works__tab<?php echo 0 === $index ? ' is-active' : ''; ?>"
						type="button"
						role="tab"
						aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						tabindex="<?php echo 0 === $index ? '0' : '-1'; ?>"
						data-how-obot-tab
					>
						<span class="obot-how-obot-works__tab-number"><?php echo esc_html( $step_number ); ?></span>
						<span><?php echo esc_html( $step['name'] ?: sprintf( __( 'Step %s', 'oboto' ), $step_number ) ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="obot-how-obot-works__progress" aria-hidden="true">
				<span class="obot-how-obot-works__progress-bar" data-how-obot-progress></span>
			</div>

			<div class="obot-how-obot-works__carousel" data-how-obot-carousel>
				<div class="obot-how-obot-works__viewport">
					<?php foreach ( $steps as $index => $step ) : ?>
						<?php
						$step_number = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
						$total_number = str_pad( (string) $step_count, 2, '0', STR_PAD_LEFT );
						$tab_id      = $id . '-tab-' . $step_number;
						$panel_id    = $id . '-panel-' . $step_number;
						$effect      = $fx_classes[ $index % count( $fx_classes ) ];
						?>
						<article
							id="<?php echo esc_attr( $panel_id ); ?>"
							class="obot-how-obot-works__slide<?php echo 0 === $index ? ' is-active' : ''; ?>"
							role="tabpanel"
							aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
							data-how-obot-slide
							data-how-obot-effect="<?php echo esc_attr( $effect ); ?>"
							<?php echo 0 === $index ? '' : 'hidden'; ?>
						>
							<div class="obot-how-obot-works__browser">
								<div class="obot-how-obot-works__browser-bar">
									<span class="obot-how-obot-works__browser-dots" aria-hidden="true">
										<span></span><span></span><span></span>
									</span>
									<?php if ( $step['browser_url'] ) : ?>
										<span class="obot-how-obot-works__browser-url"><?php echo esc_html( $step['browser_url'] ); ?></span>
									<?php endif; ?>
								</div>

								<div class="obot-how-obot-works__image-wrap<?php echo $step['image_url'] ? '' : ' obot-how-obot-works__image-wrap--empty'; ?>">
									<?php if ( $step['image_id'] ) : ?>
										<?php
										echo wp_get_attachment_image(
											$step['image_id'],
											'full',
											false,
											array(
												'class'   => 'obot-how-obot-works__image',
												'alt'     => $step['image_alt'],
												'loading' => 0 === $index ? 'eager' : 'lazy',
											)
										); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
									<?php elseif ( $step['image_url'] ) : ?>
										<img class="obot-how-obot-works__image" src="<?php echo esc_url( $step['image_url'] ); ?>" alt="<?php echo esc_attr( $step['image_alt'] ); ?>" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>">
									<?php elseif ( $is_preview ) : ?>
										<span class="obot-how-obot-works__image-placeholder"><?php esc_html_e( 'Select a step image', 'oboto' ); ?></span>
									<?php endif; ?>
								</div>
							</div>

							<div class="obot-how-obot-works__copy">
								<div class="obot-how-obot-works__step-number">
									<?php
									echo esc_html(
										sprintf(
											/* translators: 1: current step number, 2: total step count. */
											__( 'STEP %1$s / %2$s', 'oboto' ),
											$step_number,
											$total_number
										)
									);
									?>
								</div>

								<?php if ( $step['name'] ) : ?>
									<div class="obot-how-obot-works__badge"><?php echo esc_html( $step['name'] ); ?></div>
								<?php endif; ?>

								<?php if ( $step['title'] ) : ?>
									<h3 class="obot-how-obot-works__step-title"><?php echo esc_html( $step['title'] ); ?></h3>
								<?php endif; ?>

								<?php if ( $step['text'] ) : ?>
									<p class="obot-how-obot-works__step-text"><?php echo nl2br( esc_html( $step['text'] ) ); ?></p>
								<?php endif; ?>

								<?php if ( $step['bottom_text'] ) : ?>
									<div class="obot-how-obot-works__bottom-line">
										<span><?php echo esc_html( $step['bottom_text'] ); ?></span>
										<span class="obot-how-obot-works__bottom-rule" aria-hidden="true"></span>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<?php if ( $step_count > 1 ) : ?>
					<button class="obot-how-obot-works__arrow obot-how-obot-works__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous step', 'oboto' ); ?>" data-how-obot-prev></button>
					<button class="obot-how-obot-works__arrow obot-how-obot-works__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next step', 'oboto' ); ?>" data-how-obot-next></button>
				<?php endif; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-how-obot-works__placeholder">
				<?php esc_html_e( 'Add steps in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
