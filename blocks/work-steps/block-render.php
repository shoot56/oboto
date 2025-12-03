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
$id = 'work-steps-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'work-steps';

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
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
        <?php if (have_rows('steps')) : ?>
            <div class="steps">
                <?php while (have_rows('steps')) : the_row(); ?>
                    <div class="step">
                        <div class="title">
                            <p><?= get_row_Index() > 9 ? get_row_Index() : "0" . get_row_Index() ?></p>
                            <h4><?= get_sub_field('title'); ?></h4>
                        </div>
                        <div class="description">
                            <?= get_sub_field('description'); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>