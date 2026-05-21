<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-final-cta-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-final-cta';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow            = trim( (string) get_field( 'eyebrow' ) );
$title              = trim( (string) get_field( 'title' ) );
$text               = trim( (string) get_field( 'text' ) );
$list               = get_field( 'list' );
$form_embed         = trim( (string) get_field( 'hubspot_form_embed' ) );
$buttons            = get_field( 'buttons' );

$list_items = array();
if ( is_array( $list ) ) {
	foreach ( $list as $row ) {
		if ( ! is_array( $row ) || empty( $row['text'] ) ) {
			continue;
		}

		$item_text = trim( (string) $row['text'] );
		if ( $item_text !== '' ) {
			$list_items[] = $item_text;
		}
	}
}

$has_list_column = ! empty( $list_items ) || $is_preview;
$has_form_card   = $form_embed !== '' || $is_preview;

$button_items = array();
if ( is_array( $buttons ) ) {
	foreach ( $buttons as $row ) {
		if ( ! is_array( $row ) || empty( $row['button'] ) || ! is_array( $row['button'] ) || empty( $row['button']['url'] ) ) {
			continue;
		}

		$button_items[] = $row['button'];
	}
}

$allowed_form_embed_tags = array(
	'script' => array(
		'async'   => true,
		'charset' => true,
		'class'   => true,
		'defer'   => true,
		'id'      => true,
		'src'     => true,
		'type'    => true,
	),
	'div'    => array(
		'class'                           => true,
		'id'                              => true,
		'style'                           => true,
		'data-fillout-id'                 => true,
		'data-fillout-embed-type'         => true,
		'data-fillout-button-text'        => true,
		'data-fillout-dynamic-resize'     => true,
		'data-fillout-inherit-parameters' => true,
		'data-fillout-domain'             => true,
		'data-fillout-popup-size'         => true,
	),
);

