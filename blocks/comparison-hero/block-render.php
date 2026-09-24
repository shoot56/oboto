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

$enable_gradient_background = (bool) get_field( 'enable_gradient_background' );
if ( $enable_gradient_background ) {
	$wrapper_classes .= ' obot-comparison-hero--has-gradient';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow             = trim( (string) get_field( 'eyebrow' ) );
$first_service_logo  = get_field( 'first_service_logo' );
$first_service_name  = trim( (string) get_field( 'first_service_name' ) );
$second_service_logo = get_field( 'second_service_logo' );
$second_service_name = trim( (string) get_field( 'second_service_name' ) );
$title               = trim( (string) get_field( 'title' ) );
$text                = trim( (string) get_field( 'text' ) );
$buttons             = get_field( 'buttons' );

$service_items = array();
foreach (
	array(
		array(
			'logo' => $first_service_logo,
			'name' => $first_service_name,
		),
		array(
			'logo' => $second_service_logo,
			'name' => $second_service_name,
		),
	) as $service
) {
	$logo    = $service['logo'];
	$logo_id = is_array( $logo ) ? absint( $logo['ID'] ?? $logo['id'] ?? 0 ) : absint( $logo );
	if ( ! $logo_id && '' === $service['name'] ) {
		continue;
	}

	$service_items[] = array(
		'logo_id' => $logo_id,
		'name'    => $service['name'],
	);
}

$button_items = array();
if ( is_array( $buttons ) ) {
	foreach ( $buttons as $row ) {
		if ( ! is_array( $row ) || empty( $row['button'] ) || ! is_array( $row['button'] ) || empty( $row['button']['url'] ) ) {
			continue;
		}

		$variant = isset( $row['variant'] ) ? sanitize_key( (string) $row['variant'] ) : '';
		if ( ! in_array( $variant, array( 'primary', 'secondary' ), true ) ) {
			$variant = 0 === count( $button_items ) ? 'primary' : 'secondary';
		}

		$button_items[] = array(
			'button'  => $row['button'],
			'variant' => $variant,
		);
	}
}

$allowed_title_tags = array(
	'br' => array(),
);
$has_title = '' !== trim( wp_strip_all_tags( $title ) );

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-comparison-hero__inner">
		<?php if ( $eyebrow ) : ?>
			<div class="obot-comparison-hero__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
				<span class="obot-comparison-hero__eyebrow-dot" aria-hidden="true"></span>
				<span><?php echo esc_html( $eyebrow ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $service_items ) : ?>
			<div class="obot-comparison-hero__services"<?php oboto_the_aos_attributes( 180 ); ?>>
				<?php foreach ( $service_items as $index => $service ) : ?>
					<?php if ( $index > 0 ) : ?>
						<span class="obot-comparison-hero__versus">VS</span>
					<?php endif; ?>

					<div class="obot-comparison-hero__service">
						<?php if ( $service['logo_id'] ) : ?>
							<?php
							$image_attributes = array(
								'class'    => 'obot-comparison-hero__service-logo',
								'decoding' => 'async',
							);
							if ( $service['name'] ) {
								$image_attributes['alt'] = $service['name'];
							}

							echo wp_get_attachment_image( $service['logo_id'], 'full', false, $image_attributes );
							?>
						<?php else : ?>
							<span class="obot-comparison-hero__service-name"><?php echo esc_html( $service['name'] ); ?></span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $has_title ) : ?>
			<h1 class="obot-comparison-hero__title"<?php oboto_the_aos_attributes( 260 ); ?>>
				<?php echo wp_kses( nl2br( $title ), $allowed_title_tags ); ?>
			</h1>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-comparison-hero__placeholder">
				<?php esc_html_e( 'Add a title in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<p class="obot-comparison-hero__text"<?php oboto_the_aos_attributes( 340 ); ?>><?php echo nl2br( esc_html( $text ) ); ?></p>
		<?php endif; ?>

		<?php if ( $button_items ) : ?>
			<div class="obot-comparison-hero__actions"<?php oboto_the_aos_attributes( 420 ); ?>>
				<?php foreach ( $button_items as $item ) : ?>
					<?php
					$button      = $item['button'];
					$link_target = ! empty( $button['target'] ) ? $button['target'] : '';
					$link_title  = ! empty( $button['title'] ) ? $button['title'] : $button['url'];
					?>
					<a
						class="obot-comparison-hero__button obot-comparison-hero__button--<?php echo esc_attr( $item['variant'] ); ?>"
						href="<?php echo esc_url( $button['url'] ); ?>"
						<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
						<?php echo '_blank' === $link_target ? 'rel="noopener noreferrer"' : ''; ?>
					>
						<span><?php echo esc_html( $link_title ); ?></span>
						<span class="obot-comparison-hero__button-arrow" aria-hidden="true">↗</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
