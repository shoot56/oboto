<?php

if (!defined('OBOTO_LEARNING_CENTER_ARCHIVE_POSTS_PER_PAGE')) {
    define('OBOTO_LEARNING_CENTER_ARCHIVE_POSTS_PER_PAGE', 12);
}

function learning_center_archive_get_current_page()
{
    $paged = max(1, (int) get_query_var('paged'));
    $page = max(1, (int) get_query_var('page'));

    return max($paged, $page);
}

function learning_center_archive_get_query_args($category = '', $paged = 1)
{
    $args = array(
        'post_type' => 'learning-center',
        'post_status' => array('publish'),
        'posts_per_page' => OBOTO_LEARNING_CENTER_ARCHIVE_POSTS_PER_PAGE,
        'paged' => max(1, (int) $paged),
    );

    if (!empty($category) && $category !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'learning-center-category',
                'field' => 'slug',
                'terms' => $category,
                'include_children' => true,
            ),
        );
    }

    return $args;
}

function learning_center_archive_render_post_card($post_id)
{
    $image_url = wp_get_attachment_url(get_post_thumbnail_id($post_id), 'thumbnail');

    ob_start();
    ?>
    <a class="learning-center-archive__item" href="<?php echo esc_url(get_permalink($post_id)); ?>">
        <figure class="learning-center-archive__image">
            <?php if ($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" />
            <?php endif; ?>
        </figure>
        <div class="learning-center-archive__data">
            <h3><?php echo esc_html(get_the_title($post_id)); ?></h3>
            <p class="learning-center-archive__description">
                <?php echo esc_html(get_the_excerpt($post_id)); ?>
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
    <?php

    return ob_get_clean();
}

function learning_center_archive_render_posts_markup($query, $include_max_page = false)
{
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo learning_center_archive_render_post_card(get_the_ID());
        }
        wp_reset_postdata();

        if ($include_max_page) {
            ?>
            <input class="max-page" type="hidden" value="<?php echo esc_attr((int) $query->max_num_pages); ?>" />
            <?php
        }
    } else {
        echo '<p>' . esc_html__('Sorry, no posts matched your criteria.', 'oboto') . '</p>';
    }

    return ob_get_clean();
}

function learning_center_archive_render_pagination($query)
{
    if ((int) $query->max_num_pages < 2) {
        return '';
    }

    $pagination_links = paginate_links(array(
        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
        'format' => '',
        'current' => learning_center_archive_get_current_page(),
        'total' => (int) $query->max_num_pages,
        'prev_text' => __('Previous', 'oboto'),
        'next_text' => __('Next', 'oboto'),
        'type' => 'array',
    ));

    if (empty($pagination_links) || !is_array($pagination_links)) {
        return '';
    }

    ob_start();
    ?>
    <nav class="learning-center-archive__pagination" aria-label="<?php echo esc_attr__('Learning Center pagination', 'oboto'); ?>">
        <?php foreach ($pagination_links as $link) : ?>
            <?php
            $link = str_replace('page-numbers', 'page-numbers learning-center-archive__page-link', $link);
            $link = str_replace('prev learning-center-archive__page-link', 'prev learning-center-archive__page-link btn btn--outline', $link);
            $link = str_replace('next learning-center-archive__page-link', 'next learning-center-archive__page-link btn btn--outline', $link);
            $link = str_replace('current learning-center-archive__page-link', 'current learning-center-archive__page-link is-current', $link);
            ?>
            <?php echo wp_kses_post($link); ?>
        <?php endforeach; ?>
    </nav>
    <?php

    return ob_get_clean();
}

/**
 * Enqueue scripts and styles for learning-center-archive block.
 */
function learning_center_archive_scripts()
{
    // Register block style for frontend 
    wp_register_style('learning-center', get_template_directory_uri() . '/css/learning-center.css', array(), filemtime(get_template_directory() . '/css/learning-center.css'));
    wp_register_script('learning-center-archive-script', get_template_directory_uri() . '/blocks/learning-center-archive/view-script.js', ['jquery'], wp_get_theme()->get('Version'));
    wp_localize_script('learning-center-archive-script', 'obotoLearningCenterArchive', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ));
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

    $the_query = new WP_Query(learning_center_archive_get_query_args($category, 1));
    echo learning_center_archive_render_posts_markup($the_query, true);

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

    $the_query = new WP_Query(learning_center_archive_get_query_args($category, $paged));
    echo learning_center_archive_render_posts_markup($the_query);

    $data = ob_get_clean();
    wp_send_json_success($data);
    wp_die();
}
add_action('wp_ajax_load_more_learning_center_archive', 'load_more_learning_center_archive');
add_action('wp_ajax_nopriv_load_more_learning_center_archive', 'load_more_learning_center_archive');
