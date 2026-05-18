<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-traction-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-traction';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow      = trim( (string) get_field( 'eyebrow' ) );
$cards        = get_field( 'cards' );
$quote_text   = trim( (string) get_field( 'quote_text' ) );
$quote_author = trim( (string) get_field( 'quote_author' ) );

$card_items = array();
if ( is_array( $cards ) ) {
	foreach ( $cards as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$value = isset( $row['value'] ) ? trim( (string) $row['value'] ) : '';
		$text  = isset( $row['text'] ) ? trim( (string) $row['text'] ) : '';

		if ( $value === '' && $text === '' ) {
			continue;
		}

		$card_items[] = array(
			'value' => $value,
			'text'  => $text,
		);
	}
}

?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-traction__inner">
		<div class="obot-landing-traction__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-traction__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-traction__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-traction__eyebrow-pulse"></span>
						<span class="obot-landing-traction__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $card_items ) : ?>
			<ul class="obot-landing-traction__cards" aria-label="<?php esc_attr_e( 'Traction metrics', 'oboto' ); ?>">
				<?php foreach ( $card_items as $index => $card ) : ?>
					<li class="obot-landing-traction__card"<?php oboto_the_aos_attributes( 180 + ( $index * 80 ) ); ?>>
						<?php if ( $card['value'] ) : ?>
							<div class="obot-landing-traction__card-value"><?php echo esc_html( $card['value'] ); ?></div>
						<?php endif; ?>

						<?php if ( $card['text'] ) : ?>
							<div class="obot-landing-traction__card-text"><?php echo esc_html( $card['text'] ); ?></div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-traction__cards-placeholder">
				<?php esc_html_e( 'Add three traction cards in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $quote_text || $quote_author ) : ?>
			<figure class="obot-landing-traction__quote"<?php oboto_the_aos_attributes( 420 ); ?>>
				<div class="obot-landing-traction__quote-icon" aria-hidden="true">
					<svg class="obot-landing-traction__quote-svg" width="28" height="28" viewBox="0 0 24 24" focusable="false">
						<path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path>
						<path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 5v3c0 1 0 1 1 1z"></path>
					</svg>
				</div>

				<?php if ( $quote_text ) : ?>
					<blockquote class="obot-landing-traction__quote-text">
						<?php echo esc_html( $quote_text ); ?>
					</blockquote>
				<?php endif; ?>

				<?php if ( $quote_author ) : ?>
					<figcaption class="obot-landing-traction__quote-author">
						<span class="obot-landing-traction__quote-line" aria-hidden="true"></span>
						<span><?php echo esc_html( $quote_author ); ?></span>
					</figcaption>
				<?php endif; ?>
			</figure>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-traction__quote-placeholder">
				<?php esc_html_e( 'Add quote text and author in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
