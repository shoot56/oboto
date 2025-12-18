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
        $current_term_id = 0;
        $current_term_ancestors = array();
        if (is_tax('learning-center-category')) {
            $queried = get_queried_object();
            if (!empty($queried) && !is_wp_error($queried) && !empty($queried->slug)) {
                $current_term_slug = (string) $queried->slug;
                if (!empty($queried->term_id)) {
                    $current_term_id = (int) $queried->term_id;
                    $current_term_ancestors = get_ancestors($current_term_id, 'learning-center-category');
                }
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
                    <?php
                    // Build a children map for hierarchical output.
                    $children_map = array();
                    foreach ($categories as $t) {
                        $parent_id = (int) $t->parent;
                        if (!isset($children_map[$parent_id])) {
                            $children_map[$parent_id] = array();
                        }
                        $children_map[$parent_id][] = $t;
                    }

                    $render_tree = function ($parent_id, $depth) use (&$render_tree, $children_map, $current_term_id, $current_term_ancestors, $current_term_slug) {
                        if (empty($children_map[$parent_id])) {
                            return;
                        }

                        echo '<ul class="learning-center-archive__categories' . ($depth > 0 ? ' learning-center-archive__categories--nested' : '') . '">';

                        foreach ($children_map[$parent_id] as $term) {
                            $term_id = (int) $term->term_id;
                            $has_children = !empty($children_map[$term_id]);

                            $is_active = ($current_term_slug !== '' && $term_id === $current_term_id);
                            $is_open = $is_active || in_array($term_id, $current_term_ancestors, true);

                            echo '<li class="learning-center-archive__term' . ($has_children ? ' has-children' : '') . ($is_open ? ' is-open' : '') . '">';
                            echo '<div class="learning-center-archive__term-row">';

                            // Clickable label (filters)
                            echo '<button type="button" class="learning-center-archive__category' . ($is_active ? ' active' : '') . '" data-category="' . esc_attr($term->slug) . '" data-term-id="' . esc_attr($term_id) . '">';
                            echo esc_html($term->name);
                            echo '</button>';

                            // Toggle caret (expand/collapse)
                            if ($has_children) {
                                echo '<button type="button" class="learning-center-archive__toggle" aria-expanded="' . ($is_open ? 'true' : 'false') . '" aria-label="' . esc_attr__('Toggle subcategories', 'obot') . '"></button>';
                            }

                            echo '</div>';

                            if ($has_children) {
                                $render_tree($term_id, $depth + 1);
                            }

                            echo '</li>';
                        }

                        echo '</ul>';
                    };
                    ?>

                    <div class="learning-center-archive__tree">
                        <button type="button" class="learning-center-archive__category learning-center-archive__category--all <?php echo $current_term_slug === '' ? 'active' : ''; ?>" data-category="all">All</button>
                        <?php $render_tree(0, 0); ?>
                    </div>
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
