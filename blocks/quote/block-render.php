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
$id = 'quote-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'quote';

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
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
            <path d="M26 43V24L38 5H48L39 22H48V43H26ZM0 43V24L11 5H22L13 22H22V43H0Z" fill="white" fill-opacity="0.16" />
        </svg>
        <div class="quote__content" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>><?php the_field('content'); ?></div>
        <?php $button = get_field('button'); ?>
        <?php if ($button) : ?>
            <a <?= get_field('add_animation') == 1 ? $aos_args : ""; ?> class="btn--quote" href="<?php echo esc_url($button['url']); ?>" target="<?php echo esc_attr($button['target']); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                    <path d="M15 0L15.8938 11.3735L21.9708 1.71816L17.4768 12.2043L27.3448 6.47903L18.4923 13.6756L29.8906 13.1919L18.7078 15.4502L29.0252 20.3191L18.0738 17.1217L24.9468 26.2277L16.7357 18.3072L18.5897 29.5641L15 18.735L11.4103 29.5641L13.2643 18.3072L5.05316 26.2277L11.9262 17.1217L0.974756 20.3191L11.2922 15.4502L0.109366 13.1919L11.5077 13.6756L2.65524 6.47903L12.5232 12.2043L8.02915 1.71816L14.1062 11.3735L15 0Z" fill="#4F7DF3" />
                </svg>
                <span><?php echo esc_html($button['title']); ?></span>

            </a>
        <?php endif; ?>
    </section>
<?php endif; ?>