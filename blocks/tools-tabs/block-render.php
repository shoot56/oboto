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
$id = 'tools-tabs-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'tools-tabs';

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
        <h3><?= get_field( 'title' ); ?></h3>
        <?php if ( have_rows( 'tabs' ) ) : ?>
        <div class="tabs-container">
            <div class="tabs__wrap">
            <div class="tabs" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>
                <?php while ( have_rows( 'tabs' ) ) : the_row(); ?>
                    <div class="tab <?=get_row_Index() == 1 ? "active" : "";?>" id="<?=$id?>--<?=get_row_Index();?>" onclick="openTab(event, '<?=$id?>--content--<?=get_row_Index();?>')">
                        <div class="tab__head">
                            <?php $icon = get_sub_field( 'icon' ); ?>
                            <?php if ( $icon ) : ?>
                                <figure>
                                    <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                </figure>
                            <?php endif; ?>
                            <h4><?= get_sub_field( 'title' ); ?></h4>
                        </div>
                        <p class="tab__description"><?= get_sub_field( 'desctiption' ); ?></p>
                       
                    </div>
                <?php endwhile; ?>
            </div>
            </div>
            <div class="tab-content" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>
                <?php while ( have_rows( 'tabs' ) ) : the_row(); ?>
                <div class="content <?=get_row_Index() == 1 ? "active" : "";?>" id="<?=$id?>--content--<?=get_row_Index();?>">
                   <?= get_sub_field( 'main_content' ); ?>
                </div>
                <?php endwhile; ?>
            </div>
            
        </div>
        <?php endif; ?>
         
    </section>
<?php endif; ?>