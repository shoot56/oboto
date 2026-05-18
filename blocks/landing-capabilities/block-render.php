<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-capabilities-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-capabilities';
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
$rows    = get_field( 'items' );

$get_upload_path_from_url = static function ( $url ) {
	$uploads  = wp_get_upload_dir();
	$base_url = isset( $uploads['baseurl'] ) ? untrailingslashit( $uploads['baseurl'] ) : '';
	$base_dir = isset( $uploads['basedir'] ) ? untrailingslashit( $uploads['basedir'] ) : '';

	if ( ! $base_url || ! $base_dir || strpos( $url, $base_url ) !== 0 ) {
		return '';
	}

	$relative_path = ltrim( substr( $url, strlen( $base_url ) ), '/' );
	$file_path     = $base_dir . '/' . $relative_path;
	$real_base     = realpath( $base_dir );
	$real_file     = realpath( $file_path );

	if ( ! $real_base || ! $real_file || strpos( $real_file, $real_base ) !== 0 ) {
		return '';
	}

	return $real_file;
};

$get_media_data = static function ( $media_field, $fallback_alt ) {
	if ( is_array( $media_field ) && ! empty( $media_field['url'] ) ) {
		$attachment_id = ! empty( $media_field['ID'] ) ? (int) $media_field['ID'] : 0;
		$alt           = ! empty( $media_field['alt'] ) ? $media_field['alt'] : '';

		return array(
			'url'  => $media_field['url'],
			'alt'  => $alt ?: ( ! empty( $media_field['title'] ) ? $media_field['title'] : $fallback_alt ),
			'path' => $attachment_id ? get_attached_file( $attachment_id ) : '',
		);
	}

	if ( is_numeric( $media_field ) ) {
		$media_src = wp_get_attachment_url( (int) $media_field );
		if ( $media_src ) {
			return array(
				'url'  => $media_src,
				'alt'  => get_post_meta( (int) $media_field, '_wp_attachment_image_alt', true ) ?: $fallback_alt,
				'path' => get_attached_file( (int) $media_field ),
			);
		}
	}

	if ( is_string( $media_field ) && $media_field !== '' ) {
		return array(
			'url'  => $media_field,
			'alt'  => $fallback_alt,
			'path' => '',
		);
	}

	return null;
};

$allowed_svg_html = array(
	'svg' => array(
		'aria-hidden' => true,
		'class' => true,
		'focusable' => true,
		'height' => true,
		'preserveaspectratio' => true,
		'role' => true,
		'viewbox' => true,
		'width' => true,
		'xmlns' => true,
		'xmlns:xlink' => true,
	),
	'defs' => array(),
	'g' => array(
		'clip-path' => true,
		'fill' => true,
		'fill-opacity' => true,
		'filter' => true,
		'font-family' => true,
		'font-size' => true,
		'font-weight' => true,
		'letter-spacing' => true,
		'mask' => true,
		'opacity' => true,
		'stroke' => true,
		'stroke-width' => true,
		'transform' => true,
	),
	'filter' => array(
		'height' => true,
		'id' => true,
		'width' => true,
		'x' => true,
		'y' => true,
	),
	'fegaussianblur' => array(
		'in' => true,
		'result' => true,
		'stddeviation' => true,
	),
	'femerge' => array(),
	'femergenode' => array(
		'in' => true,
	),
	'clippath' => array(
		'id' => true,
	),
	'mask' => array(
		'height' => true,
		'id' => true,
		'maskunits' => true,
		'width' => true,
		'x' => true,
		'y' => true,
	),
	'radialgradient' => array(
		'id' => true,
	),
	'lineargradient' => array(
		'id' => true,
		'x1' => true,
		'x2' => true,
		'y1' => true,
		'y2' => true,
	),
	'stop' => array(
		'offset' => true,
		'stop-color' => true,
		'stop-opacity' => true,
	),
	'circle' => array(
		'cx' => true,
		'cy' => true,
		'fill' => true,
		'fill-opacity' => true,
		'opacity' => true,
		'r' => true,
		'stroke' => true,
		'stroke-dasharray' => true,
		'stroke-dashoffset' => true,
		'stroke-linecap' => true,
		'stroke-linejoin' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
	),
	'ellipse' => array(
		'cx' => true,
		'cy' => true,
		'fill' => true,
		'fill-opacity' => true,
		'opacity' => true,
		'rx' => true,
		'ry' => true,
		'stroke' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
	),
	'path' => array(
		'clip-rule' => true,
		'd' => true,
		'fill' => true,
		'fill-opacity' => true,
		'fill-rule' => true,
		'id' => true,
		'opacity' => true,
		'stroke' => true,
		'stroke-dasharray' => true,
		'stroke-dashoffset' => true,
		'stroke-linecap' => true,
		'stroke-linejoin' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
	),
	'polygon' => array(
		'fill' => true,
		'fill-opacity' => true,
		'points' => true,
		'stroke' => true,
		'stroke-linejoin' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
	),
	'polyline' => array(
		'fill' => true,
		'points' => true,
		'stroke' => true,
		'stroke-linecap' => true,
		'stroke-linejoin' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
	),
	'rect' => array(
		'fill' => true,
		'fill-opacity' => true,
		'height' => true,
		'opacity' => true,
		'rx' => true,
		'ry' => true,
		'stroke' => true,
		'stroke-dasharray' => true,
		'stroke-dashoffset' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
		'width' => true,
		'x' => true,
		'y' => true,
	),
	'line' => array(
		'stroke' => true,
		'stroke-dasharray' => true,
		'stroke-dashoffset' => true,
		'stroke-linecap' => true,
		'stroke-linejoin' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
		'x1' => true,
		'x2' => true,
		'y1' => true,
		'y2' => true,
	),
	'text' => array(
		'fill' => true,
		'fill-opacity' => true,
		'font-family' => true,
		'font-size' => true,
		'font-weight' => true,
		'letter-spacing' => true,
		'opacity' => true,
		'text-anchor' => true,
		'transform' => true,
		'x' => true,
		'y' => true,
	),
	'animate' => array(
		'attributename' => true,
		'begin' => true,
		'dur' => true,
		'from' => true,
		'repeatcount' => true,
		'to' => true,
		'values' => true,
	),
	'animatemotion' => array(
		'begin' => true,
		'dur' => true,
		'path' => true,
		'repeatcount' => true,
	),
	'mpath' => array(
		'href' => true,
		'xlink:href' => true,
	),
);

