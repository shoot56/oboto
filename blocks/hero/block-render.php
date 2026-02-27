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
$id = 'hero-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'hero';

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
        <div class="hero__content">
            <h1 <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>><?= get_field('title'); ?></h1>
            <p <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>><?= get_field('description'); ?></p>

            <div class="buttons">
                <?php $button_primary = get_field('button_primary'); ?>
                <?php if ($button_primary) : ?>

                    <a <?= get_field('add_animation') == 1 ? $aos_args : ""; ?> class="btn btn--primary" href="<?php echo esc_url($button_primary['url']); ?>"<?php if ( ! empty( $button_primary['target'] ) ) : ?> target="<?php echo esc_attr( $button_primary['target'] ); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($button_primary['title']); ?></span>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                <?php endif; ?>
                <?php $button_secondary_1 = get_field('button_secondary_1'); ?>
                <?php if ($button_secondary_1) : ?>
                    <a <?= get_field('add_animation') == 1 ? $aos_args : ""; ?> class="btn btn--no-border" href="<?php echo esc_url($button_secondary_1['url']); ?>"<?php if ( ! empty( $button_secondary_1['target'] ) ) : ?> target="<?php echo esc_attr( $button_secondary_1['target'] ); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($button_secondary_1['title']); ?></span>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                <?php endif; ?>

                <?php $button_secondary_2 = get_field('button_secondary_2'); ?>
                <?php if ($button_secondary_2) : ?>
                    <a <?= get_field('add_animation') == 1 ? $aos_args : ""; ?> class="btn btn--no-border" href="<?php echo esc_url($button_secondary_2['url']); ?>"<?php if ( ! empty( $button_secondary_2['target'] ) ) : ?> target="<?php echo esc_attr( $button_secondary_2['target'] ); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($button_secondary_2['title']); ?></span>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <figure class="hero__image" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
            <?php $image = get_field('image'); ?>
            <?php if ($image) : ?>
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
            <?php endif; ?>
        </figure>
    </section>
<?php endif; ?>