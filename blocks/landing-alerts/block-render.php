<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-alerts-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-alerts';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow     = trim( (string) get_field( 'eyebrow' ) );
$heading     = trim( (string) get_field( 'heading' ) );
$description = trim( (string) get_field( 'description' ) );
$bottom_text = trim( (string) get_field( 'bottom_text' ) );
$alerts      = get_field( 'alerts' );

$alert_items = array();
if ( is_array( $alerts ) ) {
	foreach ( $alerts as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$alert_name  = isset( $row['alert_name'] ) ? trim( (string) $row['alert_name'] ) : '';
		$criticality = isset( $row['criticality'] ) ? trim( (string) $row['criticality'] ) : '';
		$title       = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
		$text        = isset( $row['text'] ) ? trim( (string) $row['text'] ) : '';

		if ( $alert_name === '' && $criticality === '' && $title === '' && $text === '' ) {
			continue;
		}

		$alert_items[] = array(
			'alert_name'  => $alert_name,
			'criticality' => $criticality,
			'title'       => $title,
			'text'        => $text,
			'class'       => $criticality ? ' obot-landing-alerts__alert--' . sanitize_html_class( sanitize_title( $criticality ) ) : '',
		);
	}
}

$alert_count = count( $alert_items );

$allowed_bottom_text_tags = array(
	'span' => array(),
);
?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-alerts__inner">
		<div class="obot-landing-alerts__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-alerts__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-alerts__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-alerts__eyebrow-pulse"></span>
						<span class="obot-landing-alerts__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 class="obot-landing-alerts__heading"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="obot-landing-alerts__description"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<div class="obot-landing-alerts__monitor"<?php oboto_the_aos_attributes( 340 ); ?>>
			<div class="obot-landing-alerts__monitor-bar">
				<span class="obot-landing-alerts__window-dot obot-landing-alerts__window-dot--red" aria-hidden="true"></span>
				<span class="obot-landing-alerts__window-dot obot-landing-alerts__window-dot--yellow" aria-hidden="true"></span>
				<span class="obot-landing-alerts__window-dot obot-landing-alerts__window-dot--green" aria-hidden="true"></span>
				<span class="obot-landing-alerts__terminal-title">obot-secops ~ threat-monitor.live</span>
				<span class="obot-landing-alerts__live">
					<span class="obot-landing-alerts__live-dot" aria-hidden="true"></span>
					<span>LIVE &middot; <?php echo esc_html( (string) $alert_count ); ?> ACTIVE</span>
				</span>
			</div>

			<div class="obot-landing-alerts__scan" aria-hidden="true">
				<span class="obot-landing-alerts__scan-sweep"></span>
			</div>

			<?php if ( $alert_items ) : ?>
				<ul class="obot-landing-alerts__list" aria-label="<?php esc_attr_e( 'Active alerts', 'oboto' ); ?>">
					<?php foreach ( $alert_items as $index => $alert ) : ?>
						<li class="obot-landing-alerts__alert<?php echo esc_attr( $alert['class'] ); ?>"<?php oboto_the_aos_attributes( 420 + ( $index * 80 ) ); ?>>
							<div class="obot-landing-alerts__alert-meta">
								<span class="obot-landing-alerts__alert-dot" aria-hidden="true"></span>
								<?php if ( $alert['alert_name'] ) : ?>
									<span class="obot-landing-alerts__alert-name"><?php echo esc_html( $alert['alert_name'] ); ?></span>
								<?php endif; ?>
								<?php if ( $alert['criticality'] ) : ?>
									<span class="obot-landing-alerts__criticality"><?php echo esc_html( $alert['criticality'] ); ?></span>
								<?php endif; ?>
							</div>
							<div class="obot-landing-alerts__alert-content">
								<?php if ( $alert['title'] ) : ?>
									<h3 class="obot-landing-alerts__alert-title"><?php echo esc_html( $alert['title'] ); ?></h3>
								<?php endif; ?>
								<?php if ( $alert['text'] ) : ?>
									<p class="obot-landing-alerts__alert-text"><?php echo esc_html( $alert['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php elseif ( $is_preview ) : ?>
				<div class="obot-landing-alerts__placeholder">
					<?php esc_html_e( 'Add alert rows in the block fields.', 'oboto' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $bottom_text !== '' ) : ?>
			<p class="obot-landing-alerts__bottom-text"<?php oboto_the_aos_attributes( 520 ); ?>><?php echo wp_kses( $bottom_text, $allowed_bottom_text_tags ); ?></p>
		<?php endif; ?>
	</div>
</section>
