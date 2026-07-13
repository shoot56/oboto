<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'comparison-hero-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-comparison-hero';
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

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-comparison-hero__inner">
		<?php if ( $eyebrow ) : ?>
			<div class="obot-comparison-hero__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
				<span><?php echo esc_html( $eyebrow ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $title ) : ?>
			<h1 class="obot-comparison-hero__title"<?php oboto_the_aos_attributes( 180 ); ?>>
				<?php echo esc_html( $title ); ?>
			</h1>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-comparison-hero__placeholder">
				<?php esc_html_e( 'Add a title in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
