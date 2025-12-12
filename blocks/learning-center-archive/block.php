<?php

/**
 * Enqueue scripts and styles for learning-center-archive block.
 */
function learning_center_archive_scripts()
{
    // Register block style for frontend 
    wp_register_style('learning-center', get_template_directory_uri() . '/css/learning-center.css', array(), filemtime(get_template_directory() . '/css/learning-center.css'));
    wp_register_script('learning-center-archive-script', get_template_directory_uri() . '/blocks/learning-center-archive/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'learning_center_archive_scripts');

// Setup admin style for block
function learning_center_archive_admin_style()
{
    wp_register_style('learning-center', get_template_directory_uri() . '/css/learning-center.css', array(), filemtime(get_template_directory() . '/css/learning-center.css'));
}
add_action('admin_enqueue_scripts', 'learning_center_archive_admin_style');

// Setup editor style for block
function learning_center_archive_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/css/learning-center.css');
}
add_action('init', 'learning_center_archive_editor_styles');

/**
 * AJAX handler for filtering learning center posts by category
 */
function filter_learning_center_archive()
{
    ob_start();

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

    $args = array(
        'post_type' => 'learning-center',
        'post_status' => array('publish'),
        'posts_per_page' => 12,
    );

    // Add category filter if provided
    if (!empty($category) && $category !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'learning-center-category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) :
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
add_action('wp_ajax_filter_learning_center_archive', 'filter_learning_center_archive');
add_action('wp_ajax_nopriv_filter_learning_center_archive', 'filter_learning_center_archive');

/**
 * AJAX handler for loading more learning center posts
 */
function load_more_learning_center_archive()
{
    ob_start();

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = array(
        'post_type' => 'learning-center',
        'post_status' => array('publish'),
        'posts_per_page' => 12,
        'paged' => $paged,
    );

    // Add category filter if provided
    if (!empty($category) && $category !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'learning-center-category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) :
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
add_action('wp_ajax_load_more_learning_center_archive', 'load_more_learning_center_archive');
add_action('wp_ajax_nopriv_load_more_learning_center_archive', 'load_more_learning_center_archive');
