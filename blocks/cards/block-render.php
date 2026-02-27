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
$id = 'cards-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'cards';

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
        <?php if (have_rows('cards')) : ?>
            <?php if (get_field('background_image') == 1) : ?>
                <svg width="920" height="210" viewBox="0 0 920 210" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0.5" y="0.5" width="281" height="104" stroke="url(#paint0_linear_12648_2794)" stroke-opacity="0.16" />
                    <mask id="path-3-inside-1_12648_2794" fill="white">
                        <path d="M282 0H466V105H282V0Z" />
                    </mask>
                    <path d="M466 0H467V-1H466V0ZM466 105V106H467V105H466ZM282 0V1H466V0V-1H282V0ZM466 0H465V105H466H467V0H466ZM466 105V104H282V105V106H466V105Z" fill="url(#paint1_linear_12648_2794)" fill-opacity="0.16" mask="url(#path-3-inside-1_12648_2794)" />
                    <mask id="path-5-inside-2_12648_2794" fill="white">
                        <path d="M466 0H650V105H466V0Z" />
                    </mask>
                    <path d="M650 0H651V-1H650V0ZM650 105V106H651V105H650ZM466 0V1H650V0V-1H466V0ZM650 0H649V105H650H651V0H650ZM650 105V104H466V105V106H650V105Z" fill="url(#paint2_linear_12648_2794)" fill-opacity="0.16" mask="url(#path-5-inside-2_12648_2794)" />
                    <mask id="path-7-inside-3_12648_2794" fill="white">
                        <path d="M650 0H834V105H650V0Z" />
                    </mask>
                    <path d="M834 0H835V-1H834V0ZM834 105V106H835V105H834ZM650 0V1H834V0V-1H650V0ZM834 0H833V105H834H835V0H834ZM834 105V104H650V105V106H834V105Z" fill="url(#paint3_linear_12648_2794)" fill-opacity="0.16" mask="url(#path-7-inside-3_12648_2794)" />
                    <mask id="path-9-inside-4_12648_2794" fill="white">
                        <path d="M834 0H1018V105H834V0Z" />
                    </mask>
                    <path d="M1018 0H1019V-1H1018V0ZM1018 105V106H1019V105H1018ZM834 0V1H1018V0V-1H834V0ZM1018 0H1017V105H1018H1019V0H1018ZM1018 105V104H834V105V106H1018V105Z" fill="white" fill-opacity="0.16" mask="url(#path-9-inside-4_12648_2794)" />
                    <mask id="path-11-inside-5_12648_2794" fill="white">
                        <path d="M0 105H195V210H0V105Z" />
                    </mask>
                    <path d="M195 210V211H196V210H195ZM0 210H-1V211H0V210ZM195 105H194V210H195H196V105H195ZM195 210V209H0V210V211H195V210ZM0 210H1V105H0H-1V210H0Z" fill="url(#paint4_linear_12648_2794)" fill-opacity="0.16" mask="url(#path-11-inside-5_12648_2794)" />
                    <mask id="path-13-inside-6_12648_2794" fill="white">
                        <path d="M195 105H379V210H195V105Z" />
                    </mask>
                    <path d="M379 210V211H380V210H379ZM379 105H378V210H379H380V105H379ZM379 210V209H195V210V211H379V210Z" fill="url(#paint5_linear_12648_2794)" fill-opacity="0.2" mask="url(#path-13-inside-6_12648_2794)" />
                    <mask id="path-15-inside-7_12648_2794" fill="white">
                        <path d="M379 105H563V210H379V105Z" />
                    </mask>
                    <path d="M563 210V211H564V210H563ZM563 105H562V210H563H564V105H563ZM563 210V209H379V210V211H563V210Z" fill="url(#paint6_linear_12648_2794)" fill-opacity="0.16" mask="url(#path-15-inside-7_12648_2794)" />
                    <mask id="path-17-inside-8_12648_2794" fill="white">
                        <path d="M563 105H747V210H563V105Z" />
                    </mask>
                    <path d="M747 210V211H748V210H747ZM747 105H746V210H747H748V105H747ZM747 210V209H563V210V211H747V210Z" fill="url(#paint7_linear_12648_2794)" fill-opacity="0.16" mask="url(#path-17-inside-8_12648_2794)" />
                    <mask id="path-19-inside-9_12648_2794" fill="white">
                        <path d="M747 105H931V210H747V105Z" />
                    </mask>
                    <path d="M931 210V211H932V210H931ZM931 105H930V210H931H932V105H931ZM931 210V209H747V210V211H931V210Z" fill="url(#paint8_linear_12648_2794)" fill-opacity="0.16" mask="url(#path-19-inside-9_12648_2794)" />
                    <defs>
                        <linearGradient id="paint0_linear_12648_2794" x1="199" y1="-106" x2="43.3567" y2="58.17" gradientUnits="userSpaceOnUse">
                            <stop offset="0.181492" stop-color="white" stop-opacity="0.5" />
                            <stop offset="0.783471" stop-color="white" stop-opacity="0" />
                        </linearGradient>
                        <linearGradient id="paint1_linear_12648_2794" x1="504.5" y1="-37" x2="132.314" y2="-32.3409" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white" stop-opacity="0.7" />
                            <stop offset="0.872438" stop-color="white" stop-opacity="0.1" />
                        </linearGradient>
                        <linearGradient id="paint2_linear_12648_2794" x1="688.5" y1="-37" x2="316.314" y2="-32.3409" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white" stop-opacity="0.8" />
                            <stop offset="0.872438" stop-color="white" stop-opacity="0.1" />
                        </linearGradient>
                        <linearGradient id="paint3_linear_12648_2794" x1="872.5" y1="-37" x2="500.314" y2="-32.3409" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white" stop-opacity="0.8" />
                            <stop offset="0.872438" stop-color="white" stop-opacity="0.1" />
                        </linearGradient>
                        <linearGradient id="paint4_linear_12648_2794" x1="137.606" y1="-0.999998" x2="-10.8069" y2="107.248" gradientUnits="userSpaceOnUse">
                            <stop offset="0.181492" stop-color="white" stop-opacity="0.5" />
                            <stop offset="0.783471" stop-color="white" stop-opacity="0" />
                        </linearGradient>
                        <linearGradient id="paint5_linear_12648_2794" x1="324.844" y1="-0.999998" x2="179.26" y2="99.1948" gradientUnits="userSpaceOnUse">
                            <stop offset="0.181492" stop-color="white" stop-opacity="0.5" />
                            <stop offset="0.783471" stop-color="white" stop-opacity="0" />
                        </linearGradient>
                        <linearGradient id="paint6_linear_12648_2794" x1="601.5" y1="68" x2="229.314" y2="72.6591" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white" stop-opacity="0.8" />
                            <stop offset="0.872438" stop-color="white" stop-opacity="0.1" />
                        </linearGradient>
                        <linearGradient id="paint7_linear_12648_2794" x1="785.5" y1="68" x2="413.314" y2="72.6591" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white" stop-opacity="0.8" />
                            <stop offset="0.872438" stop-color="white" stop-opacity="0.1" />
                        </linearGradient>
                        <linearGradient id="paint8_linear_12648_2794" x1="969.5" y1="68" x2="597.314" y2="72.6591" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white" stop-opacity="0.8" />
                            <stop offset="0.872438" stop-color="white" stop-opacity="0.1" />
                        </linearGradient>
                    </defs>
                </svg>
            <?php endif; ?>


            <h2 class="block-title align--<?= get_field('title_align'); ?>"><?= get_field('block_title'); ?></h2>
            <div class="cards__list">
                <?php while (have_rows('cards')) : the_row(); ?>
                    <?php $link = get_sub_field('link'); ?>
                    <a class="cards__card" href="<?= $link ? esc_url($link['url']) : "#" ?>">
                        <?php $icon = get_sub_field('icon'); ?>
                        <figure>
                            <?php if ($icon) : ?>
                                <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt']); ?>" />
                            <?php endif; ?>
                        </figure>
                        <div>
                            <h3 class="cards__list-title h5"><?= get_sub_field('title'); ?></h3>
                            <p class="description"><?= get_sub_field('description'); ?></p>
                        </div>

                    </a>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>