<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'product-hero-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-product-hero';
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
$text    = trim( (string) get_field( 'text' ) );
$buttons = get_field( 'buttons' );

$allowed_title_tags = array(
	'br'   => array(),
	'span' => array(
		'class' => true,
	),
);

$button_items = array();
if ( is_array( $buttons ) ) {
	foreach ( $buttons as $index => $row ) {
		if ( ! is_array( $row ) || empty( $row['button'] ) || ! is_array( $row['button'] ) || empty( $row['button']['url'] ) ) {
			continue;
		}

		$variant = isset( $row['variant'] ) ? sanitize_key( (string) $row['variant'] ) : '';
		if ( ! in_array( $variant, array( 'primary', 'secondary' ), true ) ) {
			$variant = count( $button_items ) === 0 ? 'primary' : 'secondary';
		}

		$button_items[] = array(
			'button'  => $row['button'],
			'variant' => $variant,
		);
	}
}

$has_heading = trim( wp_strip_all_tags( $title ) ) !== '';

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?> data-product-hero>
	<div class="obot-product-hero__grid" aria-hidden="true"></div>

	<div class="obot-product-hero__inner">
		<div class="obot-product-hero__copy">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-product-hero__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-product-hero__eyebrow-status" aria-hidden="true">
						<span class="obot-product-hero__eyebrow-pulse"></span>
						<span class="obot-product-hero__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $has_heading ) : ?>
				<h1 class="obot-product-hero__title"<?php oboto_the_aos_attributes( 180 ); ?>>
					<?php echo wp_kses( $title, $allowed_title_tags ); ?>
				</h1>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="obot-product-hero__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>

			<?php if ( $button_items ) : ?>
				<div class="obot-product-hero__actions"<?php oboto_the_aos_attributes( 340 ); ?>>
					<?php foreach ( $button_items as $item ) : ?>
						<?php
						$button      = $item['button'];
						$link_target = ! empty( $button['target'] ) ? $button['target'] : '';
						$link_title  = ! empty( $button['title'] ) ? $button['title'] : $button['url'];
						?>
						<a
							class="obot-product-hero__button obot-product-hero__button--<?php echo esc_attr( $item['variant'] ); ?>"
							href="<?php echo esc_url( $button['url'] ); ?>"
							<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
							<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						>
							<span><?php echo esc_html( $link_title ); ?></span>
							<?php if ( $item['variant'] === 'primary' ) : ?>
								<span class="obot-product-hero__button-arrow" aria-hidden="true"></span>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="obot-product-hero__visual"<?php oboto_the_aos_attributes( 260 ); ?>>
			<div class="obot-product-hero__diagram" aria-hidden="true">
				<canvas class="obot-product-hero__canvas" data-product-hero-diagram></canvas>
			</div>
		</div>
	</div>
</section>
