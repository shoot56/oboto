<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'product-feature-v2-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$section_eyebrow    = trim( (string) get_field( 'section_eyebrow' ) );
$section_title      = trim( (string) get_field( 'section_title' ) );
$has_section_header = $section_eyebrow || $section_title;
$media_on_left      = (bool) get_field( 'media_on_left' );
$wrapper_classes    = 'obot-product-feature-v2';
if ( $has_section_header ) {
	$wrapper_classes .= ' obot-product-feature-v2--has-section-header';
}
if ( $media_on_left ) {
	$wrapper_classes .= ' obot-product-feature-v2--media-left';
}
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
		return array( 91, 155, 255 );
	}

	return array(
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
	);
};

$accent_rgb          = $hex_to_rgb( $accent_color );
$contrast_threshold  = 125;
$accent_brightness   = ( $accent_rgb[0] * 299 + $accent_rgb[1] * 587 + $accent_rgb[2] * 114 ) / 1000;
$button_text_color   = $accent_brightness >= $contrast_threshold ? '#07101f' : '#ffffff';
$wrapper_attributes  = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
		'style' => sprintf(
			'--product-feature-v2-accent: %1$s; --product-feature-v2-accent-rgb: %2$s; --product-feature-v2-button-text: %3$s;',
			$accent_color,
			implode( ', ', $accent_rgb ),
			$button_text_color
		),
	)
);

$eyebrow            = trim( (string) get_field( 'eyebrow' ) );
$title              = trim( (string) get_field( 'title' ) );
$text               = trim( (string) get_field( 'text' ) );
$list               = get_field( 'list' );
$button             = get_field( 'button' );
$media_type         = sanitize_key( (string) get_field( 'media_type' ) );
$image              = get_field( 'image' );
$add_browser_header = (bool) get_field( 'add_browser_header' );
$browser_address    = trim( (string) get_field( 'browser_address' ) );
$video              = get_field( 'video' );
$video_poster       = get_field( 'video_poster' );
$raw_html           = trim( (string) get_field( 'raw_html' ) );
$html_sizing        = sanitize_key( (string) get_field( 'html_sizing' ) );
$html_ratio_width   = absint( get_field( 'html_ratio_width' ) );
$html_ratio_height  = absint( get_field( 'html_ratio_height' ) );

if ( ! in_array( $media_type, array( 'image', 'video', 'html' ), true ) ) {
	$media_type = 'image';
}

if ( ! in_array( $html_sizing, array( 'auto', 'ratio' ), true ) ) {
	$html_sizing = 'auto';
}

if ( ! $html_ratio_width ) {
	$html_ratio_width = 970;
}

if ( ! $html_ratio_height ) {
	$html_ratio_height = 516;
}

