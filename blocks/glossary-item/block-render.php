<?php

/**
 * Block template file: block-render.php
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (may contain InnerBlocks).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'glossary-item-' . $block['id'];
if (!empty($block['anchor'])) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'glossary-item';
$is_editor = is_admin() || $is_preview;
if ($is_editor) {
	$classes .= ' glossary-item--editor is-open';
}
$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes
]);

$title = (string) get_field('title');
$button_id = $id . '-button';
$panel_id = $id . '-panel';
$expanded = $is_editor ? 'true' : 'false';
$hidden = $is_editor ? 'false' : 'true';

?>

<section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
	<button
		id="<?php echo esc_attr($button_id); ?>"
		class="glossary-item__trigger"
		type="button"
		aria-expanded="<?php echo esc_attr($expanded); ?>"
		aria-controls="<?php echo esc_attr($panel_id); ?>"
	>
		<span class="glossary-item__title"><?php echo esc_html($title); ?></span>

		<span class="glossary-item__icon" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" class="glossary-item__icon-plus">
				<path d="M7.99728 1C8.301 1 8.55205 1.22562 8.59184 1.51848L8.59733 1.5999L8.59829 7.40027H14.4001C14.7314 7.40027 15 7.66891 15 8.00029C15 8.30406 14.7743 8.55511 14.4815 8.59484L14.4001 8.60032H8.59829L8.59993 14.3999C8.6 14.7312 8.33145 15 8.00012 15C7.6964 15 7.44535 14.7744 7.40557 14.4815L7.40007 14.4001L7.39844 8.60032H1.59993C1.2686 8.60032 1 8.33168 1 8.00029C1 7.69652 1.2257 7.44548 1.51852 7.40575L1.59993 7.40027H7.39844L7.39747 1.60015C7.3974 1.26876 7.66595 1 7.99728 1Z" fill="white" />
			</svg>
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" class="glossary-item__icon-minus">
				<path d="M13.8516 7.45215H8.54711H7.4501H2.1486L2.07417 7.45716C1.80645 7.49348 1.6001 7.72301 1.6001 8.00074C1.6001 8.30372 1.84567 8.54934 2.1486 8.54934H7.4501H8.54711H13.8516L13.926 8.54433C14.1937 8.508 14.4001 8.27848 14.4001 8.00074C14.4001 7.69776 14.1545 7.45215 13.8516 7.45215Z" fill="white" />
			</svg>
		</span>
	</button>

	<div
		id="<?php echo esc_attr($panel_id); ?>"
		class="glossary-item__panel"
		role="region"
		aria-labelledby="<?php echo esc_attr($button_id); ?>"
		aria-hidden="<?php echo esc_attr($hidden); ?>"
	>
		<div class="glossary-item__panel-inner">
			<div class="glossary-item__content">
				<?php if (!empty(trim((string) $content))) : ?>
					<?php echo $content; ?>
				<?php else : ?>
					<InnerBlocks />
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

