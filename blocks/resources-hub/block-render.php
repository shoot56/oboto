<?php

/**
 * Block template file: block-render.php
 *
 * Renders the Resources Hub: Docs & Resources buttons (manual),
 * latest Blog and Learning Center posts, community links and manual events.
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'resources-hub-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'resources-hub']);

$resource_links = (array) get_field('extra_resource_links');
$resources_title = get_field('resources_title') ?: 'Docs & Resources';
$resources_description = get_field('resources_description') ?: 'All you need to get started with the Obot MCP Gateway';
$hero_title = get_field('hero_title');
$hero_description = get_field('hero_description');

$blogs_title = get_field('blogs_title') ?: 'Blogs';
$blogs_description = get_field('blogs_description') ?: 'Detailed guides and inspiration focused on using MCP and agentic AI tools.';
$blogs_read_all = get_field('blogs_read_all');

$lc_title = get_field('learning_center_title') ?: 'Learning Center';
$lc_description = get_field('learning_center_description') ?: 'A catalog of interesting articles on MCP basics, advanced guides, and all things AI, LLM, and more.';
$lc_read_all = get_field('learning_center_read_all');

$community_title = get_field('community_title') ?: "See what's happening in our community";
$community_description = get_field('community_description');
$discord_link = get_field('discord_link');
$github_link = get_field('github_link');
$events_items = (array) get_field('events_items');

?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__));
    echo '<img src="' . esc_url(get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help']) . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
<div id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>

    <?php if (!empty($hero_title) || !empty($hero_description)) : ?>
    <!-- Hero -->
    <section class="resources-hub__hero">
        <div class="resources-hub__hero-content">
            <?php if (!empty($hero_title)) : ?>
                <h1 class="resources-hub__hero-title"><?php echo esc_html($hero_title); ?></h1>
            <?php endif; ?>
            <?php if (!empty($hero_description)) : ?>
                <p class="resources-hub__hero-desc"><?php echo esc_html($hero_description); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Docs & Resources (quick links) -->
    <section class="resources-hub__section">
        <h2 class="resources-hub__section-title"><?php echo esc_html($resources_title); ?></h2>
        <p class="resources-hub__section-desc"><?php echo esc_html($resources_description); ?></p>
        <div class="resources-hub__buttons">
            <?php foreach ($resource_links as $row) :
                $link = is_array($row) && isset($row['link']) ? $row['link'] : $row;
                if (empty($link) || !is_array($link) || empty($link['url'])) continue;
                $title = !empty($link['title']) ? $link['title'] : $link['url'];
                $target = !empty($link['target']) ? $link['target'] : '_self';
                $icon = isset($row['icon']) && is_array($row['icon']) && !empty($row['icon']['url']) ? $row['icon'] : null;
            ?>
                <a href="<?php echo esc_url($link['url']); ?>" class="btn btn--outline"<?php echo ($target !== '_self') ? ' target="' . esc_attr($target) . '" rel="noopener"' : ''; ?>>
                    <span><?php echo esc_html($title); ?></span>
                    <span class="icon">
                        <?php if ($icon) : ?>
                            <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt'] ?? $title); ?>" width="24" height="24" />
                        <?php else : ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        <?php endif; ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Blogs (latest 3 from /blog/) -->
    <section class="resources-hub__section">
        <h2 class="resources-hub__section-title"><?php echo esc_html($blogs_title); ?></h2>
        <p class="resources-hub__section-desc"><?php echo esc_html($blogs_description); ?></p>
        <?php
        $blog_query = new WP_Query(array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'order'          => 'DESC',
            'orderby'        => 'date',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => array('blog'),
                ),
            ),
        ));
        ?>
        <div class="resources-hub__grid resources-hub__grid--three">
            <?php if ($blog_query->have_posts()) : while ($blog_query->have_posts()) : $blog_query->the_post(); $pid = get_the_ID(); ?>
                <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="resources-hub__card">
                    <figure class="resources-hub__card-image">
                        <?php $thumb = wp_get_attachment_image_url(get_post_thumbnail_id($pid), 'medium_large'); ?>
                        <?php if ($thumb) : ?><img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" /><?php endif; ?>
                    </figure>
                    <div class="resources-hub__card-data">
                        <h3 class="resources-hub__card-title"><?php echo esc_html(get_the_title()); ?></h3>
                        <p class="resources-hub__card-desc"><?php echo esc_html(wp_trim_words(get_the_excerpt($pid), 22, '...')); ?></p>
                        <span class="btn btn--no-border">
                            <span>Read More</span>
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </span>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
        <?php if ($blogs_read_all && !empty($blogs_read_all['url'])) : ?>
            <div class="resources-hub__read-all">
                <a href="<?php echo esc_url($blogs_read_all['url']); ?>" class="btn btn--outline"<?php if (!empty($blogs_read_all['target'])) : ?> target="<?php echo esc_attr($blogs_read_all['target']); ?>" rel="noopener"<?php endif; ?>>
                    <span><?php echo esc_html(!empty($blogs_read_all['title']) ? $blogs_read_all['title'] : 'View all'); ?></span>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Learning Center (latest 3) -->
    <section class="resources-hub__section">
        <h2 class="resources-hub__section-title"><?php echo esc_html($lc_title); ?></h2>
        <p class="resources-hub__section-desc"><?php echo esc_html($lc_description); ?></p>
        <?php
        $lc_query = new WP_Query(array(
            'post_type'      => 'learning-center',
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'order'          => 'DESC',
            'orderby'        => 'date',
        ));
        ?>
        <div class="resources-hub__grid resources-hub__grid--three">
            <?php if ($lc_query->have_posts()) : while ($lc_query->have_posts()) : $lc_query->the_post(); $pid = get_the_ID(); ?>
                <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="resources-hub__card">
                    <?php $thumb = wp_get_attachment_image_url(get_post_thumbnail_id($pid), 'medium_large'); ?>
                    <?php if ($thumb) : ?>
                        <figure class="resources-hub__card-image">
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                        </figure>
                    <?php endif; ?>
                    <div class="resources-hub__card-data">
                        <h3 class="resources-hub__card-title"><?php echo esc_html(get_the_title()); ?></h3>
                        <p class="resources-hub__card-desc"><?php echo esc_html(wp_trim_words(get_the_excerpt($pid), 22, '...')); ?></p>
                        <span class="btn btn--no-border">
                            <span>Read More</span>
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </span>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
        <?php if ($lc_read_all && !empty($lc_read_all['url'])) : ?>
            <div class="resources-hub__read-all">
                <a href="<?php echo esc_url($lc_read_all['url']); ?>" class="btn btn--outline"<?php if (!empty($lc_read_all['target'])) : ?> target="<?php echo esc_attr($lc_read_all['target']); ?>" rel="noopener"<?php endif; ?>>
                    <span><?php echo esc_html(!empty($lc_read_all['title']) ? $lc_read_all['title'] : 'View all'); ?></span>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Community -->
    <section class="resources-hub__section">
        <h2 class="resources-hub__section-title"><?php echo esc_html($community_title); ?></h2>
        <?php if ($community_description) : ?>
            <p class="resources-hub__section-desc"><?php echo esc_html($community_description); ?></p>
        <?php endif; ?>
        <div class="resources-hub__community-links">
            <?php if ($discord_link && !empty($discord_link['url'])) : ?>
                <a href="<?php echo esc_url($discord_link['url']); ?>" class="btn btn--outline"<?php echo !empty($discord_link['target']) ? ' target="' . esc_attr($discord_link['target']) . '" rel="noopener"' : ''; ?>>
                    <span><?php echo esc_html(!empty($discord_link['title']) ? $discord_link['title'] : 'Discord'); ?></span>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </a>
            <?php endif; ?>
            <?php if ($github_link && !empty($github_link['url'])) : ?>
                <a href="<?php echo esc_url($github_link['url']); ?>" class="btn btn--outline"<?php echo !empty($github_link['target']) ? ' target="' . esc_attr($github_link['target']) . '" rel="noopener"' : ''; ?>>
                    <span><?php echo esc_html(!empty($github_link['title']) ? $github_link['title'] : 'GitHub'); ?></span>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </a>
            <?php endif; ?>
        </div>
        <?php if (!empty($events_items)) : ?>
            <div class="resources-hub__grid resources-hub__grid--three">
                <?php foreach ($events_items as $row) :
                    $event_link = null;
                    $event_title = '';
                    if (is_array($row)) {
                        if (isset($row['link']) && is_array($row['link']) && !empty($row['link']['url'])) {
                            $event_link = $row['link'];
                            $event_title = !empty($row['link']['title']) ? $row['link']['title'] : $row['link']['url'];
                        } elseif (isset($row['url'])) {
                            $event_link = $row;
                            $event_title = !empty($row['title']) ? $row['title'] : $row['url'];
                        }
                    }
                    if (!$event_link || empty($event_link['url'])) continue;
                    $target = !empty($event_link['target']) ? $event_link['target'] : '_self';
                ?>
                    <a href="<?php echo esc_url($event_link['url']); ?>" class="resources-hub__card"<?php echo ($target !== '_self') ? ' target="' . esc_attr($target) . '" rel="noopener"' : ''; ?>>
                        <div class="resources-hub__card-data">
                            <h3 class="resources-hub__card-title"><?php echo esc_html($event_title); ?></h3>
                            <span class="btn btn--no-border">
                                <span>Read More</span>
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</div>
<?php endif; ?>
