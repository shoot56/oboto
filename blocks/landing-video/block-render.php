<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-video-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-video';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow       = trim( (string) get_field( 'eyebrow' ) );
$title         = trim( (string) get_field( 'title' ) );
$text          = trim( (string) get_field( 'text' ) );
$youtube_url   = get_field( 'youtube_url' );
$button_one    = get_field( 'button_one' );
$button_two    = get_field( 'button_two' );
$button_three  = get_field( 'button_three' );

$normalize_youtube_id = static function ( $id ) {
	if ( ! is_string( $id ) ) {
		return '';
	}

	$id = trim( $id );

	return preg_match( '/^[A-Za-z0-9_-]{6,64}$/', $id ) ? $id : '';
};

$get_youtube_id = static function ( $url ) use ( $normalize_youtube_id ) {
	if ( ! is_string( $url ) || $url === '' ) {
		return '';
	}

	$parts = wp_parse_url( $url );
	if ( empty( $parts['host'] ) ) {
		return '';
	}

	$host = strtolower( preg_replace( '/^www\./', '', $parts['host'] ) );

	if ( $host === 'youtu.be' && ! empty( $parts['path'] ) ) {
		return $normalize_youtube_id( trim( $parts['path'], '/' ) );
	}

	if ( in_array( $host, array( 'youtube.com', 'm.youtube.com', 'music.youtube.com' ), true ) ) {
		if ( ! empty( $parts['query'] ) ) {
			parse_str( $parts['query'], $query_args );
			if ( ! empty( $query_args['v'] ) && is_string( $query_args['v'] ) ) {
				return $normalize_youtube_id( $query_args['v'] );
			}
		}

		if ( ! empty( $parts['path'] ) && preg_match( '#/(embed|shorts)/([^/?]+)#', $parts['path'], $matches ) ) {
			return $normalize_youtube_id( $matches[2] );
		}
	}

	return '';
};

$youtube_id = $get_youtube_id( $youtube_url );
$embed_url  = $youtube_id ? 'https://www.youtube.com/embed/' . rawurlencode( $youtube_id ) . '?autoplay=1' : '';
$thumb_url  = $youtube_id ? 'https://img.youtube.com/vi/' . rawurlencode( $youtube_id ) . '/maxresdefault.jpg' : '';

$buttons = array();
if ( is_array( $button_one ) && ! empty( $button_one['url'] ) ) {
	$buttons[] = array(
		'data'        => $button_one,
		'class'       => 'obot-landing-video__button obot-landing-video__button--primary',
		'arrow_class' => 'obot-landing-video__button-arrow obot-landing-video__button-arrow--right',
	);
}

if ( is_array( $button_two ) && ! empty( $button_two['url'] ) ) {
	$buttons[] = array(
		'data'        => $button_two,
		'class'       => 'obot-landing-video__button obot-landing-video__button--outline',
		'arrow_class' => '',
	);
}

if ( is_array( $button_three ) && ! empty( $button_three['url'] ) ) {
	$buttons[] = array(
		'data'        => $button_three,
		'class'       => 'obot-landing-video__button obot-landing-video__button--text',
		'arrow_class' => 'obot-landing-video__button-arrow obot-landing-video__button-arrow--up-right',
	);
}

?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-video__inner">
		<div class="obot-landing-video__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-video__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-video__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-video__eyebrow-pulse"></span>
						<span class="obot-landing-video__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-landing-video__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="obot-landing-video__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $embed_url || $is_preview ) : ?>
			<div class="obot-landing-video__video-wrap">
				<div class="obot-landing-video__browser"<?php oboto_the_aos_attributes( 340 ); ?>>
					<div class="obot-landing-video__browser-bar" aria-hidden="true">
						<span class="obot-landing-video__window-dot obot-landing-video__window-dot--red"></span>
						<span class="obot-landing-video__window-dot obot-landing-video__window-dot--yellow"></span>
						<span class="obot-landing-video__window-dot obot-landing-video__window-dot--green"></span>
					</div>

					<div class="obot-landing-video__frame">
						<?php if ( $embed_url ) : ?>
							<button
								class="obot-landing-video__play"
								type="button"
								data-landing-video-play
								data-landing-video-src="<?php echo esc_url( $embed_url ); ?>"
							>
								<?php if ( $thumb_url ) : ?>
									<img
										class="obot-landing-video__thumb"
										src="<?php echo esc_url( $thumb_url ); ?>"
										alt="<?php echo esc_attr( $title ); ?>"
										loading="lazy"
									>
								<?php endif; ?>
								<span class="obot-landing-video__play-icon" aria-hidden="true"></span>
								<span class="screen-reader-text"><?php esc_html_e( 'Play video', 'oboto' ); ?></span>
							</button>
						<?php elseif ( $is_preview ) : ?>
							<div class="obot-landing-video__placeholder">
								<?php esc_html_e( 'Add a YouTube video URL in the block fields.', 'oboto' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $buttons ) : ?>
			<div class="obot-landing-video__actions">
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
						<?php oboto_the_aos_attributes( 420 + ( $index * 70 ) ); ?>
					>
						<span><?php echo esc_html( $link_title ); ?></span>
						<?php if ( $button['arrow_class'] ) : ?>
							<span class="<?php echo esc_attr( $button['arrow_class'] ); ?>" aria-hidden="true"></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-video__actions-placeholder">
				<?php esc_html_e( 'Add the three buttons in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
