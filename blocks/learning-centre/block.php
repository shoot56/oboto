<?php

/**
 * Enqueue scripts and styles for block.
 */
function learning_center_scripts()
{
    //  Rgeister blcok style for frontend 
    wp_register_style('learning-center', get_template_directory_uri() . '/css/learning-center.css', array(), filemtime(get_template_directory() . '/css/learning-center.css'));
    wp_register_script('learning-center-script', get_template_directory_uri() . '/blocks/learning-centre/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'learning_center_scripts');

// Setup admin style for block
function learning_center_admin_style()
{
    wp_register_style('learning-center', get_template_directory_uri() . '/css/learning-center.css', array(), filemtime(get_template_directory() . '/css/learning-center.css'));
}
add_action('admin_enqueue_scripts', 'learning_center_admin_style');


// Setup editor style for block
function learning_center_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/css/learning-center.css');
}

add_action('init', 'learning_center_editor_styles');



function loadMoreLearningCenter()
{
    ob_start();

    $args = array(
        'post_type' => 'post',
        'post_status' => array('publish'),
        'posts_per_page' => 12,
        'tax_query' => array(
            array(
                'taxonomy' => 'category', // Replace with your taxonomy name
                'field'    => 'slug',
                'terms'    => 'learning-center',
            ),
        ),
    );
    $args['paged'] = esc_attr($_POST['paged']);

    // the query.
    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) : ?>


        <?php
        while ($the_query->have_posts()) :
            $the_query->the_post();
        ?>

            <?php $post_ids = get_the_ID(); ?>

            <a class="learning-center_item" href="<?= get_permalink($post_ids); ?>">
                <figure class="learning-center_image">
                    <?php $url = wp_get_attachment_url(get_post_thumbnail_id($post_ids), 'thumbnail'); ?>
                    <?php if ($url) : ?>
                        <img src="<?php echo $url ?>" />
                    <?php endif; ?>
                </figure>
                <div class="learning-center_data">
                    <h3><?= get_the_title(); ?></h3>
                    <p class="learning-center_description">
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

add_action('wp_ajax_load_more_learning_center', 'loadMoreLearningCenter');
add_action('wp_ajax_nopriv_load_more_learning_center', 'loadMoreLearningCenter');
