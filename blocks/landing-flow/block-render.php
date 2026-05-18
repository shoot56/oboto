<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-flow-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-flow';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow             = trim( (string) get_field( 'eyebrow' ) );
$title               = trim( (string) get_field( 'title' ) );
$animated_svg        = get_field( 'animated_svg' );
$mobile_animated_svg = get_field( 'mobile_animated_svg' );
$badges              = get_field( 'badges' );

$get_svg_data = static function ( $svg_field, $fallback_alt ) {
	if ( is_array( $svg_field ) && ! empty( $svg_field['url'] ) ) {
		$attachment_id = ! empty( $svg_field['ID'] ) ? (int) $svg_field['ID'] : 0;

		return array(
			'url'  => $svg_field['url'],
			'alt'  => ! empty( $svg_field['title'] ) ? $svg_field['title'] : $fallback_alt,
			'path' => $attachment_id ? get_attached_file( $attachment_id ) : '',
		);
	}

	if ( is_numeric( $svg_field ) ) {
		$svg_src = wp_get_attachment_url( (int) $svg_field );
		if ( $svg_src ) {
			return array(
				'url'  => $svg_src,
				'alt'  => get_the_title( (int) $svg_field ) ?: $fallback_alt,
				'path' => get_attached_file( (int) $svg_field ),
			);
		}
	}

	if ( is_string( $svg_field ) && $svg_field !== '' ) {
		return array(
			'url'  => $svg_field,
			'alt'  => $fallback_alt,
			'path' => '',
		);
	}

	return null;
};

$svg_data        = $get_svg_data( $animated_svg, '' );
$mobile_svg_data = $get_svg_data( $mobile_animated_svg, '' );

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
	),
	'defs' => array(),
	'g' => array(
		'fill' => true,
		'fill-opacity' => true,
		'filter' => true,
		'font-family' => true,
		'font-size' => true,
		'font-weight' => true,
		'letter-spacing' => true,
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
		'stroke-opacity' => true,
		'stroke-width' => true,
	),
	'path' => array(
		'd' => true,
		'fill' => true,
		'fill-opacity' => true,
		'id' => true,
		'stroke' => true,
		'stroke-dasharray' => true,
		'stroke-dashoffset' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
	),
	'polygon' => array(
		'fill' => true,
		'fill-opacity' => true,
		'points' => true,
		'stroke' => true,
		'stroke-opacity' => true,
		'stroke-width' => true,
	),
	'rect' => array(
		'fill' => true,
		'fill-opacity' => true,
		'height' => true,
		'rx' => true,
		'ry' => true,
		'stroke' => true,
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
		'repeatcount' => true,
	),
	'mpath' => array(
		'href' => true,
		'xlink:href' => true,
	),
);

$get_svg_markup = static function ( $svg_data ) use ( $allowed_svg_html, $get_upload_path_from_url ) {
	if ( ! $svg_data ) {
		return '';
	}

	$svg_path = ! empty( $svg_data['path'] ) ? $svg_data['path'] : $get_upload_path_from_url( $svg_data['url'] );

	if ( ! $svg_path || ! is_readable( $svg_path ) || strtolower( pathinfo( $svg_path, PATHINFO_EXTENSION ) ) !== 'svg' ) {
		return '';
	}

	$raw_svg = file_get_contents( $svg_path );
	if ( ! is_string( $raw_svg ) || $raw_svg === '' ) {
		return '';
	}

	$raw_svg = preg_replace( '/<\?xml.*?\?>/is', '', $raw_svg );
	$raw_svg = preg_replace( '/<!doctype.*?>/is', '', $raw_svg );

	return wp_kses( $raw_svg, $allowed_svg_html );
};

$svg_markup        = $get_svg_markup( $svg_data );
$mobile_svg_markup = $get_svg_markup( $mobile_svg_data );
$has_mobile_svg    = ! empty( $mobile_svg_markup ) || ! empty( $mobile_svg_data );

$desktop_graphic_classes = 'obot-landing-flow__graphic obot-landing-flow__graphic--desktop';
if ( $has_mobile_svg ) {
	$desktop_graphic_classes .= ' obot-landing-flow__graphic--has-mobile';
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
	<div class="obot-landing-flow__background" aria-hidden="true"></div>
	<div class="obot-landing-flow__inner">
		<div class="obot-landing-flow__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-flow__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-flow__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-flow__eyebrow-pulse"></span>
						<span class="obot-landing-flow__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-landing-flow__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</div>

		<div class="<?php echo esc_attr( $desktop_graphic_classes ); ?>" aria-hidden="true"<?php oboto_the_aos_attributes( 260 ); ?>>
			<?php if ( $svg_markup ) : ?>
				<?php echo $svg_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php elseif ( $svg_data ) : ?>
				<img
					class="obot-landing-flow__image"
					src="<?php echo esc_url( $svg_data['url'] ); ?>"
					alt="<?php echo esc_attr( $svg_data['alt'] ); ?>"
					loading="lazy"
				>
			<?php elseif ( $mobile_svg_markup ) : ?>
				<?php echo $mobile_svg_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php elseif ( $mobile_svg_data ) : ?>
				<img
					class="obot-landing-flow__image"
					src="<?php echo esc_url( $mobile_svg_data['url'] ); ?>"
					alt="<?php echo esc_attr( $mobile_svg_data['alt'] ); ?>"
					loading="lazy"
				>
			<?php elseif ( $is_preview ) : ?>
				<div class="obot-landing-flow__placeholder">
					<?php esc_html_e( 'Upload an animated SVG in the block fields.', 'oboto' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $has_mobile_svg ) : ?>
			<div class="obot-landing-flow__graphic obot-landing-flow__graphic--mobile" aria-hidden="true"<?php oboto_the_aos_attributes( 260 ); ?>>
				<?php if ( $mobile_svg_markup ) : ?>
					<?php echo $mobile_svg_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php elseif ( $mobile_svg_data ) : ?>
					<img
						class="obot-landing-flow__image"
						src="<?php echo esc_url( $mobile_svg_data['url'] ); ?>"
						alt="<?php echo esc_attr( $mobile_svg_data['alt'] ); ?>"
						loading="lazy"
					>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $badge_items ) : ?>
			<ul class="obot-landing-flow__badges" aria-label="<?php esc_attr_e( 'Traffic flow notes', 'oboto' ); ?>">
				<?php foreach ( $badge_items as $index => $badge ) : ?>
					<li class="obot-landing-flow__badge"<?php oboto_the_aos_attributes( 340 + ( $index * 70 ) ); ?>><?php echo esc_html( $badge ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-flow__badges-placeholder">
				<?php esc_html_e( 'Add bottom badges in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
