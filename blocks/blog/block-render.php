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
$id = 'blog-block-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'blog-block';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $classes
]);

$post_slug = "post";
$taxonomy_slug = "category";

?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?> post-type-slug="<?= $post_slug; ?>" taxonomy-slug="<?= $taxonomy_slug; ?>">

        <?php if (get_field('taxonomy_filter') == 1 || get_field('taxonomy_filter') == null) : ?>

            <?php
            $taxonomy  = $taxonomy_slug;
            $tax_terms = get_terms($taxonomy, array('hide_empty' => false));
            ?>

            <ul class="blog_categories">

                <?php
                foreach ($tax_terms as &$tax) {
                ?>

                    <?php if ($tax->term_id != 1 &&  $tax->slug != "learning-center") : ?>
                        <li data-slug="<?= $tax->slug; ?>"><?= $tax->name; ?></li>
                    <?php endif; ?>
                <?php
                }
                ?>

            </ul>


        <?php endif; ?>


        <?php
        $args = array(
            'post_type' => $post_slug,
            'post_status' => array('publish'),
            'posts_per_page' => 12,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category', // Replace with your taxonomy name if different
                    'field'    => 'slug',
                    'terms'    => array('learning-center'),
                    'operator' => 'NOT IN',
                ),
            ),
        );
        $the_query = new WP_Query($args);
        ?>

        <?php if ($the_query->have_posts()) : ?>
            <div class="blog_list">


                <?php
                while ($the_query->have_posts()) :
                    $the_query->the_post();
                ?>

                    <?php $post_ids = get_the_ID(); ?>


                    <a class="blog_item" href="<?= get_permalink($post_ids); ?>">
                        <figure class="blog_image">
                            <?php $url = wp_get_attachment_url(get_post_thumbnail_id($post_ids), 'thumbnail'); ?>
                            <?php if ($url) : ?>
                                <img src="<?php echo $url ?>" />
                            <?php endif; ?>
                        </figure>
                        <div class="blog_data">
                            <h3><?= get_the_title(); ?></h3>
                            <p class="blog_description">
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


                <?php wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p><?php esc_html_e('Sorry, no posts matched your criteria.'); ?></p>

        <?php endif; ?>
        <button class="load-more btn  btn--outline" data-max-page=<?= $the_query->max_num_pages ?>>
            <span>Load more</span>
            <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </button>
    </section>
<?php endif; ?>