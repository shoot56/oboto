<?php

/**
 * Enqueue scripts and styles for block.
 */
function blog_scripts()
{
    //  Rgeister blcok style for frontend 
    wp_register_style('blog', get_template_directory_uri() . '/css/blog.css', array(), filemtime(get_template_directory() . '/css/blog.css'));
    wp_register_script('blog-script', get_template_directory_uri() . '/blocks/blog/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'blog_scripts');

// Setup admin style for block
function blog_admin_style()
{
    wp_register_style('blog', get_template_directory_uri() . '/css/blog.css', array(), filemtime(get_template_directory() . '/css/blog.css'));
}
add_action('admin_enqueue_scripts', 'blog_admin_style');


// Setup editor style for block
function blog_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/css/blog.css');
}

add_action('init', 'blog_editor_styles');




// ======== Ajax Function ========
// ===============================

function filter_blog()
{
    ob_start();

    $post_type_slug = $_POST['post_type_slug'];
    $taxonomy_slug = $_POST['taxonomy_slug'];

    $tax_queries = $_POST['categories'] != ""  ?  array() : "";


    if ($_POST['categories'] != "") {
        $tax_queries[] = array(
            'taxonomy' => $taxonomy_slug,
            'field' => 'slug',
            'terms' =>  explode(",", $_POST['categories']),
        );
    }



    $args = array(
        'post_type' => $post_type_slug,
        'post_status' => array('publish'),
        'posts_per_page' => 12,
        'tax_query' => array(
            $tax_queries
        )
    );

    // the query.
    $the_query = new WP_Query($args);


    if ($the_query->have_posts()) : ?>

        <?php
        while ($the_query->have_posts()) :
            $the_query->the_post();
        ?>

            <?php $post_ids = get_the_ID(); ?>


            <a class="blog_item <?php if (in_array('learning-center', explode(",", $_POST['categories']))) : ?>learning-center_item<?php endif ?>" href="<?= get_permalink($post_ids); ?>">

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
        <input class="max-page" type="hidden" value="<?= $the_query->max_num_pages ?>" />
    <?php else : ?>
        <p><?php esc_html_e('Sorry, no posts matched your criteria.'); ?></p>

    <?php endif;
    $data = ob_get_clean();
    wp_send_json_success($data);
    wp_die();
}

add_action('wp_ajax_filter_blog', 'filter_blog');
add_action('wp_ajax_nopriv_filter_blog', 'filter_blog');


function load_more_blog()
{
    ob_start();



    $post_type_slug = $_POST['post_type_slug'];
    $taxonomy_slug = $_POST['taxonomy_slug'];

    $tax_queries = $_POST['categories'] != ""  ?  array() : "";

    // if ($_POST['categories'] != "") {
    //     $tax_queries[] = array(
    //         'taxonomy' => $taxonomy_slug,
    //         'field' => 'slug',
    //         'terms' =>  explode(",", $_POST['categories']),
    //     );
    // }

    $args = array(
        'post_type' => $post_type_slug,
        'post_status' => array('publish'),
        'posts_per_page' => 12,
        'tax_query'      => array(
            'relation' => 'AND',
            // Exclude learning-center category
            array(
                'taxonomy' => $taxonomy_slug,
                'field'    => 'slug',
                'terms'    => array('learning-center'),
                'operator' => 'NOT IN',
            ),
        ),
    );

    // If user selected categories, add them
    if ($_POST['categories'] != "") {
        $args['tax_query'][] = array(
            'taxonomy' => $taxonomy_slug,
            'field'    => 'slug',
            'terms'    => explode(",", $_POST['categories']),
            'operator' => 'IN',
        );
    }
    $args['paged'] = esc_attr($_POST['paged']);

    // the query.
    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) : ?>


        <?php
        while ($the_query->have_posts()) :
            $the_query->the_post();
        ?>

            <?php $post_ids = get_the_ID(); ?>

            <a class="blog_item <?php if (in_array('learning-center', explode(",", $_POST['categories']))) : ?>learning-center_item<?php endif ?>" href="<?= get_permalink($post_ids); ?>">

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

<?php endif;
    $data = ob_get_clean();
    wp_send_json_success($data);
    wp_die();
}

add_action('wp_ajax_load_more_blog', 'load_more_blog');
add_action('wp_ajax_nopriv_load_more_blog', 'load_more_blog');
