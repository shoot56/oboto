<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'product-feature-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-product-feature';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$accent_color = sanitize_hex_color( (string) get_field( 'accent_color' ) );
if ( ! $accent_color ) {
	$accent_color = '#5b9bff';
}

$hex_to_rgb = static function ( $hex ) {
	$hex = ltrim( (string) $hex, '#' );

	if ( strlen( $hex ) === 3 ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( strlen( $hex ) !== 6 ) {
		return '91, 155, 255';
	}

	return sprintf(
		'%d, %d, %d',
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) )
	);
};

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
		'style' => '--product-feature-accent: ' . $accent_color . '; --product-feature-accent-rgb: ' . $hex_to_rgb( $accent_color ) . ';',
	)
);

$eyebrow = trim( (string) get_field( 'eyebrow' ) );
$title   = trim( (string) get_field( 'title' ) );
$text    = trim( (string) get_field( 'text' ) );
$image   = get_field( 'image' );
$list    = get_field( 'list' );
$button  = get_field( 'button' );

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

$list_items = array();
if ( is_array( $list ) ) {
	foreach ( $list as $row ) {
		if ( ! is_array( $row ) || empty( $row['text'] ) ) {
			continue;
		}

		$item_text = trim( (string) $row['text'] );
		if ( $item_text !== '' ) {
			$list_items[] = $item_text;
		}
	}
}

$has_button = is_array( $button ) && ! empty( $button['url'] );

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-product-feature__inner">
		<div class="obot-product-feature__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-product-feature__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-product-feature__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="obot-product-feature__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>

		<div class="obot-product-feature__body">
			<div class="obot-product-feature__media"<?php oboto_the_aos_attributes( 320 ); ?>>
				<?php if ( $image_data ) : ?>
					<img
						class="obot-product-feature__image"
						src="<?php echo esc_url( $image_data['url'] ); ?>"
						alt="<?php echo esc_attr( $image_data['alt'] ); ?>"
						loading="lazy"
					>
				<?php elseif ( $is_preview ) : ?>
					<div class="obot-product-feature__image-placeholder">
						<?php esc_html_e( 'Add an image in the block fields.', 'oboto' ); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $list_items || $has_button || $is_preview ) : ?>
				<div class="obot-product-feature__content"<?php oboto_the_aos_attributes( 380 ); ?>>
					<?php if ( $list_items ) : ?>
						<ul class="obot-product-feature__list">
							<?php foreach ( $list_items as $item ) : ?>
								<li class="obot-product-feature__list-item">
									<span class="obot-product-feature__list-dot" aria-hidden="true"></span>
									<span><?php echo esc_html( $item ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php elseif ( $is_preview ) : ?>
						<div class="obot-product-feature__placeholder">
							<?php esc_html_e( 'Add list items in the block fields.', 'oboto' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $has_button ) : ?>
						<?php
						$link_target = ! empty( $button['target'] ) ? $button['target'] : '';
						$link_title  = ! empty( $button['title'] ) ? $button['title'] : $button['url'];
						?>
						<a
							class="obot-product-feature__button"
							href="<?php echo esc_url( $button['url'] ); ?>"
							<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
							<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						>
							<svg class="obot-product-feature__button-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
								<polyline points="14 2 14 8 20 8"></polyline>
								<line x1="12" y1="11" x2="12" y2="17"></line>
								<line x1="9" y1="14" x2="15" y2="14"></line>
							</svg>
							<span><?php echo esc_html( $link_title ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
