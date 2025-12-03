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
$id = 'navigation-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'navigation collapse-' . get_field('collapse_natigation');

$wrapper_attributes = get_block_wrapper_attributes([
    'class' =>  $classes
]);


?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <nav id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>

        <?php $menu = get_field('menu'); ?>

        <button aria-label="Open menu" class="navigation__responsive-container-open">
            <?php $menu_icon = get_field('menu_icon'); ?>
            <?php if ($menu_icon) : ?>
                <img src="<?php echo esc_url($menu_icon['url']); ?>" alt="<?php echo esc_attr($menu_icon['alt']); ?>" />
            <?php else : ?>
                <span> Menu</span>
            <?php endif; ?>

        </button>
        <div class="navigation__responsive-container">
            <div class="navigation__responsive-container-head">
                <div class="wp-block-site-logo"><?= get_custom_logo(); ?></div>

                <button aria-label="Close menu" class="navigation__responsive-container-close">
                    <?php $close_icon = get_field('close_icon'); ?>
                    <?php if ($close_icon) : ?>
                        <img src="<?php echo esc_url($close_icon['url']); ?>" alt="<?php echo esc_attr($close_icon['alt']); ?>" />
                    <?php else : ?>
                        <span> Close</span>
                    <?php endif; ?>

                </button>
            </div>

            <div class="navigation__responsive-container-menu">
                <?php

                $args = array(
                    'theme_location' => $menu,
                    'menu_id'        => $menu,
                    'menu_class'        => $menu,
                    'container'       => '',
                    'walker' => new Header_Menu_Walker(),
                );

                wp_nav_menu(
                    $args
                );
                ?>

                <?php $header_button = get_field('header_button', 'option'); ?>
                <?php if ($header_button && $menu == "main-menu") : ?>
                    <a class="btn btn--primary" href="<?php echo esc_url($header_button['url']); ?>" target="<?php echo esc_attr($header_button['target']); ?>">
                        <span><?php echo esc_html($header_button['title']); ?></span>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </a>
                <?php endif; ?>


            </div>
        </div>


    </nav>
    <?php $header_button = get_field('header_button', 'option'); ?>
    <?php if ($header_button && $menu == "main-menu") : ?>
        <a class="btn btn--primary hide-mobile" href="<?php echo esc_url($header_button['url']); ?>" target="<?php echo esc_attr($header_button['target']); ?>">
            <span><?php echo esc_html($header_button['title']); ?></span>
            <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </a>
    <?php endif; ?>
<?php endif; ?>