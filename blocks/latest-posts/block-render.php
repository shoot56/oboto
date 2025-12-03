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
$id = 'latest-posts-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'latest-posts';

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

        <div class="posts__header" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
            <h3 class="section-title"><?= get_field('title'); ?></h3>
        </div>



        <div class="posts_list" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
            <?php $posts = get_field('posts'); ?>
            <?php if ($posts) : ?>
                <div class="swiper articleSwiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($posts as $post_ids) : ?>

                            <a href="<?= get_permalink($post_ids); ?>" class="post_item swiper-slide" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
                                <figure class="post_image">
                                    <?php $url = wp_get_attachment_url(get_post_thumbnail_id($post_ids), 'thumbnail'); ?>
                                    <img src="<?php echo $url ?>" />
                                </figure>
                                <div class="post_data">
                                    <h3><?= get_the_title(); ?></h3>
                                    <p class="post_description">
                                        <?= get_the_excerpt($post_ids); ?>
                                    </p>
                                    <p class="btn btn--no-border">
                                        <span>Read More</span>
                                        <span class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </p>
                                </div>
                            </a>

                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else : ?>
                <?php
                $args = array(
                    'post_type' => "post",
                    'post_status' => array('publish'),
                    'posts_per_page' => -1,
                    'order' => 'DESC',
                    'orderby' => 'date'
                );
                $the_query = new WP_Query($args);
                ?>
                <?php if ($the_query->have_posts()) : ?>
                    <div class="swiper articleSwiper">
                        <div class="swiper-wrapper">
                            <?php
                            while ($the_query->have_posts()) :
                                $the_query->the_post();
                                $post_ids = get_the_ID();
                            ?>
                                <a href="<?= get_permalink($post_ids); ?>" class="post_item swiper-slide" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
                                    <figure class="post_image">
                                        <?php $url = wp_get_attachment_url(get_post_thumbnail_id($post_ids), 'thumbnail'); ?>
                                        <?php if ($url) : ?>
                                            <img src="<?php echo $url ?>" />
                                        <?php endif; ?>
                                    </figure>
                                    <div class="post_data">
                                        <h3><?= get_the_title(); ?></h3>
                                        <p class="post_description">
                                            <?= get_the_excerpt($post_ids); ?>
                                        </p>
                                        <p class="btn btn--no-border">
                                            <span>Read More</span>
                                            <span class="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </p>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                        <!-- <div class="article-swiper-pagination"></div> -->
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php $read_all = get_field('read_all'); ?>
        <?php if ($read_all) : ?>
            <a href="<?php echo esc_url($read_all['url']); ?>" target="<?php echo esc_attr($read_all['target']); ?>" class="btn btn--outline">
                <span><?php echo esc_html($read_all['title']); ?></span>
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </a>
        <?php endif; ?>
    </section>
<?php endif; ?>