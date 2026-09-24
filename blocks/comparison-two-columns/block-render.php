<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'comparison-two-columns-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-comparison-two-columns';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$columns = array();
foreach ( array( 'first', 'second' ) as $column_key ) {
	$logo     = get_field( $column_key . '_column_logo' );
	$logo_id  = is_array( $logo ) ? absint( $logo['ID'] ?? $logo['id'] ?? 0 ) : absint( $logo );
	$title    = trim( (string) get_field( $column_key . '_column_title' ) );
	$text     = trim( (string) get_field( $column_key . '_column_text' ) );

	if ( ! $logo_id && '' === $title && '' === $text ) {
		continue;
	}

	$columns[] = array(
		'logo_id' => $logo_id,
		'title'   => $title,
		'text'    => $text,
	);
}

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-comparison-two-columns__inner">
		<?php if ( $columns ) : ?>
			<div class="obot-comparison-two-columns__grid">
				<?php foreach ( $columns as $index => $column ) : ?>
					<article class="obot-comparison-two-columns__card"<?php oboto_the_aos_attributes( 100 + ( $index * 100 ) ); ?>>
						<?php if ( $column['logo_id'] || $column['title'] ) : ?>
							<div class="obot-comparison-two-columns__heading">
								<?php if ( $column['logo_id'] ) : ?>
									<?php
									echo wp_get_attachment_image(
										$column['logo_id'],
										'full',
										false,
										array(
											'class'    => 'obot-comparison-two-columns__logo',
											'decoding' => 'async',
										)
									);
									?>
								<?php endif; ?>

								<?php if ( $column['title'] ) : ?>
									<h2 class="obot-comparison-two-columns__title"><?php echo esc_html( $column['title'] ); ?></h2>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $column['text'] ) : ?>
							<p class="obot-comparison-two-columns__text"><?php echo nl2br( esc_html( $column['text'] ) ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-comparison-two-columns__placeholder">
				<?php esc_html_e( 'Add content for one or both comparison columns.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
