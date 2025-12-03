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
$id = 'image-tabs-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'image-tabs';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $classes
]);

if (get_field('add_animation') == 1) {
    $data_aos = get_field('animations');
    $delay = "1000";
    $aos_args = 'data-aos="' . $data_aos . '" delay="' . $delay . '" data-aos-mirror="true" data-aos-once="false" data-aos-easing="ease-in-out"';
}

?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>


        <h2 class="block-title" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>><?= get_field('block_title'); ?></h2>
        <p class="block-description" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>><?= get_field('block_description'); ?></p>
        <div class="tabs__wrapper">
            <?php if (have_rows('tabs')) : ?>
                <ul <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
                    <?php while (have_rows('tabs')) : the_row(); ?>
                        <?php $image = get_sub_field('image'); ?>
                        <li class="tab__title <?= get_row_Index() == 1 ? "active" : ""; ?>" data-image="<?= $image ? esc_url($image['url']) : "#" ?>"><?= get_sub_field('title'); ?></li>
                    <?php endwhile; ?>
                </ul>
                <figure class="tabs__image" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
                    <img src="<?= esc_url(get_field("tabs")[0]["image"]["url"]) ?>" alt="<?= get_field("tabs")[0]["image"]["alt"]; ?>">
                </figure>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>