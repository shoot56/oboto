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

        <div class="posts__header" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
            <h3 class="section-title"><?= get_field('title'); ?></h3>
        </div>



        <div class="posts_list" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
            <?php 
            $posts = get_field('posts');
            $categories = get_field('categories');
            $exclude_categories = get_field('exclude_categories');
            
            // Priority 1: If specific posts are selected, show them (excluding posts from excluded categories)
            if ($posts && !empty($posts)) : 
                // Filter out posts that belong to excluded categories
                if ($exclude_categories && !empty($exclude_categories)) {
                    $filtered_posts = array();
                    foreach ($posts as $post_id) {
                        $post_categories = wp_get_post_categories($post_id);
                        $has_excluded = false;
                        foreach ($exclude_categories as $excluded_cat) {
                            if (in_array($excluded_cat, $post_categories)) {
                                $has_excluded = true;
                                break;
                            }
                        }
                        if (!$has_excluded) {
                            $filtered_posts[] = $post_id;
                        }
                    }
                    $posts = $filtered_posts;
                }
            ?>
                <div class="swiper articleSwiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($posts as $post_ids) : ?>
                            <a href="<?= get_permalink($post_ids); ?>" class="post_item swiper-slide" <?= get_field('add_animation') == 1 ? $aos_args : ""; ?>>
                                <figure class="post_image">
                                    <?php $url = wp_get_attachment_url(get_post_thumbnail_id($post_ids), 'thumbnail'); ?>
                                    <?php if ($url) : ?>
                                        <img src="<?php echo $url ?>" />
                                    <?php endif; ?>
                                </figure>
                                <div class="post_data">
                                    <h3><?= get_the_title($post_ids); ?></h3>
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
            <?php 
            // Priority 2: If categories are selected, show posts from those categories (excluding excluded categories)
            elseif ($categories && !empty($categories)) : ?>
                <?php
                $tax_query = array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $categories,
                    ),
                );
                
                // Add exclusion if exclude_categories is set
                if ($exclude_categories && !empty($exclude_categories)) {
                    $tax_query[] = array(
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $exclude_categories,
                        'operator' => 'NOT IN',
                    );
                }
                
                $args = array(
                    'post_type' => "post",
                    'post_status' => array('publish'),
                    'posts_per_page' => -1,
                    'order' => 'DESC',
                    'orderby' => 'date',
                    'tax_query' => $tax_query,
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
                <?php wp_reset_postdata(); ?>
            <?php 
            // Priority 3: Otherwise, show all posts (excluding excluded categories if set)
            else : ?>
                <?php
                $args = array(
                    'post_type' => "post",
                    'post_status' => array('publish'),
                    'posts_per_page' => -1,
                    'order' => 'DESC',
                    'orderby' => 'date'
                );
                
                // Add exclusion if exclude_categories is set
                if ($exclude_categories && !empty($exclude_categories)) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'category',
                            'field' => 'term_id',
                            'terms' => $exclude_categories,
                            'operator' => 'NOT IN',
                        ),
                    );
                }
                
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
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>

        <?php $read_all = get_field('read_all'); ?>
        <?php if ($read_all) : ?>
            <a href="<?php echo esc_url($read_all['url']); ?>"<?php if ( ! empty( $read_all['target'] ) ) : ?> target="<?php echo esc_attr( $read_all['target'] ); ?>"<?php endif; ?> class="btn btn--outline">
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