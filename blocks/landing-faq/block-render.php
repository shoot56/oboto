<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-faq-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-faq';
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
$faqs    = get_field( 'faqs' );

$faq_items = array();
if ( is_array( $faqs ) ) {
	foreach ( $faqs as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$question = isset( $row['question'] ) ? trim( (string) $row['question'] ) : '';
		$answer   = isset( $row['answer'] ) ? trim( (string) $row['answer'] ) : '';

		if ( $question === '' || $answer === '' ) {
			continue;
		}

		$faq_items[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}
}

?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-faq__inner">
		<div class="obot-landing-faq__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-faq__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-faq__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-faq__eyebrow-pulse"></span>
						<span class="obot-landing-faq__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-landing-faq__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</div>

		<?php if ( $faq_items ) : ?>
			<div class="obot-landing-faq__list" data-landing-faq-list>
				<?php foreach ( $faq_items as $index => $item ) : ?>
					<?php
					$button_id = $id . '-question-' . ( $index + 1 );
					$panel_id  = $id . '-answer-' . ( $index + 1 );
					?>
					<div class="obot-landing-faq__item" data-landing-faq-item data-open="false"<?php oboto_the_aos_attributes( 260 + ( $index * 70 ) ); ?>>
						<button
							id="<?php echo esc_attr( $button_id ); ?>"
							class="obot-landing-faq__question"
							type="button"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
							data-landing-faq-trigger
						>
							<span class="obot-landing-faq__question-text"><?php echo esc_html( $item['question'] ); ?></span>
							<span class="obot-landing-faq__icon" aria-hidden="true"></span>
						</button>

						<div
							id="<?php echo esc_attr( $panel_id ); ?>"
							class="obot-landing-faq__answer"
							role="region"
							aria-labelledby="<?php echo esc_attr( $button_id ); ?>"
							aria-hidden="true"
							inert
							data-landing-faq-panel
						>
							<div class="obot-landing-faq__answer-inner">
								<?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-faq__placeholder">
				<?php esc_html_e( 'Add FAQ rows in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
