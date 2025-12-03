<?php

/**
 * Navigation header menu walker
 */
class Header_Menu_Walker extends Walker_Nav_Menu
{
    function start_el(&$output, $menu_item, $depth = 0, $args = null, $current_object_id = 0)
    {
        ob_start();

        $icon = get_field('icon', $menu_item);


?>
        <li class="menu-item menu-item--depth-<?= $depth; ?> menu-item--type-<?= get_field('submenu_type', $menu_item) ?>">
            <a href="<?= $menu_item->url; ?>" class="menu-item__link item-type--<?= get_field("item_type", $menu_item) ?> <?php if (in_array("menu-item-has-children", $menu_item->classes)) : ?>has-children<?php endif; ?>" <?php if (get_field('open_in_new_tab', $menu_item) == 1): ?>target="_blank" <?php endif; ?>>
                <?php if ($icon) : ?>
                    <span class="icon"><img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt']); ?>" /></span>
                <?php endif; ?>
                <span class="menu-item-text"><?= $menu_item->title; ?></span>
                <?php if (in_array("menu-item-has-children", $menu_item->classes)) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z" />
                    </svg>
                <?php endif; ?>
            </a>
            <?php if (in_array("menu-item-has-children", $menu_item->classes)) : ?>

                <div class="submenu__container submenu--type-<?= get_field('submenu_type', $menu_item) ? get_field('submenu_type', $menu_item) : "default"; ?>">
                    <?php if ($depth == 0): ?>
                        <div class="overlay"></div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php

            $output .= ob_get_contents();
            ob_end_clean();
        }

        function end_el(&$output, $menu_item, $depth = 0, $args = null)
        {
            ob_start();
            ?>
                <?php if (in_array("menu-item-has-children", $menu_item->classes)) : ?>
                </div>
            <?php endif; ?>
        </li>


<?php
            $output .= ob_get_contents();
            ob_end_clean();
        }
    }
