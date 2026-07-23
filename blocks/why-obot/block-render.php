<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'why-obot-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-why-obot';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow   = trim( (string) get_field( 'eyebrow' ) );
$title      = trim( (string) get_field( 'title' ) );
$text       = trim( (string) get_field( 'text' ) );
$rows       = get_field( 'cards' );
$cards      = array();
$has_header = $eyebrow || $title || $text;

if ( is_array( $rows ) ) {
	foreach ( $rows as $row ) {
		$card_title   = trim( (string) ( $row['title'] ?? '' ) );
		$card_text    = trim( (string) ( $row['text'] ?? '' ) );
		$accent_color = sanitize_hex_color( (string) ( $row['accent_color'] ?? '' ) );
		$accent_rgb   = '';
		$icon         = $row['icon'] ?? null;
		$link         = $row['link'] ?? null;
		$icon_url     = '';
		$icon_alt     = '';

		if ( $accent_color ) {
			$hex = ltrim( $accent_color, '#' );
			if ( strlen( $hex ) === 3 ) {
				$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
			}

			$accent_rgb = implode(
				', ',
				array_map(
					'hexdec',
					str_split( $hex, 2 )
				)
			);
		}

		if ( is_array( $icon ) ) {
			$icon_url = (string) ( $icon['url'] ?? '' );
			$icon_alt = (string) ( $icon['alt'] ?? '' );
		} elseif ( is_numeric( $icon ) ) {
			$icon_url = (string) wp_get_attachment_image_url( (int) $icon, 'thumbnail' );
			$icon_alt = (string) get_post_meta( (int) $icon, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $icon ) ) {
			$icon_url = $icon;
		}

		$link_url = '';
		if ( is_array( $link ) ) {
			$link_url = trim( (string) ( $link['url'] ?? '' ) );
		} elseif ( is_string( $link ) ) {
			$link_url = trim( $link );
			$link     = array(
				'url' => $link_url,
			);
		}

		if ( ! $card_title && ! $card_text && ! $icon_url && ! $link_url ) {
			continue;
		}

		$cards[] = array(
			'accent_color' => $accent_color,
			'accent_rgb'   => $accent_rgb,
			'icon_url'     => $icon_url,
			'icon_alt'     => $icon_alt,
			'title'        => $card_title,
			'text'         => $card_text,
			'link'         => $link,
		);
	}
}

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-why-obot__inner">
		<?php if ( $has_header ) : ?>
			<header class="obot-why-obot__header">
				<?php if ( $eyebrow ) : ?>
					<div class="obot-why-obot__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
						<span class="obot-why-obot__eyebrow-dot" aria-hidden="true"></span>
						<span><?php echo esc_html( $eyebrow ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="obot-why-obot__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $text ) : ?>
					<p class="obot-why-obot__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo nl2br( esc_html( $text ) ); ?></p>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $cards ) : ?>
			<div class="obot-why-obot__grid">
				<?php foreach ( $cards as $index => $card ) : ?>
					<?php
					$card_style = '';
					if ( $card['accent_color'] && $card['accent_rgb'] ) {
						$card_style = sprintf(
							'--why-obot-accent: %s; --why-obot-accent-rgb: %s;',
							$card['accent_color'],
							$card['accent_rgb']
						);
					}
					?>
					<article class="obot-why-obot__card"<?php echo $card_style ? ' style="' . esc_attr( $card_style ) . '"' : ''; ?><?php oboto_the_aos_attributes( 320 + ( $index * 50 ) ); ?>>
						<?php if ( $card['icon_url'] ) : ?>
							<span class="obot-why-obot__icon">
								<img src="<?php echo esc_url( $card['icon_url'] ); ?>" alt="<?php echo esc_attr( $card['icon_alt'] ); ?>">
							</span>
						<?php endif; ?>

						<?php if ( $card['title'] ) : ?>
							<h3 class="obot-why-obot__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( $card['text'] ) : ?>
							<p class="obot-why-obot__card-text"><?php echo nl2br( esc_html( $card['text'] ) ); ?></p>
						<?php endif; ?>

						<?php if ( is_array( $card['link'] ) && ! empty( $card['link']['url'] ) ) : ?>
							<?php
							$link        = $card['link'];
							$link_title  = ! empty( $link['title'] ) ? $link['title'] : __( 'Learn more', 'oboto' );
							$link_target = ! empty( $link['target'] ) ? $link['target'] : '';
							?>
							<a
								class="obot-why-obot__card-link"
								href="<?php echo esc_url( $link['url'] ); ?>"
								<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
								<?php echo '_blank' === $link_target ? 'rel="noopener noreferrer"' : ''; ?>
							>
								<span><?php echo esc_html( $link_title ); ?></span>
								<span class="obot-why-obot__link-arrow" aria-hidden="true"></span>
							</a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-why-obot__placeholder">
				<?php esc_html_e( 'Add cards in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
