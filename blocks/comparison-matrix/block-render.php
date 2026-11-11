<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'comparison-matrix-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'obot-comparison-matrix',
	)
);

$eyebrow     = trim( (string) get_field( 'eyebrow' ) );
$title       = trim( (string) get_field( 'title' ) );
$text        = trim( (string) get_field( 'text' ) );
$bottom_text = trim( (string) get_field( 'bottom_text' ) );
$rows        = get_field( 'rows' );

$minimum_column_count = 2;
$maximum_column_count = 10;
$column_count         = absint( get_field( 'column_count' ) );
$column_count         = min( $maximum_column_count, max( $minimum_column_count, $column_count ) );

$column_headings = array();
for ( $column_index = 1; $column_index <= $column_count; $column_index++ ) {
	$heading = trim( (string) get_field( 'column_' . $column_index . '_heading' ) );

	if ( $heading === '' ) {
		$heading = sprintf(
			/* translators: %d: table column number. */
			__( 'Column %d', 'oboto' ),
			$column_index
		);
	}

	$column_headings[] = $heading;
}

$allowed_statuses = array( 'neutral', 'yes', 'no' );
$normalize_status = static function ( $status ) use ( $allowed_statuses ) {
	$status = sanitize_key( (string) $status );

	return in_array( $status, $allowed_statuses, true ) ? $status : 'neutral';
};

$matrix_rows = array();
if ( is_array( $rows ) ) {
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$cells       = array();
		$has_content = false;

		for ( $column_index = 1; $column_index <= $column_count; $column_index++ ) {
			$cell_data = $row[ 'cell_' . $column_index ] ?? array();
			$cell_data = is_array( $cell_data ) ? $cell_data : array();
			$cell_text = trim( (string) ( $cell_data['text'] ?? '' ) );
			$status    = $normalize_status( $cell_data['status'] ?? 'neutral' );

			if ( $cell_text !== '' || $status !== 'neutral' ) {
				$has_content = true;
			}

			$cells[] = array(
				'text'   => $cell_text,
				'status' => $status,
			);
		}

		if ( ! $has_content ) {
			continue;
		}

		$matrix_rows[] = array(
			'highlighted' => ! empty( $row['highlighted'] ),
			'cells'       => $cells,
		);
	}
}

$render_cell_value = static function ( $cell ) {
	$status       = $cell['status'];
	$status_label = '';
	$status_icon  = '';

	if ( $status === 'yes' ) {
		$status_label = __( 'Yes', 'oboto' );
		$status_icon  = '✓';
	} elseif ( $status === 'no' ) {
		$status_label = __( 'No', 'oboto' );
		$status_icon  = '✕';
	}
	?>
	<span class="obot-comparison-matrix__value obot-comparison-matrix__value--<?php echo esc_attr( $status ); ?>">
		<?php if ( $status_icon !== '' ) : ?>
			<span class="obot-comparison-matrix__status-icon" aria-hidden="true"><?php echo esc_html( $status_icon ); ?></span>
			<span class="screen-reader-text"><?php echo esc_html( $status_label . ': ' ); ?></span>
		<?php endif; ?>
		<?php if ( $cell['text'] !== '' ) : ?>
			<span class="obot-comparison-matrix__cell-text"><?php echo nl2br( esc_html( $cell['text'] ) ); ?></span>
		<?php endif; ?>
	</span>
	<?php
};

$first_column_width   = 190;
$other_column_width   = 170;
$table_minimum_width  = $first_column_width + ( ( $column_count - 1 ) * $other_column_width );
$table_labelledby     = $title !== '' ? $id . '-title' : '';
$table_accessible_name = $title !== '' ? $title : __( 'Comparison matrix', 'oboto' );

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<?php if ( $eyebrow !== '' || $title !== '' || $text !== '' ) : ?>
		<header class="obot-comparison-matrix__header">
			<?php if ( $eyebrow !== '' ) : ?>
				<p class="obot-comparison-matrix__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $title !== '' ) : ?>
				<h2 id="<?php echo esc_attr( $id . '-title' ); ?>" class="obot-comparison-matrix__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<?php if ( $text !== '' ) : ?>
				<div class="obot-comparison-matrix__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<?php if ( $matrix_rows ) : ?>
		<div
			class="obot-comparison-matrix__table-wrap"
			role="region"
			tabindex="0"
			<?php if ( $table_labelledby !== '' ) : ?>
				aria-labelledby="<?php echo esc_attr( $table_labelledby ); ?>"
			<?php else : ?>
				aria-label="<?php echo esc_attr( $table_accessible_name ); ?>"
			<?php endif; ?>
		>
			<table class="obot-comparison-matrix__table" style="min-width: <?php echo esc_attr( (string) $table_minimum_width ); ?>px;">
				<caption class="screen-reader-text"><?php echo esc_html( $table_accessible_name ); ?></caption>
				<colgroup>
					<col class="obot-comparison-matrix__first-column">
					<?php if ( $column_count > 1 ) : ?>
						<col span="<?php echo esc_attr( (string) ( $column_count - 1 ) ); ?>">
					<?php endif; ?>
				</colgroup>
				<thead>
					<tr>
						<?php foreach ( $column_headings as $heading ) : ?>
							<th scope="col"><?php echo esc_html( $heading ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $matrix_rows as $matrix_row ) : ?>
						<tr class="<?php echo $matrix_row['highlighted'] ? 'obot-comparison-matrix__row--highlighted' : ''; ?>">
							<?php foreach ( $matrix_row['cells'] as $cell_index => $cell ) : ?>
								<?php if ( $cell_index === 0 ) : ?>
									<th scope="row">
										<?php $render_cell_value( $cell ); ?>
									</th>
								<?php else : ?>
									<td>
										<?php $render_cell_value( $cell ); ?>
									</td>
								<?php endif; ?>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php if ( $bottom_text !== '' ) : ?>
			<div class="obot-comparison-matrix__bottom-text"><?php echo wp_kses_post( wpautop( $bottom_text ) ); ?></div>
		<?php endif; ?>
	<?php elseif ( $is_preview ) : ?>
		<div class="obot-comparison-matrix__placeholder">
			<?php esc_html_e( 'Add comparison rows in the block fields.', 'oboto' ); ?>
		</div>
	<?php endif; ?>
</section>
