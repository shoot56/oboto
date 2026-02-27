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
$id = 'learning-center-single-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'learning-center-single-block';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $classes
]);

global $post;
$current_post_id = $post->ID;

// Get categories of current post
$categories = wp_get_post_terms($current_post_id, 'learning-center-category');
$category_ids = array();
if ($categories && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
    }
}

?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
        
        <div class="learning-center-single-layout">
            
            <!-- Sidebar with related posts -->
            <aside class="learning-center-single-sidebar">
                <?php
                // Get related posts from same categories
                $related_args = array(
                    'post_type' => 'learning-center',
                    'post_status' => 'publish',
                    'posts_per_page' => 5,
                    'post__not_in' => array($current_post_id),
                );
                
                // If we have categories, filter by them
                if (!empty($category_ids)) {
                    $related_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'learning-center-category',
                            'field' => 'term_id',
                            'terms' => $category_ids,
                        ),
                    );
                }
                
                $related_query = new WP_Query($related_args);
                
                if ($related_query->have_posts()) :
                ?>
                    <h3 class="learning-center-single-sidebar__title">Related Posts</h3>
                    <ul class="learning-center-single__related-list">
                        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                            <li class="learning-center-single__related-item">
                                <a href="<?= get_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <figure class="learning-center-single__related-image">
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        </figure>
                                    <?php endif; ?>
                                    <div class="learning-center-single__related-data">
                                        <h4><?= get_the_title(); ?></h4>
                                        <span class="learning-center-single__related-date">
                                            <?= get_the_date(); ?>
                                        </span>
                                    </div>
                                </a>
                            </li>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </ul>
                <?php endif;
                ?>
            </aside>

            <!-- Main content area -->
            <div class="learning-center-single-content">
                <?php
                // Reset to current post
                global $post;
                setup_postdata($post);
                ?>
                
                <article class="learning-center-single__article">
                    <header class="learning-center-single__header">
                        <h1 class="learning-center-single__title"><?php echo esc_html(get_the_title()); ?></h1>
                        
                        <div class="learning-center-single__meta">
                            <p class="post-meta learning-center-single__date">
                                Published: <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time><br>
                                Last Updated: <time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>"><?php echo esc_html(get_the_modified_date()); ?></time>
                            </p>
                            <?php if ($categories && !is_wp_error($categories) && !empty($categories)) : ?>
                                <span class="learning-center-single__categories">
                                    <?php 
                                    $category_links = array();
                                    foreach ($categories as $category) {
                                        $term_link = get_term_link($category);
                                        if (!is_wp_error($term_link)) {
                                            $category_links[] = '<a href="' . esc_url($term_link) . '">' . esc_html($category->name) . '</a>';
                                        }
                                    }
                                    echo implode(', ', $category_links);
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <ul class="share-links">
                            <li>
                                <a onClick="javascript:open('https://www.linkedin.com/shareArticle?mini=true&url=<?= get_permalink() ?>', '', 'height=500,width=500')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <g clip-path="url(#clip0_12483_1042)">
                                            <path d="M22.2234 0H1.77187C0.792187 0 0 0.773438 0 1.72969V22.2656C0 23.2219 0.792187 24 1.77187 24H22.2234C23.2031 24 24 23.2219 24 22.2703V1.72969C24 0.773438 23.2031 0 22.2234 0ZM7.12031 20.4516H3.55781V8.99531H7.12031V20.4516ZM5.33906 7.43438C4.19531 7.43438 3.27188 6.51094 3.27188 5.37187C3.27188 4.23281 4.19531 3.30937 5.33906 3.30937C6.47813 3.30937 7.40156 4.23281 7.40156 5.37187C7.40156 6.50625 6.47813 7.43438 5.33906 7.43438ZM20.4516 20.4516H16.8937V14.8828C16.8937 13.5562 16.8703 11.8453 15.0422 11.8453C13.1906 11.8453 12.9094 13.2937 12.9094 14.7891V20.4516H9.35625V8.99531H12.7687V10.5609H12.8156C13.2891 9.66094 14.4516 8.70938 16.1813 8.70938C19.7859 8.70938 20.4516 11.0813 20.4516 14.1656V20.4516Z" fill="white" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_12483_1042">
                                                <rect width="24" height="24" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a onClick="javascript:open('https://twitter.com/intent/tweet?text=<?= get_permalink() ?>', '', 'height=500,width=500')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M18.3263 1.90393H21.6998L14.3297 10.3274L23 21.7899H16.2112L10.894 14.838L4.80995 21.7899H1.43443L9.31744 12.78L1 1.90393H7.96111L12.7674 8.25826L18.3263 1.90393ZM17.1423 19.7707H19.0116L6.94539 3.81706H4.93946L17.1423 19.7707Z" fill="white" />
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a onClick="javascript:open('https://www.facebook.com/sharer/sharer.php?u=<?= get_permalink() ?>', '', 'height=500,width=500')">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" height="24" fill="#fff">
                                        <path d="M240 363.3L240 576L356 576L356 363.3L442.5 363.3L460.5 265.5L356 265.5L356 230.9C356 179.2 376.3 159.4 428.7 159.4C445 159.4 458.1 159.8 465.7 160.6L465.7 71.9C451.4 68 416.4 64 396.2 64C289.3 64 240 114.5 240 223.4L240 265.5L174 265.5L174 363.3L240 363.3z" />
                                    </svg>

                                </a>
                            </li>
                        </ul>
                        <?php
                        $article_ld = array(
                            '@context'    => 'https://schema.org',
                            '@type'       => 'Article',
                            'headline'    => get_the_title(),
                            'datePublished' => get_the_date('c'),
                            'dateModified'  => get_the_modified_date('c'),
                            'author'      => array(
                                '@type' => 'Person',
                                'name'  => get_the_author_meta('display_name'),
                            ),
                            'publisher'   => array(
                                '@type' => 'Organization',
                                'name'  => get_bloginfo('name'),
                            ),
                            'mainEntityOfPage' => array(
                                '@type' => 'WebPage',
                                '@id'   => get_permalink(),
                            ),
                        );
                        ?>
                        <script type="application/ld+json"><?php echo wp_json_encode($article_ld); ?></script>
                    </header>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <figure class="learning-center-single__featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </figure>
                    <?php endif; ?>
                    
                    <div class="learning-center-single__content">
                        <?php the_content(); ?>
                    </div>
                </article>
                
            </div>
            
        </div>
        
    </section>
<?php endif; ?>
