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
$id = 'tools-grid-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'tools-grid';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' =>  $classes
]);

 if ( get_field( 'add_animation' ) == 1 ) {
    $data_aos = get_field( 'animations' );
    $delay = "1000";
    $aos_args = 'data-aos="' . $data_aos . '" delay="'.$delay.'" data-aos-mirror="true" data-aos-once="false" data-aos-easing="ease-in-out"';
 }


?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
        <div class="tools-grid__description" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>
             <?= get_field( 'description' ); ?>
        </div>
      
        <?php if ( have_rows( 'tools' ) ) : ?>
            <div class="tools-grid__items">
            <?php while ( have_rows( 'tools' ) ) : the_row(); ?>
            <div class="tools-grid__item" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>
                <span class="tools-grid__item__tooltip">
                    <?= get_sub_field( 'toltip_text' ); ?>
                    <svg width="17" height="8" viewBox="0 0 17 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.5 0H0.5L7.08579 6.58579C7.86684 7.36684 9.13317 7.36684 9.91421 6.58579L16.5 0Z" fill="white" fill-opacity="0.22"/>
                    </svg>

                </span>
                <a href="<?= get_sub_field( 'link' ) ? get_sub_field( 'link' ) : "#"; ?>" target="_blank">
                    <?php $icon = get_sub_field( 'icon' ); ?>
                    <?php if ( $icon ) : ?>
                        <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                    <?php endif; ?>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </section>
<?php endif; ?>