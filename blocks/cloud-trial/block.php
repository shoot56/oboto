<?php

/**
 * Register assets for the Obot Cloud Trial block.
 */
function cloud_trial_register_assets() {
	$stylesheet_path = get_template_directory() . '/css/cloud-trial.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/cloud-trial.css';

	wp_register_style(
		'cloud-trial',
		$stylesheet_url,
		array(),
		filemtime( $stylesheet_path )
	);
}
add_action( 'wp_enqueue_scripts', 'cloud_trial_register_assets' );
add_action( 'admin_enqueue_scripts', 'cloud_trial_register_assets' );

function cloud_trial_editor_styles() {
	$stylesheet_path = get_template_directory() . '/css/cloud-trial.css';
	$stylesheet_url  = get_template_directory_uri() . '/css/cloud-trial.css';

	add_editor_style(
		add_query_arg(
			'ver',
			filemtime( $stylesheet_path ),
			$stylesheet_url
		)
	);
}
add_action( 'init', 'cloud_trial_editor_styles' );

function cloud_trial_enqueue_editor_assets() {
	cloud_trial_register_assets();
	wp_enqueue_style( 'cloud-trial' );
}
add_action( 'enqueue_block_editor_assets', 'cloud_trial_enqueue_editor_assets' );

/**
 * Inline SVG icons used by the block chrome (checklist ticks and the hero note).
 *
 * Editable icons are uploaded through ACF image fields instead.
 *
 * @param string $name Icon slug.
 *
 * @return string Inline SVG markup, or an empty string for unknown icons.
 */
function cloud_trial_get_icon( $name ) {
	$attributes = 'xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';

	$paths = array(
		'check'        => '<path d="M20 6 9 17l-5-5"/>',
		'shield-check' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
	);

	$name = sanitize_key( $name );

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return '<svg ' . $attributes . '>' . $paths[ $name ] . '</svg>';
}

/**
 * Normalise an ACF image value (array, attachment ID or URL).
 *
 * @param mixed $image ACF image field value.
 *
 * @return array|null Array with 'id', 'url', 'alt' and 'mime', or null when empty.
 */
function cloud_trial_get_image( $image ) {
	$id   = 0;
	$url  = '';
	$alt  = '';
	$mime = '';

	if ( is_array( $image ) ) {
		$id   = (int) ( $image['ID'] ?? $image['id'] ?? 0 );
		$url  = trim( (string) ( $image['url'] ?? '' ) );
		$alt  = trim( (string) ( $image['alt'] ?? '' ) );
		$mime = trim( (string) ( $image['mime_type'] ?? '' ) );
	} elseif ( is_numeric( $image ) ) {
		$id = (int) $image;
	} elseif ( is_string( $image ) ) {
		$url = trim( $image );
	}

	if ( $id ) {
		if ( '' === $url ) {
			$url = (string) wp_get_attachment_image_url( $id, 'full' );
		}
		if ( '' === $alt ) {
			$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		}
		if ( '' === $mime ) {
			$mime = (string) get_post_mime_type( $id );
		}
	}

	if ( '' === $url ) {
		return null;
	}

	if ( '' === $mime && preg_match( '#\.svg(\?|$)#i', $url ) ) {
		$mime = 'image/svg+xml';
	}

	return array(
		'id'   => $id,
		'url'  => $url,
		'alt'  => $alt,
		'mime' => $mime,
	);
}

/**
 * Allowed tags and attributes for inlined SVG icons.
 *
 * @return array
 */
