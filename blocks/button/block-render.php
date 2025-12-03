<?php

/**
 * Block template file: block-render.php
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'btn-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'btn btn--' . get_field('style');

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $classes
]);


?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <?php $link = get_field('link'); ?>
    <?php if ($link) : ?>
        <a id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?> href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target']); ?>">
            <span class="text-wrap">
                <?php echo esc_html($link['title']); ?>
            </span>

            <?php $icon = get_field('icon'); ?>
            <?php if ($icon) : ?>
                <span class="icon">
                    <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt']); ?>" />
                </span>

            <?php endif; ?>

        </a>
    <?php endif; ?>
<?php endif; ?>