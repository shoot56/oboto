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
$id = 'post-head-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'post-head';

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
        <?php $postTypeObj = get_post_type_object(get_post_type());  ?>

        <a href="<?= get_post_type() == "post" ? "/blog" : "/" . get_post_type(); ?>" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                <path d="M23.625 14H4.375" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12.25 6.125L4.375 14L12.25 21.875" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
        <h1><?= get_the_title() ?></h1>

        <?php
        $categories = get_the_category();
        ?>


        <?php if (! empty($categories) && esc_html($categories[0]->name) != "Learning Center") : ?>
            <figure class="post_image">
                <?php $url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()), 'thumbnail'); ?>
                <img src="<?php echo $url ?>" />
            </figure>
        <?php endif; ?>

        <p class="post-head_date_auth"><?= get_the_date('', get_the_ID()); ?>
            <span> by
                <span>
                    <a href="<?= get_author_posts_url(get_the_author_meta('ID')); ?>">
                        <?= str_replace("-", " ", get_the_author_meta('display_name')); ?>
                    </a>
                </span>
            </span>
        </p>

        <ul class="share-links">
            <li>
                <a onClick="javascript:open('https://www.linkedin.com/shareArticle?mini=true&url=<?= get_permalink() ?>', '', 'height=500,width=500')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <g clip-path="url(#clip0_12483_1042)">
                            <path d="M22.2234 0H1.77187C0.792187 0 0 0.773438 0 1.72969V22.2656C0 23.2219 0.792187 24 1.77187 24H22.2234C23.2031 24 24 23.2219 24 22.2703V1.72969C24 0.773438 23.2031 0 22.2234 0ZM7.12031 20.4516H3.55781V8.99531H7.12031V20.4516ZM5.33906 7.43438C4.19531 7.43438 3.27188 6.51094 3.27188 5.37187C3.27188 4.23281 4.19531 3.30937 5.33906 3.30937C6.47813 3.30937 7.40156 4.23281 7.40156 5.37187C7.40156 6.50625 6.47813 7.43438 5.33906 7.43438ZM20.4516 20.4516H16.8937V14.8828C16.8937 13.5562 16.8703 11.8453 15.0422 11.8453C13.1906 11.8453 12.9094 13.2937 12.9094 14.7891V20.4516H9.35625V8.99531H12.7687V10.5609H12.8156C13.2891 9.66094 14.4516 8.70938 16.1813 8.70938C19.7859 8.70938 20.4516 11.0813 20.4516 14.1656V20.4516Z" fill="white" />
                        </g>
                        <defs>
                            <clipPath id="clip0_12483_1042">
                                <rect width="24" height="24" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                </a>
            </li>
            <li>
                <a onClick="javascript:open('https://twitter.com/intent/tweet?text=<?= get_permalink() ?>', '', 'height=500,width=500')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M18.3263 1.90393H21.6998L14.3297 10.3274L23 21.7899H16.2112L10.894 14.838L4.80995 21.7899H1.43443L9.31744 12.78L1 1.90393H7.96111L12.7674 8.25826L18.3263 1.90393ZM17.1423 19.7707H19.0116L6.94539 3.81706H4.93946L17.1423 19.7707Z" fill="white" />
                    </svg>
                </a>
            </li>
            <li>
                <a onClick="javascript:open('https://www.facebook.com/sharer/sharer.php?u=<?= get_permalink() ?>', '', 'height=500,width=500')">

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" height="24" fill="#fff">
                        <path d="M240 363.3L240 576L356 576L356 363.3L442.5 363.3L460.5 265.5L356 265.5L356 230.9C356 179.2 376.3 159.4 428.7 159.4C445 159.4 458.1 159.8 465.7 160.6L465.7 71.9C451.4 68 416.4 64 396.2 64C289.3 64 240 114.5 240 223.4L240 265.5L174 265.5L174 363.3L240 363.3z" />
                    </svg>

                </a>
            </li>
        </ul>
    </section>


<?php endif; ?>