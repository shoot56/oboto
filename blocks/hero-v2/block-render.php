<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'hero-v2-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-hero-v2';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow          = trim( (string) get_field( 'eyebrow' ) );
$title            = trim( (string) get_field( 'title' ) );
$text             = trim( (string) get_field( 'text' ) );
$primary_button   = get_field( 'primary_button' );
$secondary_button = get_field( 'secondary_button' );

$button_items = array();
foreach (
	array(
		'primary'   => $primary_button,
		'secondary' => $secondary_button,
	) as $variant => $button
) {
	if ( ! is_array( $button ) || empty( $button['url'] ) ) {
		continue;
	}

	$button_items[] = array(
		'button'  => $button,
		'variant' => $variant,
	);
}

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?> data-hero-v2>
	<div class="obot-hero-v2__rain" data-hero-v2-rain aria-hidden="true">
		<div class="obot-hero-v2__rain-lines" data-hero-v2-rain-lines></div>
	</div>

	<div class="obot-hero-v2__inner">
		<?php if ( $eyebrow ) : ?>
			<div class="obot-hero-v2__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
				<span class="obot-hero-v2__eyebrow-dot" aria-hidden="true"></span>
				<span><?php echo esc_html( $eyebrow ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $title ) : ?>
			<h1 class="obot-hero-v2__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo nl2br( esc_html( $title ) ); ?></h1>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-hero-v2__placeholder">
				<?php esc_html_e( 'Add a title in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<p class="obot-hero-v2__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo nl2br( esc_html( $text ) ); ?></p>
		<?php endif; ?>

		<?php if ( $button_items ) : ?>
			<div class="obot-hero-v2__actions"<?php oboto_the_aos_attributes( 340 ); ?>>
				<?php foreach ( $button_items as $item ) : ?>
					<?php
					$button       = $item['button'];
					$link_target  = ! empty( $button['target'] ) ? $button['target'] : '';
					$link_title   = ! empty( $button['title'] ) ? $button['title'] : $button['url'];
					$button_class = 'obot-hero-v2__button obot-hero-v2__button--' . $item['variant'];
					?>
					<a
						class="<?php echo esc_attr( $button_class ); ?>"
						href="<?php echo esc_url( $button['url'] ); ?>"
						<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
						<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
					>
						<span><?php echo esc_html( $link_title ); ?></span>
						<?php if ( $item['variant'] === 'primary' ) : ?>
							<span class="obot-hero-v2__button-arrow" aria-hidden="true"></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