$resolve_image_data = static function ( $image_value ) {
	if ( is_array( $image_value ) && ! empty( $image_value['url'] ) ) {
		return array(
			'url' => (string) $image_value['url'],
			'alt' => (string) ( $image_value['alt'] ?? '' ),
		);
	}

	if ( is_numeric( $image_value ) ) {
		$image_src = wp_get_attachment_image_url( (int) $image_value, 'full' );
		if ( $image_src ) {
			return array(
				'url' => $image_src,
				'alt' => (string) get_post_meta( (int) $image_value, '_wp_attachment_image_alt', true ),
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
			'url'  => (string) $video_value['url'],
			'mime' => (string) ( $video_value['mime_type'] ?? '' ),
		);
	}

	if ( is_numeric( $video_value ) ) {
		$video_src = wp_get_attachment_url( (int) $video_value );
		if ( $video_src ) {
			return array(
				'url'  => $video_src,
				'mime' => (string) get_post_mime_type( (int) $video_value ),
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
$show_browser_header = $media_type === 'image' && $add_browser_header;
$has_media           = ( $media_type === 'image' && $image_data ) || ( $media_type === 'video' && $video_data ) || ( $media_type === 'html' && $raw_html );

$list_items = array();
if ( is_array( $list ) ) {
	foreach ( $list as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$item_text = trim( (string) ( $row['text'] ?? '' ) );
		$icon_data = $resolve_image_data( $row['icon'] ?? null );
		if ( $item_text === '' && ! $icon_data ) {
			continue;
		}

		$list_items[] = array(
			'icon' => $icon_data,
			'text' => $item_text,
		);
	}
}

$has_button  = is_array( $button ) && ! empty( $button['url'] );
$has_content = $eyebrow || $title || $text || $list_items || $has_button;
$content_animation_delay = $has_section_header ? 260 : 100;
$media_animation_delay   = $has_section_header ? 340 : 180;

$html_document      = $raw_html;
$html_resize_script = <<<'HTML'
<script>
(function () {
  var messageType = 'obot-product-feature-v2-html-height';
  var lastHeight = 0;
  var root = document.documentElement;

  function sendHeight() {
    var body = document.body;
    if (!body || !root) {
      return;
    }

    var height = Math.ceil(Math.max(
      body.scrollHeight,
      body.offsetHeight,
      body.getBoundingClientRect().height,
      root.scrollHeight,
      root.offsetHeight,
      root.getBoundingClientRect().height
    ));

    if (!Number.isFinite(height) || height < 1 || Math.abs(height - lastHeight) < 1) {
      return;
    }

    lastHeight = height;
    window.parent.postMessage({ type: messageType, height: height }, '*');
  }

  function scheduleMeasurements() {
    window.requestAnimationFrame(sendHeight);
    window.setTimeout(sendHeight, 50);
    window.setTimeout(sendHeight, 250);
    window.setTimeout(sendHeight, 1000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scheduleMeasurements, { once: true });
  } else {
    scheduleMeasurements();
  }

  window.addEventListener('load', scheduleMeasurements, { once: true });
  window.addEventListener('resize', sendHeight);

  if ('ResizeObserver' in window) {
    var resizeObserver = new ResizeObserver(sendHeight);
    resizeObserver.observe(root);
    if (document.body) {
      resizeObserver.observe(document.body);
    }
  } else if ('MutationObserver' in window) {
    var mutationObserver = new MutationObserver(sendHeight);
    mutationObserver.observe(root, { childList: true, subtree: true, attributes: true });
  }
})();
</script>
HTML;

if ( $media_type === 'html' && $html_document && $html_sizing === 'auto' ) {
	if ( preg_match( '/<\/body\s*>/i', $html_document ) ) {
		$resized_html_document = preg_replace( '/<\/body\s*>/i', $html_resize_script . '</body>', $html_document, 1 );
		if ( is_string( $resized_html_document ) ) {
			$html_document = $resized_html_document;
		}
	} else {
		$html_document .= $html_resize_script;
	}
}

$media_classes = 'obot-product-feature-v2__media';
$media_style   = '';
if ( $media_type === 'video' ) {
	$media_classes .= ' obot-product-feature-v2__media--video';
}
if ( $media_type === 'html' ) {
	$media_classes .= ' obot-product-feature-v2__media--html';
	$media_classes .= $html_sizing === 'ratio' ? ' obot-product-feature-v2__media--html-ratio' : ' obot-product-feature-v2__media--html-auto';

	if ( $html_sizing === 'ratio' ) {
		$media_style = '--product-feature-v2-html-aspect-ratio: ' . $html_ratio_width . ' / ' . $html_ratio_height . ';';
	}
}
if ( $show_browser_header ) {
	$media_classes .= ' obot-product-feature-v2__media--browser';
}

$body_classes = 'obot-product-feature-v2__body';
if ( ! $has_media && ! $is_preview ) {
	$body_classes .= ' obot-product-feature-v2__body--without-media';
}
if ( ! $has_content && ! $is_preview ) {
	$body_classes .= ' obot-product-feature-v2__body--without-content';
}

$media_placeholder = array(
	'image' => __( 'Add an image in the block fields.', 'oboto' ),
	'video' => __( 'Add a video in the block fields.', 'oboto' ),
	'html'  => __( 'Add HTML code in the block fields.', 'oboto' ),
);

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-product-feature-v2__inner">
		<?php if ( $has_section_header ) : ?>
			<header class="obot-product-feature-v2__section-header">
				<?php if ( $section_eyebrow ) : ?>
					<div class="obot-product-feature-v2__section-eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
						<span class="obot-product-feature-v2__section-eyebrow-dot" aria-hidden="true"></span>
						<span><?php echo esc_html( $section_eyebrow ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $section_title ) : ?>
					<h2 class="obot-product-feature-v2__section-title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $section_title ); ?></h2>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<div class="<?php echo esc_attr( $body_classes ); ?>">
			<?php if ( $has_content || $is_preview ) : ?>
				<div class="obot-product-feature-v2__content"<?php oboto_the_aos_attributes( $content_animation_delay ); ?>>
					<?php if ( $eyebrow ) : ?>
						<div class="obot-product-feature-v2__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<?php if ( $section_title ) : ?>
							<h3 class="obot-product-feature-v2__title"><?php echo esc_html( $title ); ?></h3>
						<?php else : ?>
							<h2 class="obot-product-feature-v2__title"><?php echo esc_html( $title ); ?></h2>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( $text ) : ?>
						<p class="obot-product-feature-v2__text"><?php echo nl2br( esc_html( $text ) ); ?></p>
					<?php endif; ?>

					<?php if ( $list_items ) : ?>
						<ul class="obot-product-feature-v2__list">
							<?php foreach ( $list_items as $item ) : ?>
								<li class="obot-product-feature-v2__list-item">
									<?php if ( $item['icon'] ) : ?>
										<img
											class="obot-product-feature-v2__list-icon"
											src="<?php echo esc_url( $item['icon']['url'] ); ?>"
											alt=""
											loading="lazy"
										>
									<?php endif; ?>
									<?php if ( $item['text'] ) : ?>
										<div class="obot-product-feature-v2__list-text"><?php echo wp_kses_post( $item['text'] ); ?></div>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php elseif ( $is_preview ) : ?>
						<div class="obot-product-feature-v2__placeholder">
							<?php esc_html_e( 'Add feature rows in the block fields.', 'oboto' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $has_button ) : ?>
						<?php
						$link_target = ! empty( $button['target'] ) ? $button['target'] : '';
						$link_title  = ! empty( $button['title'] ) ? $button['title'] : $button['url'];
						?>
						<a
							class="obot-product-feature-v2__button"
							href="<?php echo esc_url( $button['url'] ); ?>"
							<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
							<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						>
							<span><?php echo esc_html( $link_title ); ?></span>
							<svg class="obot-product-feature-v2__button-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M5 12h14"></path>
								<path d="m13 6 6 6-6 6"></path>
							</svg>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $has_media || $is_preview ) : ?>
				<div class="<?php echo esc_attr( $media_classes ); ?>"<?php echo $media_style ? ' style="' . esc_attr( $media_style ) . '"' : ''; ?><?php oboto_the_aos_attributes( $media_animation_delay ); ?>>
					<?php if ( $media_type === 'html' && $raw_html ) : ?>
						<iframe
							class="obot-product-feature-v2__html"
							title="<?php echo esc_attr( $title ? $title : __( 'Interactive feature preview', 'oboto' ) ); ?>"
							srcdoc="<?php echo esc_attr( $html_document ); ?>"
							sandbox="allow-scripts"
							loading="lazy"
							referrerpolicy="no-referrer"
							scrolling="no"
							<?php echo $html_sizing === 'auto' ? 'data-product-feature-v2-html-auto' : ''; ?>
						></iframe>
					<?php elseif ( $media_type === 'video' && $video_data ) : ?>
						<video
							class="obot-product-feature-v2__video"
							autoplay
							muted
							loop
							playsinline
							preload="metadata"
							<?php echo $video_poster_data ? 'poster="' . esc_url( $video_poster_data['url'] ) . '"' : ''; ?>
						>
							<source src="<?php echo esc_url( $video_data['url'] ); ?>"<?php echo ! empty( $video_data['mime'] ) ? ' type="' . esc_attr( $video_data['mime'] ) . '"' : ''; ?>>
						</video>
					<?php elseif ( $media_type === 'image' && $image_data ) : ?>
						<?php if ( $show_browser_header ) : ?>
							<div class="obot-product-feature-v2__browser-header" aria-hidden="true">
								<span class="obot-product-feature-v2__browser-controls">
									<span class="obot-product-feature-v2__browser-dot obot-product-feature-v2__browser-dot--close"></span>
									<span class="obot-product-feature-v2__browser-dot obot-product-feature-v2__browser-dot--minimize"></span>
									<span class="obot-product-feature-v2__browser-dot obot-product-feature-v2__browser-dot--maximize"></span>
								</span>
								<span class="obot-product-feature-v2__browser-address"><?php echo esc_html( $browser_address ); ?></span>
							</div>
						<?php endif; ?>
						<img class="obot-product-feature-v2__image" src="<?php echo esc_url( $image_data['url'] ); ?>" alt="<?php echo esc_attr( $image_data['alt'] ); ?>" loading="lazy">
					<?php else : ?>
						<div class="obot-product-feature-v2__media-placeholder"><?php echo esc_html( $media_placeholder[ $media_type ] ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
