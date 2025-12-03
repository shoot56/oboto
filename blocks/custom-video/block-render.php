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
$id = 'custom-video-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'custom-video';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' =>  $classes
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
        <h3 <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>><?= the_field('title'); ?></h3>
        <div class="custom-video__wrap" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
            <?php
            $poster = get_field('poster');
            $posterUrl = $poster ? 'poster=' . $poster['url'] : "";
            ?>

            <?php $video = get_field('video'); ?>
            <?php if ($video) : ?>
                <video <?= $posterUrl; ?> <?= !$poster ? "controls" : "" ?> style="max-width:<?= get_field('width'); ?>px">
                    <source src="<?php echo esc_url($video['url']); ?>" type="<?php echo esc_html($video['mime_type']); ?>">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>
            <?php if ($poster) : ?>
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="play-btn">
                    <rect width="80" height="80" rx="40" fill="white" />
                    <path d="M50.5 39.134C51.1667 39.5189 51.1667 40.4811 50.5 40.866L35.5 49.5263C34.8333 49.9112 34 49.4301 34 48.6603V31.3397C34 30.5699 34.8333 30.0888 35.5 30.4737L50.5 39.134Z" fill="#4F7DF3" />
                </svg>

            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>