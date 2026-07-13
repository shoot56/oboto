<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'comparison-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-comparison';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$title                  = trim( (string) get_field( 'title' ) );
$feature_column_heading = trim( (string) get_field( 'feature_column_heading' ) );
$first_column_heading   = trim( (string) get_field( 'first_column_heading' ) );
$second_column_heading  = trim( (string) get_field( 'second_column_heading' ) );
$comparisons            = get_field( 'comparisons' );

if ( $feature_column_heading === '' ) {
	$feature_column_heading = __( 'Feature', 'oboto' );
}

$comparison_items = array();
if ( is_array( $comparisons ) ) {
	foreach ( $comparisons as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$feature            = isset( $row['feature'] ) ? trim( (string) $row['feature'] ) : '';
		$first_column_text  = isset( $row['first_column_text'] ) ? trim( (string) $row['first_column_text'] ) : '';
		$second_column_text = isset( $row['second_column_text'] ) ? trim( (string) $row['second_column_text'] ) : '';

		if ( $feature === '' && $first_column_text === '' && $second_column_text === '' ) {
			continue;
		}

		$comparison_items[] = array(
			'feature'            => $feature,
			'first_column_text'  => $first_column_text,
			'second_column_text' => $second_column_text,
		);
	}
}

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-comparison__inner">
		<?php if ( $title ) : ?>
			<div class="obot-comparison__header">
				<h3 class="obot-comparison__title"<?php oboto_the_aos_attributes( 100 ); ?>><?php echo esc_html( $title ); ?></h3>
			</div>
		<?php endif; ?>

		<?php if ( $comparison_items ) : ?>
			<div class="obot-comparison__table-wrap"<?php oboto_the_aos_attributes( 180 ); ?>>
				<table class="obot-comparison__table">
					<colgroup>
						<col class="obot-comparison__feature-col">
						<col class="obot-comparison__primary-col">
						<col class="obot-comparison__secondary-col">
					</colgroup>
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html( $feature_column_heading ); ?></th>
							<th scope="col" class="obot-comparison__primary-heading"><?php echo esc_html( $first_column_heading ); ?></th>
							<th scope="col"><?php echo esc_html( $second_column_heading ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $comparison_items as $item ) : ?>
							<tr>
								<th scope="row" class="obot-comparison__feature">
									<?php echo esc_html( $item['feature'] ); ?>
								</th>
								<td class="obot-comparison__primary" data-label="<?php echo esc_attr( $first_column_heading ); ?>">
									<?php echo wp_kses_post( wpautop( $item['first_column_text'] ) ); ?>
								</td>
								<td data-label="<?php echo esc_attr( $second_column_heading ); ?>">
									<?php echo wp_kses_post( wpautop( $item['second_column_text'] ) ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-comparison__placeholder">
				<?php esc_html_e( 'Add comparison rows in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
