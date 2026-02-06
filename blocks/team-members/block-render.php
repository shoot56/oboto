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
$id = 'team-members-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'team-members';

// Get columns setting
$columns = get_field('columns') ?: '4';
$classes .= ' team-members--columns-' . $columns;

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $classes
]);

?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__));
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
        <?php if (have_rows('team_members')) : ?>
            <div class="team-members__grid">
                <?php while (have_rows('team_members')) : the_row(); ?>
                    <?php 
                    $image = get_sub_field('image');
                    $name = get_sub_field('name');
                    $position = get_sub_field('position');
                    $is_featured = get_sub_field('is_featured');
                    ?>
                    <div class="team-members__card <?php echo $is_featured ? 'team-members__card--featured' : ''; ?>">
                        <?php if ($image) : ?>
                            <div class="team-members__image">
                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?: $name); ?>" />
                            </div>
                        <?php endif; ?>
                        
                        <div class="team-members__content">
                            <div class="team-members__info">
                                <?php if ($name) : ?>
                                    <h3 class="team-members__name"><?php echo esc_html($name); ?></h3>
                                <?php endif; ?>
                                
                                <?php if ($position) : ?>
                                    <p class="team-members__position"><?php echo esc_html($position); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (have_rows('social_links')) : ?>
                                <div class="team-members__social">
                                    <?php while (have_rows('social_links')) : the_row(); ?>
                                        <?php 
                                        $icon = get_sub_field('icon');
                                        $url = get_sub_field('url');
                                        ?>
                                        <?php if ($icon && $url) : ?>
                                            <a href="<?php echo esc_url($url); ?>" 
                                               class="team-members__social-link" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               aria-label="Social media link">
                                                <img src="<?php echo esc_url($icon['url']); ?>" 
                                                     alt="<?php echo esc_attr($icon['alt'] ?: 'Social icon'); ?>" />
                                            </a>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
