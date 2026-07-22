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

$eyebrow      = trim( (string) get_field( 'eyebrow' ) );
$title        = trim( (string) get_field( 'title' ) );
$text         = trim( (string) get_field( 'text' ) );
$media_type   = sanitize_key( (string) get_field( 'media_type' ) );
$image        = get_field( 'image' );
$video        = get_field( 'video' );
$video_poster = get_field( 'video_poster' );
$list         = get_field( 'list' );
$button       = get_field( 'button' );
$button_icon  = get_field( 'button_icon' );

if ( ! in_array( $media_type, array( 'image', 'video' ), true ) ) {
	$media_type = 'image';
}

$resolve_image_data = static function ( $image_value ) {
	if ( is_array( $image_value ) && ! empty( $image_value['url'] ) ) {
		return array(
			'url' => $image_value['url'],
			'alt' => ! empty( $image_value['alt'] ) ? $image_value['alt'] : '',
		);
	}

	if ( is_numeric( $image_value ) ) {
		$image_src = wp_get_attachment_image_url( (int) $image_value, 'large' );
		if ( $image_src ) {
			return array(
				'url' => $image_src,
				'alt' => get_post_meta( (int) $image_value, '_wp_attachment_image_alt', true ),
			);
		}
	}

	if ( is_string( $image_value ) && $image_value !== '' ) {
		return array(
			'url' => $image_value,
			'alt' => '',
		);
	}

	return null;
};

$resolve_video_data = static function ( $video_value ) {
	if ( is_array( $video_value ) && ! empty( $video_value['url'] ) ) {
		return array(
			'url'  => $video_value['url'],
			'mime' => ! empty( $video_value['mime_type'] ) ? $video_value['mime_type'] : '',
		);
	}

	if ( is_numeric( $video_value ) ) {
		$video_src = wp_get_attachment_url( (int) $video_value );
		if ( $video_src ) {
			return array(
				'url'  => $video_src,
				'mime' => get_post_mime_type( (int) $video_value ),
			);
		}
	}

	if ( is_string( $video_value ) && $video_value !== '' ) {
		return array(
			'url'  => $video_value,
			'mime' => '',
		);
	}

	return null;
};

$image_data        = $resolve_image_data( $image );
$video_data        = $resolve_video_data( $video );
$video_poster_data = $resolve_image_data( $video_poster );
$button_icon_data  = is_array( $button_icon ) || is_numeric( $button_icon ) ? $resolve_image_data( $button_icon ) : null;
$media_classes     = 'obot-product-feature__media';
if ( $media_type === 'video' ) {
	$media_classes .= ' obot-product-feature__media--video';
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
				<div class="obot-product-feature__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-product-feature__eyebrow-dot" aria-hidden="true"></span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-product-feature__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="obot-product-feature__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>

		<div class="obot-product-feature__body">
			<div class="<?php echo esc_attr( $media_classes ); ?>"<?php oboto_the_aos_attributes( 320 ); ?>>
				<?php if ( $media_type === 'video' && $video_data ) : ?>
					<video
						class="obot-product-feature__video"
						autoplay
						muted
						loop
						playsinline
						preload="metadata"
						<?php echo $video_poster_data ? 'poster="' . esc_url( $video_poster_data['url'] ) . '"' : ''; ?>
					>
						<source
							src="<?php echo esc_url( $video_data['url'] ); ?>"
							<?php echo ! empty( $video_data['mime'] ) ? 'type="' . esc_attr( $video_data['mime'] ) . '"' : ''; ?>
						>
					</video>
				<?php elseif ( $media_type === 'image' && $image_data ) : ?>
					<img
						class="obot-product-feature__image"
						src="<?php echo esc_url( $image_data['url'] ); ?>"
						alt="<?php echo esc_attr( $image_data['alt'] ); ?>"
						loading="lazy"
					>
				<?php elseif ( $is_preview ) : ?>
					<div class="obot-product-feature__image-placeholder">
						<?php
						echo esc_html(
							$media_type === 'video'
								? __( 'Add a video in the block fields.', 'oboto' )
								: __( 'Add an image in the block fields.', 'oboto' )
						);
						?>
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
							<?php if ( $button_icon_data ) : ?>
								<img
									class="obot-product-feature__button-icon"
									src="<?php echo esc_url( $button_icon_data['url'] ); ?>"
									alt=""
									loading="lazy"
								>
							<?php else : ?>
								<svg class="obot-product-feature__button-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
									<path d="M5 12h14"></path>
									<path d="m13 6 6 6-6 6"></path>
								</svg>
							<?php endif; ?>
							<span><?php echo esc_html( $link_title ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