$hubspot_target_id = 'obot-' . sanitize_title( $id . '-hubspot-form' );
$is_hubspot_embed  = strpos( $form_embed, 'hbspt.forms.create' ) !== false;
$is_fillout_embed  = strpos( $form_embed, 'data-fillout-id' ) !== false;
$hubspot_form_css  = '
:root {
	--hsf-global__font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
	--hsf-global__font-size: 16px;
	--hsf-global__color: rgba(255,255,255,0.78);
	--hsf-background__background-color: transparent;
	--hsf-background__border-radius: 8px;
	--hsf-background__padding: 0;
	--hsf-field-label__color: rgba(255,255,255,0.82);
	--hsf-field-label-requiredindicator__color: #4f7df3;
	--hsf-field-input__color: #fff;
	--hsf-field-input__background-color: rgba(255,255,255,0.05);
	--hsf-field-input__placeholder-color: rgba(255,255,255,0.38);
	--hsf-field-input__border-color: rgba(255,255,255,0.16);
	--hsf-field-input__border-width: 1px;
	--hsf-field-input__border-style: solid;
	--hsf-field-input__border-radius: 8px;
	--hsf-field-input__padding: 13px 16px;
	--hsf-field-textarea__color: #fff;
	--hsf-field-textarea__background-color: rgba(255,255,255,0.05);
	--hsf-field-textarea__border-color: rgba(255,255,255,0.16);
	--hsf-field-textarea__border-width: 1px;
	--hsf-field-textarea__border-style: solid;
	--hsf-field-textarea__border-radius: 8px;
	--hsf-field-textarea__padding: 13px 16px;
	--hsf-button__color: #fff;
	--hsf-button__background-color: #4f7df3;
	--hsf-button__border-radius: 999px;
	--hsf-button__padding: 16px 24px;
	--hsf-button__box-shadow: 0 8px 32px -4px rgba(79,125,243,0.5);
	--hsf-richtext__color: rgba(255,255,255,0.72);
	--hsf-erroralert__color: #fca5a5;
}
form,
.hs-form {
	margin: 0;
	background: transparent;
	color: rgba(255,255,255,0.78);
	font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
	color-scheme: dark;
}
.hbspt-form,
.obot-hubspot-raw-form {
	width: 100%;
	background: transparent;
}
fieldset {
	max-width: none !important;
	margin: 0 !important;
	padding: 0 !important;
	border: 0 !important;
}
.hs-form-field {
	margin: 0 0 18px;
}
.form-columns-2 {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 16px;
}
.form-columns-2 .hs-form-field {
	width: auto !important;
	float: none !important;
}
.form-columns-1 .hs-input:not([type="checkbox"]):not([type="radio"]),
.form-columns-2 .hs-input:not([type="checkbox"]):not([type="radio"]),
.form-columns-3 .hs-input:not([type="checkbox"]):not([type="radio"]) {
	width: 100% !important;
}
.input {
	margin-right: 0 !important;
}
label,
.hs-form-field > label {
	display: block;
	margin: 0 0 7px;
	color: rgba(255,255,255,0.82);
	font-size: 14px;
	font-weight: 500;
	line-height: 1.4;
}
.hs-input:not([type="checkbox"]):not([type="radio"]),
input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]),
select,
textarea {
	box-sizing: border-box;
	width: 100% !important;
	min-height: 52px;
	padding: 13px 16px;
	border: 1px solid rgba(255,255,255,0.16);
	border-radius: 8px;
	background: rgba(255,255,255,0.05);
	color: #fff;
	font-size: 15px;
	line-height: 1.4;
	outline: none;
	box-shadow: inset 0 1px 0 rgba(255,255,255,0.03);
	color-scheme: dark;
}
select {
	appearance: auto;
	background-color: #0f1730;
	color: #fff;
}
select option,
select optgroup {
	background-color: #0f1730;
	color: #fff;
}
select option:checked,
select option:hover {
	background-color: #4f7df3;
	color: #fff;
}
textarea.hs-input,
textarea {
	min-height: 104px;
	resize: vertical;
}
.hs-input::placeholder,
input::placeholder,
textarea::placeholder {
	color: rgba(255,255,255,0.38);
	opacity: 1;
}
.hs-input:focus,
input:focus,
select:focus,
textarea:focus {
	border-color: rgba(79,125,243,0.65);
	box-shadow: 0 0 0 1px rgba(79,125,243,0.28), inset 0 1px 0 rgba(255,255,255,0.03);
}
.legal-consent-container,
.legal-consent-container p,
.hs-richtext,
.hs-dependent-field,
.inputs-list label {
	color: rgba(255,255,255,0.68);
	font-size: 13px;
	line-height: 1.65;
}
.legal-consent-container {
	margin-top: 8px;
	padding-top: 18px;
	border-top: 1px solid rgba(255,255,255,0.1);
}
.legal-consent-container a,
.hs-richtext a {
	color: #8facff;
}
.inputs-list {
	margin: 0;
	padding: 0;
	list-style: none;
}
input[type="checkbox"],
input[type="radio"] {
	width: auto !important;
	min-height: 0;
	margin: 0 8px 0 0;
}
.hs-error-msgs,
.hs-error-msgs label {
	margin: 6px 0 0;
	padding: 0;
	color: #fca5a5;
	font-size: 13px;
}
.grecaptcha-badge {
	overflow: hidden;
	border-radius: 8px;
}
.hs-submit,
.actions {
	margin-top: 22px;
}
input[type="submit"],
.hs-button {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-height: 56px;
	padding: 16px 26px;
	border: 0;
	border-radius: 999px;
	background: #4f7df3;
	color: #fff;
	font-size: 16px;
	font-weight: 700;
	line-height: 1.2;
	cursor: pointer;
	box-shadow: 0 8px 32px -4px rgba(79,125,243,0.5);
	transition: transform 200ms ease, background-color 200ms ease, box-shadow 200ms ease;
}
input[type="submit"]:hover,
.hs-button:hover {
	transform: translateY(-1px);
	background: #6b91f5;
	box-shadow: 0 12px 42px -6px rgba(79,125,243,0.6);
}
@media (max-width: 640px) {
	.form-columns-2 {
		grid-template-columns: 1fr;
	}
}
';