$get_svg_markup = static function ( $media_data ) use ( $allowed_svg_html, $get_upload_path_from_url ) {
	if ( ! $media_data ) {
		return '';
	}

	$media_path = ! empty( $media_data['path'] ) ? $media_data['path'] : $get_upload_path_from_url( $media_data['url'] );

	if ( ! $media_path || ! is_readable( $media_path ) || strtolower( pathinfo( $media_path, PATHINFO_EXTENSION ) ) !== 'svg' ) {
		return '';
	}

	$raw_svg = file_get_contents( $media_path );
	if ( ! is_string( $raw_svg ) || $raw_svg === '' ) {
		return '';
	}

	$raw_svg = preg_replace( '/<\?xml.*?\?>/is', '', $raw_svg );
	$raw_svg = preg_replace( '/<!doctype.*?>/is', '', $raw_svg );

	return wp_kses( $raw_svg, $allowed_svg_html );
};

$items = array();
if ( is_array( $rows ) ) {
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$item_title = ! empty( $row['title'] ) ? trim( (string) $row['title'] ) : '';
		$tab        = ! empty( $row['tab'] ) ? trim( (string) $row['tab'] ) : $item_title;
		$item_text  = ! empty( $row['text'] ) ? trim( (string) $row['text'] ) : '';

		if ( $tab === '' && $item_title === '' && $item_text === '' ) {
			continue;
		}

		$icon_data  = $get_media_data( isset( $row['icon'] ) ? $row['icon'] : null, $item_title );
		$image_data = $get_media_data( isset( $row['image'] ) ? $row['image'] : null, $item_title );

		$items[] = array(
			'tab'          => $tab,
			'icon'         => $icon_data,
			'icon_svg'     => $get_svg_markup( $icon_data ),
			'title'        => $item_title,
			'text'         => $item_text,
			'link'         => isset( $row['link'] ) && is_array( $row['link'] ) ? $row['link'] : null,
			'image'        => $image_data,
			'image_svg'    => $get_svg_markup( $image_data ),
		);
	}
}

