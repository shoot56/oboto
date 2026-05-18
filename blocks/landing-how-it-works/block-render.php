<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-how-it-works-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-how';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow          = trim( (string) get_field( 'eyebrow' ) );
$title            = trim( (string) get_field( 'title' ) );
$steps            = get_field( 'steps' );
$image            = get_field( 'image' );
$primary_button   = get_field( 'primary_button' );
$secondary_button = get_field( 'secondary_button' );

$image_data = null;
if ( is_array( $image ) && ! empty( $image['url'] ) ) {
	$image_data = array(
		'url' => $image['url'],
		'alt' => ! empty( $image['alt'] ) ? $image['alt'] : '',
	);
} elseif ( is_numeric( $image ) ) {
	$image_src = wp_get_attachment_image_url( (int) $image, 'large' );
	if ( $image_src ) {
		$image_data = array(
			'url' => $image_src,
			'alt' => get_post_meta( (int) $image, '_wp_attachment_image_alt', true ),
		);
	}
} elseif ( is_string( $image ) && $image !== '' ) {
	$image_data = array(
		'url' => $image,
		'alt' => '',
	);
}

$step_items = array();
if ( is_array( $steps ) ) {
	foreach ( $steps as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$step_title = ! empty( $row['title'] ) ? trim( (string) $row['title'] ) : '';
		$step_text  = ! empty( $row['text'] ) ? trim( (string) $row['text'] ) : '';

		if ( $step_title === '' && $step_text === '' ) {
			continue;
		}

		$step_items[] = array(
			'title' => $step_title,
			'text'  => $step_text,
		);
	}
}

$buttons = array();
if ( is_array( $primary_button ) && ! empty( $primary_button['url'] ) ) {
	$buttons[] = array(
		'data'        => $primary_button,
		'class'       => 'obot-landing-how__button obot-landing-how__button--primary',
		'arrow_class' => 'obot-landing-how__button-arrow obot-landing-how__button-arrow--right',
	);
}

if ( is_array( $secondary_button ) && ! empty( $secondary_button['url'] ) ) {
	$buttons[] = array(
		'data'        => $secondary_button,
		'class'       => 'obot-landing-how__button obot-landing-how__button--secondary',
		'arrow_class' => 'obot-landing-how__button-arrow obot-landing-how__button-arrow--up-right',
	);
}

?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-how__inner">
		<div class="obot-landing-how__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-how__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-how__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-how__eyebrow-pulse"></span>
						<span class="obot-landing-how__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-landing-how__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</div>

		<div class="obot-landing-how__body">
			<?php if ( $step_items ) : ?>
				<ol class="obot-landing-how__steps">
					<?php foreach ( $step_items as $index => $step ) : ?>
						<li class="obot-landing-how__step"<?php oboto_the_aos_attributes( 260 + ( $index * 90 ) ); ?>>
							<div class="obot-landing-how__step-marker">
								<span class="obot-landing-how__step-number"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span>
								<?php if ( $index < count( $step_items ) - 1 ) : ?>
									<span class="obot-landing-how__step-line" aria-hidden="true"></span>
								<?php endif; ?>
							</div>
							<div class="obot-landing-how__step-content">
								<?php if ( $step['title'] ) : ?>
									<h3 class="obot-landing-how__step-title"><?php echo esc_html( $step['title'] ); ?></h3>
								<?php endif; ?>

								<?php if ( $step['text'] ) : ?>
									<p class="obot-landing-how__step-text"><?php echo esc_html( $step['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php elseif ( $is_preview ) : ?>
				<div class="obot-landing-how__steps-placeholder">
					<?php esc_html_e( 'Add list items in the block fields.', 'oboto' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $image_data || $is_preview ) : ?>
				<div class="obot-landing-how__media">
					<div class="obot-landing-how__media-glow" aria-hidden="true"></div>
					<div class="obot-landing-how__browser"<?php oboto_the_aos_attributes( 340 ); ?>>
						<div class="obot-landing-how__browser-bar" aria-hidden="true">
							<span class="obot-landing-how__window-dot obot-landing-how__window-dot--red"></span>
							<span class="obot-landing-how__window-dot obot-landing-how__window-dot--yellow"></span>
							<span class="obot-landing-how__window-dot obot-landing-how__window-dot--green"></span>
						</div>

						<?php if ( $image_data ) : ?>
							<img
								class="obot-landing-how__image"
								src="<?php echo esc_url( $image_data['url'] ); ?>"
								alt="<?php echo esc_attr( $image_data['alt'] ); ?>"
								loading="lazy"
							>
						<?php else : ?>
							<div class="obot-landing-how__image-placeholder">
								<?php esc_html_e( 'Add an image in the block fields.', 'oboto' ); ?>
							</div>
						<?php endif; ?>

						<div class="obot-landing-how__browser-ring" aria-hidden="true"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $buttons ) : ?>
			<div class="obot-landing-how__actions">
				<?php foreach ( $buttons as $index => $button ) : ?>
					<?php
					$link        = $button['data'];
					$link_target = ! empty( $link['target'] ) ? $link['target'] : '';
					$link_title  = ! empty( $link['title'] ) ? $link['title'] : __( 'Learn more', 'oboto' );
					?>
					<a
						class="<?php echo esc_attr( $button['class'] ); ?>"
						href="<?php echo esc_url( $link['url'] ); ?>"
						<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
						<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						<?php oboto_the_aos_attributes( 520 + ( $index * 70 ) ); ?>
					>
						<span><?php echo esc_html( $link_title ); ?></span>
						<span class="<?php echo esc_attr( $button['arrow_class'] ); ?>" aria-hidden="true"></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-how__actions-placeholder">
				<?php esc_html_e( 'Add one or two buttons in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
