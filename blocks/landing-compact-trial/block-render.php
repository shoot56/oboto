<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-compact-trial-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-compact-trial';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
		'id'    => $id,
	)
);

$eyebrow = trim( (string) get_field( 'eyebrow' ) );
$title   = trim( (string) get_field( 'title' ) );
$text    = trim( (string) get_field( 'text' ) );

$eyebrow = $eyebrow !== '' ? $eyebrow : __( '14-day free trial', 'oboto' );
$title   = $title !== '' ? $title : __( 'Start your 14-day free trial', 'oboto' );
$text    = $text !== '' ? $text : __( 'Launch a dedicated Obot Cloud environment with every feature available from day one.', 'oboto' );

$benefits = array();
$rows     = get_field( 'benefits' );
if ( is_array( $rows ) ) {
	foreach ( $rows as $row ) {
		$benefit = is_array( $row ) && isset( $row['text'] ) ? trim( (string) $row['text'] ) : '';
		if ( $benefit !== '' ) {
			$benefits[] = $benefit;
		}
	}
}

if ( empty( $benefits ) ) {
	$benefits = array(
		__( 'Full access to every Obot Cloud feature for 14 days', 'oboto' ),
		__( 'One OAuth setup across every MCP server you connect', 'oboto' ),
		__( 'Full audit trail and governance out of the box', 'oboto' ),
	);
}

$button_type   = (string) get_field( 'button_type' );
$button        = get_field( 'button' );
$popup_embed   = trim( (string) get_field( 'button_popup_embed' ) );
$button_url    = '';
$button_title  = '';
$button_target = '';

if ( ! in_array( $button_type, array( 'link', 'popup' ), true ) ) {
	$button_type = 'link';
}

if ( is_array( $button ) && ! empty( $button['url'] ) ) {
	$button_url    = $button['url'];
	$button_title  = ! empty( $button['title'] ) ? trim( (string) $button['title'] ) : '';
	$button_target = ! empty( $button['target'] ) ? $button['target'] : '';
}

$allowed_popup_embed_tags = array(
	'div'    => array(
		'class'                                  => true,
		'id'                                     => true,
		'data-fillout-id'                        => true,
		'data-fillout-embed-type'                => true,
		'data-fillout-button-text'               => true,
		'data-fillout-dynamic-resize'            => true,
		'data-fillout-inherit-parameters'        => true,
		'data-fillout-domain'                    => true,
		'data-fillout-popup-size'                => true,
		'data-landing-compact-trial-button'      => true,
		'data-landing-compact-trial-button-type' => true,
	),
	'script' => array(
		'async'   => true,
		'charset' => true,
		'defer'   => true,
		'src'     => true,
		'type'    => true,
	),
);

$prepare_popup_embed = static function ( $embed ) {
	if ( $embed === '' || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $embed;
	}

	$processor = new WP_HTML_Tag_Processor( $embed );

	while ( $processor->next_tag( array( 'tag_name' => 'div' ) ) ) {
		if ( ! $processor->get_attribute( 'data-fillout-id' ) ) {
			continue;
		}

		$processor->add_class( 'obot-landing-compact-trial__fillout-trigger' );
		$processor->set_attribute( 'data-landing-compact-trial-button', '' );
		$processor->set_attribute( 'data-landing-compact-trial-button-type', 'popup' );
		break;
	}

	return $processor->get_updated_html();
};

$get_popup_button_text = static function ( $embed ) {
	if ( $embed === '' || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return '';
	}

	$processor = new WP_HTML_Tag_Processor( $embed );

	while ( $processor->next_tag( array( 'tag_name' => 'div' ) ) ) {
		if ( ! $processor->get_attribute( 'data-fillout-id' ) ) {
			continue;
		}

		return trim( (string) $processor->get_attribute( 'data-fillout-button-text' ) );
	}

	return '';
};

$popup_button_text = $get_popup_button_text( $popup_embed );
$popup_embed       = $prepare_popup_embed( $popup_embed );

$has_copy             = $eyebrow !== '' || $title !== '' || $text !== '';
$has_link_button      = $button_type === 'link' && $button_url !== '' && $button_title !== '';
$has_popup_button     = $button_type === 'popup' && $popup_embed !== '' && $popup_button_text !== '';
$has_button           = $has_link_button || $has_popup_button;
$has_content          = $has_copy || ! empty( $benefits ) || $has_button;
$mobile_sticky_value   = get_field( 'enable_mobile_sticky' );
$mobile_sticky_enabled = $has_button && ( $mobile_sticky_value === null || $mobile_sticky_value === '' ? true : (bool) $mobile_sticky_value );

if ( ! $has_content ) {
	return;
}

?>
<section
	<?php echo $wrapper_attributes; ?>
	data-landing-compact-trial
	data-mobile-sticky-enabled="<?php echo $mobile_sticky_enabled ? 'true' : 'false'; ?>"
>
	<div class="obot-landing-compact-trial__inner">
		<?php if ( $has_copy ) : ?>
			<div class="obot-landing-compact-trial__copy">
				<?php if ( $eyebrow !== '' ) : ?>
					<div class="obot-landing-compact-trial__eyebrow">
						<span class="obot-landing-compact-trial__eyebrow-dot" aria-hidden="true"></span>
						<span><?php echo esc_html( $eyebrow ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title !== '' ) : ?>
					<h2 class="obot-landing-compact-trial__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $text !== '' ) : ?>
					<p class="obot-landing-compact-trial__text"><?php echo esc_html( $text ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $benefits ) ) : ?>
			<ul class="obot-landing-compact-trial__benefits" aria-label="<?php esc_attr_e( 'Trial benefits', 'oboto' ); ?>">
				<?php foreach ( $benefits as $benefit ) : ?>
					<li class="obot-landing-compact-trial__benefit">
						<span class="obot-landing-compact-trial__check" aria-hidden="true"></span>
						<span><?php echo esc_html( $benefit ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $has_button ) : ?>
			<div class="obot-landing-compact-trial__action">
				<?php if ( $has_popup_button ) : ?>
					<div class="obot-landing-compact-trial__popup-embed">
						<?php if ( $is_preview ) : ?>
							<button class="obot-landing-compact-trial__button" type="button">
								<span><?php echo esc_html( $popup_button_text ); ?></span>
								<span class="obot-landing-compact-trial__button-arrow" aria-hidden="true"></span>
							</button>
						<?php else : ?>
							<?php echo wp_kses( $popup_embed, $allowed_popup_embed_tags ); ?>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<a
						class="obot-landing-compact-trial__button"
						href="<?php echo esc_url( $button_url ); ?>"
						<?php echo $button_target ? 'target="' . esc_attr( $button_target ) . '"' : ''; ?>
						<?php echo $button_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						data-landing-compact-trial-button
						data-landing-compact-trial-button-type="link"
					>
						<span><?php echo esc_html( $button_title ); ?></span>
						<span class="obot-landing-compact-trial__button-arrow" aria-hidden="true"></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