function cloud_trial_svg_allowed_html() {
	$shared = array(
		'fill'             => array(),
		'stroke'           => array(),
		'stroke-width'     => array(),
		'stroke-linecap'   => array(),
		'stroke-linejoin'  => array(),
		'stroke-dasharray' => array(),
		'opacity'          => array(),
		'transform'        => array(),
		'class'            => array(),
		'style'            => array(),
	);

	return array(
		'svg'            => array_merge(
			$shared,
			array(
				'xmlns'       => array(),
				'viewbox'     => array(),
				'width'       => array(),
				'height'      => array(),
				'role'        => array(),
				'aria-hidden' => array(),
				'focusable'   => array(),
			)
		),
		'g'              => $shared,
		'defs'           => array(),
		'title'          => array(),
		'path'           => array_merge( $shared, array( 'd' => array() ) ),
		'circle'         => array_merge( $shared, array( 'cx' => array(), 'cy' => array(), 'r' => array() ) ),
		'ellipse'        => array_merge( $shared, array( 'cx' => array(), 'cy' => array(), 'rx' => array(), 'ry' => array() ) ),
		'rect'           => array_merge( $shared, array( 'x' => array(), 'y' => array(), 'width' => array(), 'height' => array(), 'rx' => array(), 'ry' => array() ) ),
		'line'           => array_merge( $shared, array( 'x1' => array(), 'y1' => array(), 'x2' => array(), 'y2' => array() ) ),
		'polyline'       => array_merge( $shared, array( 'points' => array() ) ),
		'polygon'        => array_merge( $shared, array( 'points' => array() ) ),
		'lineargradient' => array( 'id' => array(), 'x1' => array(), 'y1' => array(), 'x2' => array(), 'y2' => array(), 'gradientunits' => array() ),
		'radialgradient' => array( 'id' => array(), 'cx' => array(), 'cy' => array(), 'r' => array(), 'gradientunits' => array() ),
		'stop'           => array( 'offset' => array(), 'stop-color' => array(), 'stop-opacity' => array() ),
		'use'            => array_merge( $shared, array( 'href' => array(), 'x' => array(), 'y' => array() ) ),
	);
}

/**
 * Render an uploaded icon.
 *
 * SVG uploads are inlined so they inherit the surrounding colour (Lucide-style
 * icons use `currentColor`); every other file type falls back to an <img>.
 *
 * @param array|null $image Value returned by cloud_trial_get_image().
 *
 * @return string Icon markup, or an empty string.
 */
function cloud_trial_render_image( $image ) {
	if ( ! is_array( $image ) || empty( $image['url'] ) ) {
		return '';
	}

	if ( 'image/svg+xml' === $image['mime'] && ! empty( $image['id'] ) ) {
		$file = get_attached_file( (int) $image['id'] );

		if ( $file && is_readable( $file ) && filesize( $file ) <= 100000 ) {
			$svg = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$svg = wp_kses( (string) $svg, cloud_trial_svg_allowed_html() );

			if ( '' !== trim( $svg ) ) {
				return $svg;
			}
		}
	}

	return sprintf(
		'<img src="%s" alt="%s" loading="lazy" decoding="async" />',
		esc_url( $image['url'] ),
		esc_attr( $image['alt'] )
	);
}

/**
 * Allow a small set of inline tags inside rich-text headings.
 *
 * Paragraph wrappers added by the editor are unwrapped so the value can live
 * inside an <h1>, while <span> is kept so parts of the headline can be
 * highlighted.
 *
 * @param string $html Raw field value.
 *
 * @return string Safe inline HTML.
 */
function cloud_trial_inline_html( $html ) {
	$html = (string) $html;

	$html = preg_replace( '#\s*</p>\s*<p[^>]*>\s*#i', '<br />', $html );
	$html = preg_replace( '#</?p[^>]*>#i', '', $html );

	$allowed = array(
		'span'   => array(
			'class' => array(),
			'style' => array(),
		),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'mark'   => array(),
		'br'     => array(),
		'a'      => array(
			'href'   => array(),
			'target' => array(),
			'rel'    => array(),
			'class'  => array(),
		),
	);

	return trim( wp_kses( $html, $allowed ) );
}

/**
 * Convert a hex color to a comma separated RGB triplet.
 *
 * @param string $color Hex color, with or without a leading hash.
 *
 * @return string RGB triplet, e.g. "79, 126, 243".
 */
function cloud_trial_hex_to_rgb( $color ) {
	$hex = ltrim( (string) $color, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) ) {
		return '79, 126, 243';
	}

	return implode( ', ', array_map( 'hexdec', str_split( $hex, 2 ) ) );
}
