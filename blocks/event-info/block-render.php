<?php

/**
 * Block template file: block-render.php
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'event-info-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

$items = get_field('items');
$items = is_array($items) ? $items : array();

$font_color = sanitize_hex_color((string) get_field('font_color'));
$border_color = sanitize_hex_color((string) get_field('border_color'));

$style_properties = array();
if ($font_color) {
    $style_properties[] = '--event-info-text-color: ' . $font_color;
    $style_properties[] = '--event-info-label-color: ' . $font_color;
}

if ($border_color) {
    $style_properties[] = '--event-info-border-color: ' . $border_color;
}

$wrapper_args = [
    'class' => 'event-info'
];

if ($style_properties) {
    $wrapper_args['style'] = implode('; ', $style_properties) . ';';
}

$wrapper_attributes = get_block_wrapper_attributes($wrapper_args);

?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__));
    echo '<img src="' . esc_url(get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help']) . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <?php ob_start(); ?>
        <?php foreach ($items as $item) : ?>
            <?php
            $label = isset($item['label']) ? trim((string) $item['label']) : '';
            $item_content = isset($item['content']) ? (string) $item['content'] : '';

            if ($label === '' || trim(wp_strip_all_tags($item_content)) === '') {
                continue;
            }
            ?>
            <dt><?php echo esc_html($label); ?></dt>
            <dd>
                <div class="event-info__content">
                    <?php echo wp_kses_post($item_content); ?>
                </div>
            </dd>
        <?php endforeach; ?>
    <?php $list_content = trim(ob_get_clean()); ?>

    <?php if ($list_content !== '') : ?>
        <div id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
            <dl class="event-info__list">
                <?php echo $list_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </dl>
        </div>
    <?php endif; ?>
<?php endif; ?>
