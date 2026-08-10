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
    <?php
    $menu          = get_field('menu');
    $header_button = get_field('header_button', 'option');
    $github_button = get_field('github_button', 'option');
    $is_main_menu  = 'main-menu' === $menu;
    ?>
    <nav id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>

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

            <div class="navigation__responsive-container-menu<?php echo is_array($github_button) && !empty($github_button['url']) && $is_main_menu ? ' has-github-button' : ''; ?>">
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

                <?php if ($header_button && $is_main_menu) : ?>
                    <a class="btn btn--primary" href="<?php echo esc_url($header_button['url']); ?>"<?php if ( ! empty( $header_button['target'] ) ) : ?> target="<?php echo esc_attr( $header_button['target'] ); ?>"<?php endif; ?>>
                        <span><?php echo esc_html($header_button['title']); ?></span>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </a>
                <?php endif; ?>

                <?php if (is_array($github_button) && !empty($github_button['url']) && $is_main_menu) : ?>
                    <?php $github_label = !empty($github_button['title']) ? $github_button['title'] : 'View on GitHub'; ?>
                    <a class="header-github-link header-github-link--mobile" href="<?php echo esc_url($github_button['url']); ?>" aria-label="<?php echo esc_attr($github_label); ?>"<?php if (!empty($github_button['target'])) : ?> target="<?php echo esc_attr($github_button['target']); ?>" rel="noopener noreferrer"<?php endif; ?>>
                        <span><?php echo esc_html($github_label); ?></span>
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 .7a11.5 11.5 0 0 0-3.64 22.4c.58.1.79-.25.79-.56v-2.23c-3.22.7-3.9-1.37-3.9-1.37-.52-1.34-1.28-1.7-1.28-1.7-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.57-.3-5.27-1.29-5.27-5.69 0-1.26.45-2.28 1.19-3.09-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.16 1.18a10.9 10.9 0 0 1 5.76 0c2.2-1.49 3.16-1.18 3.16-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.83 1.19 3.09 0 4.41-2.71 5.39-5.29 5.68.42.36.79 1.06.79 2.14v3.17c0 .31.21.67.8.56A11.5 11.5 0 0 0 12 .7Z" />
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>


    </nav>
    <?php if ($is_main_menu && ($header_button || (is_array($github_button) && !empty($github_button['url'])))) : ?>
        <div class="header-actions">
            <?php if ($header_button) : ?>
                <a class="btn btn--primary hide-mobile" href="<?php echo esc_url($header_button['url']); ?>"<?php if ( ! empty( $header_button['target'] ) ) : ?> target="<?php echo esc_attr( $header_button['target'] ); ?>"<?php endif; ?>>
                    <span><?php echo esc_html($header_button['title']); ?></span>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </a>
            <?php endif; ?>

            <?php if (is_array($github_button) && !empty($github_button['url'])) : ?>
                <?php $github_label = !empty($github_button['title']) ? $github_button['title'] : 'View on GitHub'; ?>
                <a class="header-github-link header-github-link--desktop" href="<?php echo esc_url($github_button['url']); ?>" aria-label="<?php echo esc_attr($github_label); ?>"<?php if (!empty($github_button['target'])) : ?> target="<?php echo esc_attr($github_button['target']); ?>" rel="noopener noreferrer"<?php endif; ?>>
                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 .7a11.5 11.5 0 0 0-3.64 22.4c.58.1.79-.25.79-.56v-2.23c-3.22.7-3.9-1.37-3.9-1.37-.52-1.34-1.28-1.7-1.28-1.7-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.57-.3-5.27-1.29-5.27-5.69 0-1.26.45-2.28 1.19-3.09-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.16 1.18a10.9 10.9 0 0 1 5.76 0c2.2-1.49 3.16-1.18 3.16-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.83 1.19 3.09 0 4.41-2.71 5.39-5.29 5.68.42.36.79 1.06.79 2.14v3.17c0 .31.21.67.8.56A11.5 11.5 0 0 0 12 .7Z" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
