<?php

if (!defined('OBOTO_BLOG_ARCHIVE_POSTS_PER_PAGE')) {
    define('OBOTO_BLOG_ARCHIVE_POSTS_PER_PAGE', 12);
}

function oboto_blog_archive_get_excluded_category_ids()
{
    $excluded_ids = array();
    $learning_center_term = get_term_by('slug', 'learning-center', 'category');

    if ($learning_center_term instanceof WP_Term) {
        $excluded_ids[] = (int) $learning_center_term->term_id;
    }

    return $excluded_ids;
}

function oboto_blog_archive_adjust_main_query($query)
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if (!$query->is_home() && !$query->is_category()) {
        return;
    }

    $query->set('post_type', 'post');
    $query->set('posts_per_page', OBOTO_BLOG_ARCHIVE_POSTS_PER_PAGE);

    if ($query->is_home()) {
        $excluded_ids = oboto_blog_archive_get_excluded_category_ids();

        if (!empty($excluded_ids)) {
            $query->set('category__not_in', $excluded_ids);
        }
    }
}
add_action('pre_get_posts', 'oboto_blog_archive_adjust_main_query');

function oboto_blog_archive_get_query_args($categories = array(), $paged = 1)
{
    $tax_query = array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => array('learning-center'),
            'operator' => 'NOT IN',
        ),
    );

    if (!empty($categories)) {
        $tax_query[] = array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => $categories,
            'operator' => 'IN',
        );
    }

    return array(
        'post_type'      => 'post',
        'post_status'    => array('publish'),
        'posts_per_page' => OBOTO_BLOG_ARCHIVE_POSTS_PER_PAGE,
        'paged'          => max(1, (int) $paged),
        'tax_query'      => $tax_query,
    );
}

function oboto_blog_archive_get_display_query()
{
    $current_page = oboto_blog_archive_get_current_page();

    if (is_category()) {
        $current_category_slug = oboto_blog_archive_get_current_category_slug();

        if ($current_category_slug !== '') {
            return new WP_Query(oboto_blog_archive_get_query_args(array($current_category_slug), $current_page));
        }
    }

    return new WP_Query(oboto_blog_archive_get_query_args(array(), $current_page));
}

function oboto_blog_archive_get_current_page()
{
    $paged = max(1, (int) get_query_var('paged'));
    $page = max(1, (int) get_query_var('page'));

    return max($paged, $page);
}

function oboto_blog_archive_get_filter_terms()
{
    $terms = get_terms(array(
        'taxonomy'   => 'category',
        'hide_empty' => false,
    ));

    if (is_wp_error($terms)) {
        return array();
    }

    return array_values(array_filter($terms, function ($term) {
        return $term instanceof WP_Term
            && $term->term_id !== 1
            && $term->slug !== 'learning-center';
    }));
}

function oboto_blog_archive_get_current_category_slug()
{
    if (!is_category()) {
        return '';
    }

    $term = get_queried_object();

    if (!$term instanceof WP_Term || $term->taxonomy !== 'category') {
        return '';
    }

    return $term->slug === 'learning-center' ? '' : $term->slug;
}

function oboto_blog_archive_render_post_card($post_id)
{
    $image_url = wp_get_attachment_url(get_post_thumbnail_id($post_id));
    ob_start();
    ?>
    <a class="blog_item" href="<?php echo esc_url(get_permalink($post_id)); ?>">
        <figure class="blog_image">
            <?php if ($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" />
            <?php endif; ?>
        </figure>
        <div class="blog_data">
            <h3><?php echo esc_html(get_the_title($post_id)); ?></h3>
            <p class="blog_description">
                <?php echo esc_html(get_the_excerpt($post_id)); ?>
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
    <?php
    return ob_get_clean();
}

function oboto_blog_archive_render_posts_markup($query)
{
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo oboto_blog_archive_render_post_card(get_the_ID());
        }
        wp_reset_postdata();
    } else {
        echo '<p>' . esc_html__('Sorry, no posts matched your criteria.', 'oboto') . '</p>';
    }

    return ob_get_clean();
}

function oboto_blog_archive_render_pagination($query)
{
    if ((int) $query->max_num_pages < 2) {
        return '';
    }

    $pagination_links = paginate_links(array(
        'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
        'format'    => '',
        'current'   => max(1, oboto_blog_archive_get_current_page()),
        'total'     => (int) $query->max_num_pages,
        'prev_text' => __('Previous', 'oboto'),
        'next_text' => __('Next', 'oboto'),
        'type'      => 'array',
    ));

    if (empty($pagination_links) || !is_array($pagination_links)) {
        return '';
    }

    ob_start();
    ?>
    <nav class="blog-archive__pagination" aria-label="<?php echo esc_attr__('Blog pagination', 'oboto'); ?>">
        <?php foreach ($pagination_links as $link) : ?>
            <?php
            $link = str_replace('page-numbers', 'page-numbers blog-archive__page-link', $link);
            $link = str_replace('prev blog-archive__page-link', 'prev blog-archive__page-link btn btn--outline', $link);
            $link = str_replace('next blog-archive__page-link', 'next blog-archive__page-link btn btn--outline', $link);
            $link = str_replace('current blog-archive__page-link', 'current blog-archive__page-link is-current', $link);
            ?>
            <?php echo wp_kses_post($link); ?>
        <?php endforeach; ?>
    </nav>
    <?php

    return ob_get_clean();
}

function oboto_blog_archive_page_scripts()
{
    wp_register_style(
        'blog-archive-page',
        get_template_directory_uri() . '/css/blog-archive.css',
        array(),
        filemtime(get_template_directory() . '/css/blog-archive.css')
    );
}
add_action('wp_enqueue_scripts', 'oboto_blog_archive_page_scripts');

function oboto_blog_archive_page_admin_style()
{
    wp_register_style(
        'blog-archive-page',
        get_template_directory_uri() . '/css/blog-archive.css',
        array(),
        filemtime(get_template_directory() . '/css/blog-archive.css')
    );
}
add_action('admin_enqueue_scripts', 'oboto_blog_archive_page_admin_style');

function oboto_blog_archive_page_editor_styles()
{
    add_editor_style(get_template_directory_uri() . '/css/blog-archive.css');
}
add_action('init', 'oboto_blog_archive_page_editor_styles');
