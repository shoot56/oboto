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
$id = 'cta-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'cta';

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
        <div>
            <div class="cta__wrap <?=get_field( 'add_border' ) == 1 ? "has-border" : ""?>" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>
                <?php $big_bottom_left_image = get_field( 'big_bottom_left_image' ); ?>
                <?php if ( $big_bottom_left_image ) : ?>
                    <figure class="cta__image bottom-img-1">
                            <img src="<?php echo esc_url( $big_bottom_left_image['url'] ); ?>" alt="<?php echo esc_attr( $big_bottom_left_image['alt'] ); ?>" />
                    </figure>
                <?php endif; ?>
                <?php $small_bottom_left_image = get_field( 'small_bottom_left_image' ); ?>
                <?php if ( $small_bottom_left_image ) : ?>
                    <figure class="cta__image bottom-img-2">
                        <img src="<?php echo esc_url( $small_bottom_left_image['url'] ); ?>" alt="<?php echo esc_attr( $small_bottom_left_image['alt'] ); ?>" />
                    </figure>
                    
                <?php endif; ?>

                <h3 <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>><?= the_field( 'title' ); ?></h3>
                <p <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>><?= the_field( 'description' ); ?></p>
                <?php $link = get_field( 'link' ); ?>
                <?php if ( $link ) : ?>
                    <a <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?> class="btn btn--primary" href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link['target'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a>
                <?php endif; ?>

                <?php $big_bottom_right_image = get_field( 'big_bottom_right_image' ); ?>
                <?php if ( $big_bottom_right_image ) : ?>
                    <figure class="cta__image bottom-img-4">
                        <img src="<?php echo esc_url( $big_bottom_right_image['url'] ); ?>" alt="<?php echo esc_attr( $big_bottom_right_image['alt'] ); ?>" />
                    </figure>
                <?php endif; ?>

                <?php $small_bottom_right_image = get_field( 'small_bottom_right_image' ); ?>
                <?php if ( $small_bottom_right_image ) : ?>
                    <figure class="cta__image bottom-img-3">
                        <img src="<?php echo esc_url( $small_bottom_right_image['url'] ); ?>" alt="<?php echo esc_attr( $small_bottom_right_image['alt'] ); ?>" />
                    </figure>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>