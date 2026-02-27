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
$id = 'media-text-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'media-text';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' =>  $classes
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

        <div class="media-text__overlay overlay--<?= get_field('overlay_position'); ?>" style="background-color: <?= get_field('overlay_color'); ?>;max-width: <?= get_field('overlay_width'); ?>px;max-height: <?= get_field('overlay_height'); ?>px"></div>
        <div class="media-text__wrap media--<?= get_field('media_posistion'); ?>">
            <h2 class="media-text__title" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="22" viewBox="0 0 25 22" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 0.036377C11.7441 0.036377 11.1218 0.658634 11.1218 1.4144C11.1232 1.91667 11.4 2.38142 11.8418 2.62106V5.84287H7.13974C5.39005 5.84287 3.85739 6.78014 3.01703 8.1754C2.95584 8.16526 2.89432 8.15816 2.8328 8.15242C1.55299 8.15242 0.5 9.20495 0.5 10.485C0.5 11.7646 1.55299 12.8175 2.8328 12.8175C2.84767 12.8165 2.86221 12.8151 2.87708 12.8141C3.67992 14.3415 5.29067 15.3873 7.13974 15.3873H17.8603C19.7097 15.3873 21.3204 14.3415 22.1233 12.8128C22.1381 12.8138 22.1527 12.8161 22.1672 12.8175C23.447 12.8175 24.5 11.765 24.5 10.485C24.5 9.20528 23.447 8.15242 22.1672 8.15242C22.1053 8.15816 22.0442 8.16492 21.983 8.1754C21.1426 6.78014 19.6099 5.84185 17.8603 5.84185H13.1578V2.62106C13.5996 2.38006 13.8765 1.91667 13.8778 1.4144C13.8778 0.658634 13.2559 0.036377 12.5 0.036377ZM7.13974 7.35339H9.15884C9.15681 7.38178 9.15444 7.41051 9.15444 7.44025C9.15444 8.12166 9.70341 8.67091 10.3856 8.67091H14.6141C15.2962 8.67091 15.8452 8.12166 15.8452 7.44025C15.8452 7.41051 15.8429 7.38212 15.8408 7.35339H17.8599C19.7016 7.35339 21.1548 8.79766 21.1548 10.6151C21.1548 12.4315 19.7016 13.8768 17.8599 13.8768H7.13974C5.29811 13.8768 3.84488 12.4315 3.84488 10.6151C3.84488 8.79766 5.29811 7.35339 7.13974 7.35339ZM6.91663 8.99742C6.02894 8.99742 5.29878 9.72682 5.29878 10.6151C5.29878 11.5023 6.02894 12.2327 6.91663 12.2327C7.80432 12.2327 8.53448 11.502 8.53448 10.6151C8.53448 9.72682 7.80398 8.99742 6.91663 8.99742ZM18.0834 8.99742C17.1957 8.99742 16.4655 9.72682 16.4655 10.6151C16.4655 11.5023 17.1957 12.2327 18.0834 12.2327C18.9711 12.2327 19.7012 11.502 19.7012 10.6151C19.7012 9.72682 18.9707 8.99742 18.0834 8.99742ZM10.3193 9.9668C10.3193 11.1711 11.2956 12.1476 12.5 12.1476C13.7044 12.1476 14.6807 11.1714 14.6807 9.9668H10.3193ZM6.8115 16.5405C6.75674 16.8569 6.7253 17.1811 6.7253 17.5133V19.106C5.5584 19.5329 4.72412 20.6496 4.72412 21.9637H10.8152C10.8152 20.6347 9.96167 19.5078 8.77415 19.0924V17.5136C8.77415 17.1804 8.81809 16.8596 8.89821 16.5534H7.13974C7.0292 16.5531 6.92001 16.5483 6.8115 16.5405ZM18.1885 16.5405C18.08 16.5487 17.9708 16.5531 17.8603 16.5531H16.1018C16.1819 16.8589 16.2259 17.1797 16.2259 17.5133V19.0921C15.038 19.5075 14.1848 20.6344 14.1848 21.9634H20.2762C20.2762 20.6493 19.4419 19.5329 18.275 19.107V17.5133C18.2747 17.1811 18.2433 16.8566 18.1885 16.5405Z" fill="white" />
                    </svg>
                </span>
                <span><?= get_field('title'); ?></span>

            </h2>
            <div class="media-text__content" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
                <?= get_field('content'); ?>
            </div>

            <?php $button = get_field('button'); ?>
            <?php if ($button) : ?>
                <a class="btn btn--outline" href="<?php echo esc_url($button['url']); ?>" target="<?php echo esc_attr($button['target']); ?>">
                    <span>

                        <span><?php echo esc_html($button['title']); ?></span>

                    </span>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                </a>
            <?php endif; ?>

            <?php $media = get_field('media'); ?>
            <?php if ($media) : ?>
                <figure <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
                    <img src="<?php echo esc_url($media['url']); ?>" alt="<?php echo esc_attr($media['alt']); ?>" />
                </figure>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>