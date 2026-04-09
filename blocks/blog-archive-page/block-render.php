<?php

/**
 * Block template file: block-render.php
 *
 * @param array $block The block settings and attributes.
 */

$id = 'blog-archive-page-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'blog-archive js-blog-archive',
));

if (isset($block['data']['preview_image_help'])) :
    $file_url = str_replace(get_stylesheet_directory(), '', dirname(__FILE__));
    echo '<img src="' . get_stylesheet_directory_uri() . $file_url . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
else :
    global $wp_query;
    $current_page = oboto_blog_archive_get_current_page();
    $current_category_slug = oboto_blog_archive_get_current_category_slug();
    $terms = oboto_blog_archive_get_filter_terms();
    $current_category = is_category() ? get_queried_object() : null;
    $current_category_name = '';
    $current_category_description = '';
    $archive_title = '';
    $archive_description = '';

    if ($current_category instanceof WP_Term && $current_category->taxonomy === 'category' && $current_category->slug !== 'learning-center') {
        $current_category_name = (string) $current_category->name;
        $current_category_description = term_description($current_category);
        $archive_title = $current_page > 1
            ? sprintf('%s - Page %d', $current_category_name, $current_page)
            : $current_category_name;
        $archive_description = $current_category_description;
    } elseif (is_home()) {
        $archive_title = $current_page > 1
            ? sprintf('Obot Blog - Page %d', $current_page)
            : 'Obot Blog';
        $archive_description = 'Stay up to date on all the latest updates on the Obot MCP Gateway';
    }
?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
        <?php if ($archive_title !== '') : ?>
            <div class="blog-archive__header">
                <h1 class="blog-archive__title"><?php echo esc_html($archive_title); ?></h1>
                <?php if ($archive_description !== '') : ?>
                    <div class="blog-archive__description">
                        <?php if ($current_category_name !== '') : ?>
                            <?php echo wp_kses_post($archive_description); ?>
                        <?php else : ?>
                            <p><?php echo esc_html($archive_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <ul class="blog_categories">
            <?php $posts_page_url = get_permalink((int) get_option('page_for_posts')); ?>
            <?php if ($posts_page_url) : ?>
                <li class="<?php echo $current_category_slug === '' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url($posts_page_url); ?>">All</a>
                </li>
            <?php endif; ?>
            <?php foreach ($terms as $term) : ?>
                <?php $term_link = get_term_link($term); ?>
                <?php if (is_wp_error($term_link)) : ?>
                    <?php continue; ?>
                <?php endif; ?>
                <li class="<?php echo $term->slug === $current_category_slug ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($term->name); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="blog_list">
            <?php echo oboto_blog_archive_render_posts_markup($wp_query); ?>
        </div>

        <?php echo oboto_blog_archive_render_pagination($wp_query); ?>
    </section>
<?php endif; ?>
