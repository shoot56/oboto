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
$id = 'releated-posts-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'releated-posts';

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
        <h3>Related Articles</h3>
        <?php

        $tax_terms = get_the_terms(get_the_ID(),  get_post_taxonomies()[0]);
        $terms_slug = [];



        $args = array(
            'post_type' => get_post_type(),
            'post_status' => array('publish'),
            'posts_per_page' => 3,
            'order' => 'DESC',
            "orderby" => "date",
            "post__not_in" => [get_the_ID()]
        );


        if ($tax_terms) {
            foreach ($tax_terms as &$terms) {
                array_push($terms_slug, $terms->slug);
            }

            $args["tax_query"][] =   array(
                'taxonomy' => get_post_taxonomies()[0],
                'field' => 'slug',
                'terms' =>  $terms_slug,
            );
        };

        $the_query = new WP_Query($args);
        ?>

        <?php if ($the_query->have_posts()) : ?>
            <div class="releated-posts-list">
                <?php
                while ($the_query->have_posts()) :
                    $the_query->the_post();
                ?>


                    <?php $post_ids = get_the_ID(); ?>
                    <a class="releated-posts_item" href="<?= get_permalink($post_ids); ?>">
                        <figure class="releated-posts_image">
                            <?php $url = wp_get_attachment_url(get_post_thumbnail_id($post_ids), 'thumbnail'); ?>
                            <?php if ($url) : ?>
                                <img src="<?php echo $url ?>" />
                            <?php endif; ?>
                        </figure>
                        <div class="releated-posts_data">
                            <h3><?= get_the_title(); ?></h3>
                            <p class="releated-posts_description">
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
        <?php endif; ?>


    </section>
<?php endif; ?>