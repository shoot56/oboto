<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-solution-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-solution';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow = trim( (string) get_field( 'eyebrow' ) );
$title   = trim( (string) get_field( 'title' ) );
$text    = trim( (string) get_field( 'text' ) );
$image   = get_field( 'image' );
$badges  = get_field( 'badges' );

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

$badge_items = array();
if ( is_array( $badges ) ) {
	foreach ( $badges as $row ) {
		if ( ! is_array( $row ) || empty( $row['badge'] ) ) {
			continue;
		}

		$badge = trim( (string) $row['badge'] );
		if ( $badge !== '' ) {
			$badge_items[] = $badge;
		}
	}
}

?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-solution__inner">
		<div class="obot-landing-solution__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-solution__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-solution__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-solution__eyebrow-pulse"></span>
						<span class="obot-landing-solution__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-landing-solution__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</div>

		<div class="obot-landing-solution__body">
			<?php if ( $text ) : ?>
				<div class="obot-landing-solution__copy"<?php oboto_the_aos_attributes( 260 ); ?>>
					<p><?php echo esc_html( $text ); ?></p>
				</div>
			<?php endif; ?>

			<div class="obot-landing-solution__media"<?php oboto_the_aos_attributes( 340 ); ?>>
				<div class="obot-landing-solution__media-glow" aria-hidden="true"></div>
				<div class="obot-landing-solution__browser">
					<div class="obot-landing-solution__browser-bar" aria-hidden="true">
						<span class="obot-landing-solution__window-dot obot-landing-solution__window-dot--red"></span>
						<span class="obot-landing-solution__window-dot obot-landing-solution__window-dot--yellow"></span>
						<span class="obot-landing-solution__window-dot obot-landing-solution__window-dot--green"></span>
					</div>

					<?php if ( $image_data ) : ?>
						<img
							class="obot-landing-solution__image"
							src="<?php echo esc_url( $image_data['url'] ); ?>"
							alt="<?php echo esc_attr( $image_data['alt'] ); ?>"
							loading="lazy"
						>
					<?php elseif ( $is_preview ) : ?>
						<div class="obot-landing-solution__image-placeholder">
							<?php esc_html_e( 'Add an image in the block fields.', 'oboto' ); ?>
						</div>
					<?php endif; ?>

					<div class="obot-landing-solution__browser-ring" aria-hidden="true"></div>
				</div>
			</div>
		</div>

		<?php if ( $badge_items ) : ?>
			<ul class="obot-landing-solution__badges" aria-label="<?php esc_attr_e( 'Solution capabilities', 'oboto' ); ?>">
				<?php foreach ( $badge_items as $index => $badge ) : ?>
					<li class="obot-landing-solution__badge"<?php oboto_the_aos_attributes( 400 + ( $index * 50 ) ); ?>><?php echo esc_html( $badge ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-solution__badges-placeholder">
				<?php esc_html_e( 'Add badges in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
