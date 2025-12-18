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
$id = 'learning-center-archive-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'learning-center-archive-block';

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
        <?php
        // If we are on a taxonomy archive, pre-select that term.
        $current_term_slug = '';
        if (is_tax('learning-center-category')) {
            $queried = get_queried_object();
            if (!empty($queried) && !is_wp_error($queried) && !empty($queried->slug)) {
                $current_term_slug = (string) $queried->slug;
            }
        }
        ?>
        <div class="learning-center-archive-block__header">
            <h1>Learning Center</h1>
        </div>
        <div class="learning-center-archive-layout">
            
            <!-- Sidebar with category filter -->
            <aside class="learning-center-archive-sidebar">
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'learning-center-category',
                    'hide_empty' => false,
                ));
                ?>
                
                <?php if ($categories && !is_wp_error($categories) && !empty($categories)) : ?>
                    <h3 class="learning-center-archive-sidebar__title">Categories</h3>
                    <ul class="learning-center-archive__categories">
                        <li class="learning-center-archive__category <?php echo $current_term_slug === '' ? 'active' : ''; ?>" data-category="all">All</li>
                        <?php foreach ($categories as $category) : ?>
                            <li class="learning-center-archive__category <?php echo $current_term_slug === $category->slug ? 'active' : ''; ?>" data-category="<?= esc_attr($category->slug); ?>">
                                <?= esc_html($category->name); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </aside>

            <!-- Main content area -->
            <div class="learning-center-archive-content">
                <?php
                $args = array(
                    'post_type' => 'learning-center',
                    'post_status' => array('publish'),
                    'posts_per_page' => 12,
                );
                if ($current_term_slug !== '') {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'learning-center-category',
                            'field'    => 'slug',
                            'terms'    => $current_term_slug,
                        ),
                    );
                }
                $the_query = new WP_Query($args);
                ?>

                <?php if ($the_query->have_posts()) : ?>
                    <div class="learning-center-archive__list">
                        <?php
                        while ($the_query->have_posts()) :
                            $the_query->the_post();
                            $post_id = get_the_ID();
                        ?>
                            <a class="learning-center-archive__item" href="<?= get_permalink($post_id); ?>">
                                <figure class="learning-center-archive__image">
                                    <?php $url = wp_get_attachment_url(get_post_thumbnail_id($post_id), 'thumbnail'); ?>
                                    <?php if ($url) : ?>
                                        <img src="<?php echo esc_url($url) ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                    <?php endif; ?>
                                </figure>
                                <div class="learning-center-archive__data">
                                    <h3><?= get_the_title(); ?></h3>
                                    <p class="learning-center-archive__description">
                                        <?= get_the_excerpt($post_id); ?>
                                    </p>
                                    <div class="btn btn--no-border">
                                        <span>Read More</span>
                                        <span class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                    
                    <?php if ($the_query->max_num_pages > 1) : ?>
                        <button class="learning-center-archive__load-more btn btn--outline" data-max-page="<?= $the_query->max_num_pages ?>" data-current-page="1" data-current-category="<?= esc_attr($current_term_slug !== '' ? $current_term_slug : 'all'); ?>">
                            <span>Load more</span>
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="learning-center-archive__list">
                        <p><?php esc_html_e('Sorry, no posts matched your criteria.'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
        
    </section>
<?php endif; ?>
