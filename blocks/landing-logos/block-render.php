<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-logos-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-logos';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$title = trim( (string) get_field( 'title' ) );
$logos = get_field( 'logos' );

$normalize_logo = static function ( $logo ) {
	if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
		return array(
			'url' => $logo['url'],
			'alt' => ! empty( $logo['alt'] ) ? $logo['alt'] : '',
		);
	}

	if ( is_numeric( $logo ) ) {
		$image_src = wp_get_attachment_image_url( (int) $logo, 'medium' );
		if ( ! $image_src ) {
			return null;
		}

		return array(
			'url' => $image_src,
			'alt' => get_post_meta( (int) $logo, '_wp_attachment_image_alt', true ),
		);
	}

	if ( is_string( $logo ) && $logo !== '' ) {
		return array(
			'url' => $logo,
			'alt' => '',
		);
	}

	return null;
};

$logo_items = array();
if ( is_array( $logos ) ) {
	foreach ( $logos as $row ) {
		$logo = is_array( $row ) && array_key_exists( 'logo', $row ) ? $row['logo'] : null;
		$logo = $normalize_logo( $logo );
		$text = is_array( $row ) && array_key_exists( 'text', $row ) ? trim( (string) $row['text'] ) : '';

		if ( $logo || $text ) {
			$logo_items[] = array(
				'logo' => $logo,
				'text' => $text,
			);
		}
	}
}

?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-logos__inner">
		<?php if ( $title ) : ?>
			<div class="obot-landing-logos__header">
				<h2 class="obot-landing-logos__title"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-logos__status" aria-hidden="true">
						<span class="obot-landing-logos__status-pulse"></span>
						<span class="obot-landing-logos__status-dot"></span>
					</span>
					<span><?php echo esc_html( $title ); ?></span>
				</h2>
			</div>
		<?php endif; ?>

		<?php if ( $logo_items ) : ?>
			<ul class="obot-landing-logos__list"<?php echo $title ? ' aria-label="' . esc_attr( $title ) . '"' : ''; ?>>
				<?php foreach ( $logo_items as $index => $logo_item ) : ?>
					<li class="obot-landing-logos__item"<?php oboto_the_aos_attributes( 180 + ( $index * 60 ) ); ?>>
						<?php if ( ! empty( $logo_item['logo'] ) ) : ?>
							<img
								class="obot-landing-logos__image"
								src="<?php echo esc_url( $logo_item['logo']['url'] ); ?>"
								alt="<?php echo esc_attr( $logo_item['text'] ? '' : $logo_item['logo']['alt'] ); ?>"
								loading="lazy"
							>
						<?php endif; ?>

						<?php if ( $logo_item['text'] ) : ?>
							<span class="obot-landing-logos__text"><?php echo esc_html( $logo_item['text'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-logos__placeholder">
				<?php esc_html_e( 'Add logos in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
