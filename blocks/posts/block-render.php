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
$id = 'posts-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'posts';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $classes
]);

 if ( get_field( 'add_animation' ) == 1 ) {
    $data_aos = get_field( 'animations' );
    $delay = "100";
    $aos_args = 'data-aos="' . $data_aos . '" data-aos-delay="' . $delay . '" data-aos-mirror="true" data-aos-once="false" data-aos-easing="ease-in-out"';
 }


?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>

        <div class="posts__header" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>
             <h2 class="section-title"><?= get_field( 'title' ); ?></h2>
             <?php $read_all = get_field( 'read_all' ); ?>
            <?php if ( $read_all ) : ?>
                <a href="<?php echo esc_url( $read_all['url'] ); ?>" target="<?php echo esc_attr( $read_all['target'] ); ?>">
                    <span><?php echo esc_html( $read_all['title'] ); ?></span>
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M8.29289 4.29289C7.90237 4.68342 7.90237 5.31658 8.29289 5.70711L14.5858 12L8.29289 18.2929C7.90237 18.6834 7.90237 19.3166 8.29289 19.7071C8.68342 20.0976 9.31658 20.0976 9.70711 19.7071L16.7071 12.7071C17.0976 12.3166 17.0976 11.6834 16.7071 11.2929L9.70711 4.29289C9.31658 3.90237 8.68342 3.90237 8.29289 4.29289Z" fill="#00919D"/>
                    </svg>
                </a>
            <?php endif; ?>
           
        </div>
       
       
       
        <div class="posts_list" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>
            <?php $posts = get_field( 'posts' ); ?>
            <?php if ( $posts ) : ?>
                <div class="swiper articleSwiper">
                    <div class="swiper-wrapper">
                    <?php foreach ( $posts as $post_ids ) : ?>
                    
                            <article class="post_item swiper-slide">
                                <figure class="post_image">
                                    <?php $url = wp_get_attachment_url( get_post_thumbnail_id($post_ids), 'thumbnail' ); ?>
                                    <img src="<?php echo $url ?>" />
                                </figure>
                                <div class="post_data">
                                    <h3><?= get_the_title(); ?></h3>
                                    <p class="post_description">
                                        <?= get_the_excerpt($post_ids); ?>
                                    </p>
                                    <a href="<?= get_permalink( $post_ids );?>">
                                        <span>Read More</span>
                                        <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.910826 0.578062C0.585389 0.903499 0.585389 1.43114 0.910826 1.75657L6.1549 7.00065L0.910826 12.2447C0.585389 12.5702 0.585389 13.0978 0.910826 13.4232C1.23626 13.7487 1.7639 13.7487 2.08934 13.4232L7.92267 7.58991C8.24811 7.26447 8.24811 6.73683 7.92267 6.4114L2.08934 0.578062C1.7639 0.252625 1.23626 0.252625 0.910826 0.578062Z" fill="#00919D"/>
                                        </svg>

                                    </a>
                                </div>
                            </article>
                            
                    <?php endforeach; ?>
                    </div>
                </div>
            <?php else : ?>
                 <?php
                    $args = array(
                        'post_type' => "post",
                        'post_status' => array( 'publish' ),
                        'posts_per_page'=> -1,
                        'order' => 'DESC',
                        'orderby' => 'date'
                    );
                    $the_query = new WP_Query( $args ); 
                ?>
                <?php if ( $the_query->have_posts() ) : ?>
                    <div class="swiper articleSwiper">
                        <div class="swiper-wrapper">
                    <?php
                        while ( $the_query->have_posts() ) :
                            $the_query->the_post();
                            $post_ids = get_the_ID();
                    ?>
                        <article class="post_item swiper-slide">
                            <figure class="post_image">
                                <?php $url = wp_get_attachment_url( get_post_thumbnail_id($post_ids), 'thumbnail' ); ?>
                                <img src="<?php echo $url ?>" />
                            </figure>
                            <div class="post_data">
                                <h3><?= get_the_title(); ?></h3>
                                <p class="post_description">
                                    <?= get_the_excerpt($post_ids); ?>
                                </p>
                                <a href="<?= get_permalink( $post_ids );?>">
                                    <span>Read More</span>
                                    <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.910826 0.578062C0.585389 0.903499 0.585389 1.43114 0.910826 1.75657L6.1549 7.00065L0.910826 12.2447C0.585389 12.5702 0.585389 13.0978 0.910826 13.4232C1.23626 13.7487 1.7639 13.7487 2.08934 13.4232L7.92267 7.58991C8.24811 7.26447 8.24811 6.73683 7.92267 6.4114L2.08934 0.578062C1.7639 0.252625 1.23626 0.252625 0.910826 0.578062Z" fill="#00919D"/>
                                    </svg>

                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                        </div>
                         <div class="article-swiper-pagination"></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>