$enhance_hubspot_embed = static function ( $embed, $target_id, $form_css ) {
	if ( $embed === '' || strpos( $embed, 'hbspt.forms.create' ) === false ) {
		return $embed;
	}

	$options = sprintf(
		"\n    target: %s,\n    cssRequired: \"\",\n    cssClass: \"obot-hubspot-raw-form\",\n    css: %s,\n    ",
		wp_json_encode( '#' . $target_id ),
		wp_json_encode( trim( $form_css ) )
	);

	return preg_replace( '/hbspt\\.forms\\.create\\s*\\(\\s*\\{/', 'hbspt.forms.create({' . $options, $embed, 1 );
};

$form_embed = $enhance_hubspot_embed( $form_embed, $hubspot_target_id, $hubspot_form_css );
?>

<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-final-cta__glow obot-landing-final-cta__glow--top" aria-hidden="true"></div>
	<div class="obot-landing-final-cta__glow obot-landing-final-cta__glow--bottom" aria-hidden="true"></div>

	<div class="obot-landing-final-cta__inner">
		<div class="obot-landing-final-cta__header">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-landing-final-cta__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-landing-final-cta__eyebrow-status" aria-hidden="true">
						<span class="obot-landing-final-cta__eyebrow-pulse"></span>
						<span class="obot-landing-final-cta__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<h2 class="obot-landing-final-cta__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="obot-landing-final-cta__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $has_list_column || $has_form_card ) : ?>
			<div class="obot-landing-final-cta__body<?php echo ! $has_form_card || ! $has_list_column ? ' obot-landing-final-cta__body--single' : ''; ?>">
				<?php if ( $has_list_column ) : ?>
					<div class="obot-landing-final-cta__list-column">
						<?php if ( $list_items ) : ?>
							<ul class="obot-landing-final-cta__list" aria-label="<?php esc_attr_e( 'Demo benefits', 'oboto' ); ?>">
								<?php foreach ( $list_items as $index => $item ) : ?>
									<li class="obot-landing-final-cta__list-item"<?php oboto_the_aos_attributes( 340 + ( $index * 70 ) ); ?>>
										<span class="obot-landing-final-cta__check" aria-hidden="true"></span>
										<span><?php echo esc_html( $item ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php elseif ( $is_preview ) : ?>
							<div class="obot-landing-final-cta__placeholder">
								<?php esc_html_e( 'Add checklist rows in the block fields.', 'oboto' ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $has_form_card ) : ?>
					<div class="obot-landing-final-cta__form-card<?php echo $is_fillout_embed ? ' obot-landing-final-cta__form-card--fillout' : ''; ?>"<?php oboto_the_aos_attributes( 420 ); ?>>
						<?php if ( $form_embed !== '' ) : ?>
							<div class="obot-landing-final-cta__form-embed">
								<?php if ( $is_preview ) : ?>
									<div class="obot-landing-final-cta__form-preview"><?php esc_html_e( 'Form embed will render on the front end.', 'oboto' ); ?></div>
								<?php else : ?>
									<?php if ( $is_hubspot_embed ) : ?>
										<div id="<?php echo esc_attr( $hubspot_target_id ); ?>" class="obot-landing-final-cta__hubspot-target"></div>
									<?php endif; ?>
									<?php echo wp_kses( $form_embed, $allowed_form_embed_tags ); ?>
								<?php endif; ?>
							</div>
						<?php elseif ( $is_preview ) : ?>
							<div class="obot-landing-final-cta__form-preview"><?php esc_html_e( 'Paste the HubSpot or Fillout form embed code in the block fields.', 'oboto' ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $button_items ) : ?>
			<div class="obot-landing-final-cta__actions">
				<?php foreach ( $button_items as $index => $button ) : ?>
					<?php
					$link_target = ! empty( $button['target'] ) ? $button['target'] : '';
					$link_title  = ! empty( $button['title'] ) ? $button['title'] : $button['url'];
					?>
					<a
						class="obot-landing-final-cta__button"
						href="<?php echo esc_url( $button['url'] ); ?>"
						<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
						<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						<?php oboto_the_aos_attributes( 520 + ( $index * 70 ) ); ?>
					>
						<span><?php echo esc_html( $link_title ); ?></span>
						<span class="obot-landing-final-cta__button-arrow" aria-hidden="true"></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-landing-final-cta__actions-placeholder">
				<?php esc_html_e( 'Add bottom buttons in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
