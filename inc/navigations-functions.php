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
        $has_children = in_array("menu-item-has-children", $menu_item->classes, true);
        $item_type = get_field("item_type", $menu_item) ?: 'link';
        $open_in_new_tab = (int) get_field('open_in_new_tab', $menu_item) === 1;


?>
        <li class="menu-item menu-item--depth-<?php echo esc_attr((string) $depth); ?>">
            <a href="<?php echo esc_url($menu_item->url); ?>" class="menu-item__link item-type--<?php echo esc_attr($item_type); ?><?php echo $has_children ? ' has-children' : ''; ?>"<?php if ($open_in_new_tab): ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
                <?php if ($icon) : ?>
                    <span class="icon"><img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt']); ?>" /></span>
                <?php endif; ?>
                <span class="menu-item-text"><?php echo esc_html($menu_item->title); ?></span>
                <?php if ($has_children) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor" aria-hidden="true" focusable="false">
                        <path d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z" />
                    </svg>
                <?php endif; ?>
            </a>
            <?php if ($has_children) : ?>

                <div class="submenu__container">
                <?php endif; ?>
            <?php

            $output .= ob_get_contents();
            ob_end_clean();
        }

        function end_el(&$output, $menu_item, $depth = 0, $args = null)
        {
            ob_start();
            ?>
                <?php if (in_array("menu-item-has-children", $menu_item->classes, true)) : ?>
                </div>
            <?php endif; ?>
        </li>


<?php
            $output .= ob_get_contents();
            ob_end_clean();
        }
    }
