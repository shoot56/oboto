<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'resources';
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-product-resources';
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
$cards   = get_field( 'cards' );

$card_items = array();
if ( is_array( $cards ) ) {
	foreach ( $cards as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$card_title = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
		$text       = isset( $row['text'] ) ? trim( (string) $row['text'] ) : '';
		$button     = isset( $row['button'] ) && is_array( $row['button'] ) ? $row['button'] : null;

		if ( $card_title === '' && $text === '' && ( ! $button || empty( $button['url'] ) ) ) {
			continue;
		}

		$card_items[] = array(
			'title'  => $card_title,
			'text'   => $text,
			'button' => $button,
		);
	}
}

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-product-resources__inner">
		<div class="obot-product-resources__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-product-resources__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-product-resources__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</div>

		<?php if ( $card_items ) : ?>
			<div class="obot-product-resources__grid">
				<?php foreach ( $card_items as $index => $card ) : ?>
					<?php
					$button      = $card['button'];
					$has_button  = is_array( $button ) && ! empty( $button['url'] );
					$link_target = $has_button && ! empty( $button['target'] ) ? $button['target'] : '';
					$link_title  = $has_button && ! empty( $button['title'] ) ? $button['title'] : '';
					$card_tag    = $has_button ? 'a' : 'article';
					?>
					<<?php echo esc_html( $card_tag ); ?>
						class="obot-product-resources__card"
						<?php if ( $has_button ) : ?>
							href="<?php echo esc_url( $button['url'] ); ?>"
							<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
							<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						<?php endif; ?>
						<?php oboto_the_aos_attributes( 240 + ( $index * 70 ) ); ?>
					>
						<div class="obot-product-resources__card-body">
							<?php if ( $card['title'] ) : ?>
								<h3 class="obot-product-resources__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
							<?php endif; ?>

							<?php if ( $card['text'] ) : ?>
								<p class="obot-product-resources__card-text"><?php echo esc_html( $card['text'] ); ?></p>
							<?php endif; ?>

							<?php if ( $has_button ) : ?>
								<span class="obot-product-resources__card-cta">
									<span><?php echo esc_html( $link_title ?: $button['url'] ); ?></span>
									<svg class="obot-product-resources__card-arrow" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
										<path d="M5 12h14M13 6l6 6-6 6"></path>
									</svg>
								</span>
							<?php endif; ?>
						</div>
					</<?php echo esc_html( $card_tag ); ?>>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-product-resources__placeholder">
				<?php esc_html_e( 'Add resource cards in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