?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?> data-landing-capabilities>
	<div class="obot-landing-capabilities__glow" aria-hidden="true"></div>
	<div class="obot-landing-capabilities__inner">
		<div class="obot-landing-capabilities__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-capabilities__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-capabilities__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-capabilities__eyebrow-pulse"></span>
						<span class="obot-landing-capabilities__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-landing-capabilities__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</div>

		<?php if ( $items ) : ?>
			<div class="obot-landing-capabilities__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Capabilities', 'oboto' ); ?>">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$is_active = $index === 0;
					$tab_id    = $id . '-tab-' . $index;
					$panel_id  = $id . '-panel-' . $index;
					?>
					<button
						class="obot-landing-capabilities__tab<?php echo $is_active ? ' is-active' : ''; ?>"
						type="button"
						role="tab"
						id="<?php echo esc_attr( $tab_id ); ?>"
						aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
						aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						data-capability-tab="<?php echo esc_attr( (string) $index ); ?>"
						<?php oboto_the_aos_attributes( 260 + ( $index * 60 ) ); ?>
					>
						<?php echo esc_html( $item['tab'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php if ( count( $items ) > 1 ) : ?>
				<div class="obot-landing-capabilities__progress" aria-hidden="true">
					<span class="obot-landing-capabilities__progress-fill"></span>
				</div>
			<?php endif; ?>

			<div class="obot-landing-capabilities__panels">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$is_active = $index === 0;
					$tab_id    = $id . '-tab-' . $index;
					$panel_id  = $id . '-panel-' . $index;
					$link      = $item['link'];
					?>
					<div
						class="obot-landing-capabilities__panel<?php echo $is_active ? ' is-active' : ''; ?>"
						id="<?php echo esc_attr( $panel_id ); ?>"
						role="tabpanel"
						aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
						data-capability-panel="<?php echo esc_attr( (string) $index ); ?>"
						<?php echo $is_active ? '' : 'hidden'; ?>
					>
						<div class="obot-landing-capabilities__panel-content">
							<div class="obot-landing-capabilities__copy">
								<?php if ( $item['icon'] || $item['icon_svg'] ) : ?>
									<div class="obot-landing-capabilities__icon" aria-hidden="true"<?php if ( $is_active ) { oboto_the_aos_attributes( 360 ); } ?>>
										<?php if ( $item['icon_svg'] ) : ?>
											<?php echo $item['icon_svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php elseif ( $item['icon'] ) : ?>
											<img
												class="obot-landing-capabilities__icon-image"
												src="<?php echo esc_url( $item['icon']['url'] ); ?>"
												alt=""
												loading="lazy"
											>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ( $item['title'] ) : ?>
									<h3 class="obot-landing-capabilities__panel-title"<?php if ( $is_active ) { oboto_the_aos_attributes( 420 ); } ?>><?php echo esc_html( $item['title'] ); ?></h3>
								<?php endif; ?>

								<?php if ( $item['text'] ) : ?>
									<p class="obot-landing-capabilities__text"<?php if ( $is_active ) { oboto_the_aos_attributes( 480 ); } ?>><?php echo esc_html( $item['text'] ); ?></p>
								<?php endif; ?>

								<?php if ( $link && ! empty( $link['url'] ) ) : ?>
									<a
										class="obot-landing-capabilities__link"
										href="<?php echo esc_url( $link['url'] ); ?>"
										<?php echo ! empty( $link['target'] ) ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
										<?php echo ! empty( $link['target'] ) && $link['target'] === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
										<?php if ( $is_active ) { oboto_the_aos_attributes( 540 ); } ?>
									>
										<span><?php echo esc_html( ! empty( $link['title'] ) ? $link['title'] : __( 'Learn more', 'oboto' ) ); ?></span>
										<span class="obot-landing-capabilities__link-arrow" aria-hidden="true"></span>
									</a>
								<?php endif; ?>
							</div>

							<div class="obot-landing-capabilities__media" aria-hidden="true"<?php if ( $is_active ) { oboto_the_aos_attributes( 360 ); } ?>>
								<?php if ( $item['image_svg'] ) : ?>
									<?php echo $item['image_svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php elseif ( $item['image'] ) : ?>
									<img
										class="obot-landing-capabilities__image"
										src="<?php echo esc_url( $item['image']['url'] ); ?>"
										alt="<?php echo esc_attr( $item['image']['alt'] ); ?>"
										loading="lazy"
									>
								<?php elseif ( $is_preview ) : ?>
									<div class="obot-landing-capabilities__media-placeholder">
										<?php esc_html_e( 'Add an animated image or SVG in this row.', 'oboto' ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-capabilities__empty">
				<?php esc_html_e( 'Add capability rows in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
