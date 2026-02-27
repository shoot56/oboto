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
$id = 'steps-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'steps';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $classes
]);

if (get_field('add_animation') == 1) {
    $data_aos = get_field('animations');
    $delay = "100";
    $aos_args = 'data-aos="' . $data_aos . '" data-aos-delay="' . $delay . '" data-aos-mirror="true" data-aos-once="false" data-aos-easing="ease-in-out"';
}


?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
        <h3 class="title align--<?= get_field('title_align'); ?>"><?= get_field('title'); ?></h3>
        <?php if (have_rows('steps')) : ?>
            <div class="steps__list">
                <?php while (have_rows('steps')) : the_row(); ?>

                    <?php $step = get_sub_field('step'); ?>
                    <?php if ($step) : ?>
                        <a class="step" href="<?php echo esc_url($step['url']); ?>" target="<?php echo esc_attr($step['target']); ?>"><?php echo esc_html($step['title']); ?></a>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>