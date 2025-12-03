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
$id = 'hero-questions-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'hero-questions';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' =>  $classes
]);

 if ( get_field( 'add_animation' ) == 1 ) {
    $data_aos = get_field( 'animations' );
    $delay = "1000";
    $aos_args = 'data-aos="' . $data_aos . '" delay="'.$delay.'" data-aos-mirror="true" data-aos-once="false" data-aos-easing="ease-in-out"   data-aos-duration="500"   data-aos-anchor-placement="center-center" ';
 }

?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
       
        <div class="chat__head hero-code active">
            <h1><?= get_field( 'section_title' ); ?></h1>
            <div class="chat__head__description">
                <?= get_field( 'section_description' ); ?>
            </div>
            <div class="code-wrapper has-boxShadow">
                <p class="has-text-color has-link-color" style="color:#555555;margin-bottom:7px">Deploy Obot:</p>
                <pre >
                    <code><?= get_field( 'code' ); ?></code>
                </pre>
            </div>
        </div>
        <?php if ( have_rows( 'chat' ) ) : ?>
            <div class="chat__wrapper">
                 <div class="overlay"></div>
                 <div class="pinned-element active"></div>
                <div class="panels">
                <?php while ( have_rows( 'chat' ) ) : the_row(); ?>
                    <div class="chat__answer__question <?=get_row_Index() == 1 ? "active" : "" ?>" >
                    <p class="chat__question" ><?= get_sub_field( 'question' ); ?></p>
                    <div class="chat__answer" >
                        <p>
                            <svg width="28" height="26" viewBox="0 0 28 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9954 0.111084C13.114 0.111084 12.3892 0.845991 12.3892 1.73686C12.3905 2.32953 12.7134 2.87853 13.2287 3.16176V6.96481H7.74577C5.70571 6.96481 3.91957 8.07154 2.9396 9.71728C2.86811 9.70481 2.79661 9.69732 2.72512 9.68983C1.23359 9.68983 0.00585937 10.9326 0.00585937 12.4423C0.00585937 13.9533 1.23359 15.1948 2.72512 15.1948C2.74238 15.1935 2.75963 15.1923 2.77689 15.191C3.71249 16.994 5.59107 18.228 7.747 18.228H20.2462C22.4022 18.228 24.2807 16.994 25.2163 15.1898C25.2336 15.191 25.2509 15.1935 25.2681 15.1948C26.7596 15.1948 27.9874 13.952 27.9874 12.4423C27.9874 10.9313 26.7596 9.68983 25.2681 9.68983C25.1966 9.69607 25.1239 9.70481 25.0536 9.71728C24.0737 8.07029 22.2875 6.96357 20.2475 6.96357L14.7646 6.96357V3.16176C15.2798 2.87728 15.6028 2.33078 15.604 1.73686C15.6015 0.844744 14.8767 0.111084 13.9954 0.111084ZM7.74577 8.74656H10.1002C10.0977 8.78025 10.0952 8.81393 10.0952 8.84887C10.0952 9.65365 10.735 10.3012 11.5313 10.3012H16.4607C17.2558 10.3012 17.8968 9.65365 17.8968 8.84887C17.8968 8.81393 17.8943 8.78025 17.8918 8.74656H20.2462C22.3935 8.74656 24.0872 10.4509 24.0872 12.5958C24.0872 14.7394 22.3923 16.445 20.2462 16.445H7.74577C5.59846 16.445 3.90478 14.7394 3.90478 12.5958C3.90355 10.4509 5.59846 8.74656 7.74577 8.74656ZM7.48567 10.6868C6.45147 10.6868 5.5997 11.5477 5.5997 12.5958C5.5997 13.6426 6.45147 14.5048 7.48567 14.5048C8.51988 14.5048 9.37165 13.6426 9.37165 12.5958C9.37165 11.5477 8.51988 10.6868 7.48567 10.6868ZM20.5051 10.6868C19.4709 10.6868 18.6191 11.5477 18.6191 12.5958C18.6191 13.6426 19.4709 14.5048 20.5051 14.5048C21.5393 14.5048 22.3911 13.6426 22.3911 12.5958C22.3911 11.5477 21.5393 10.6868 20.5051 10.6868ZM11.4524 11.8309C11.4524 13.2521 12.5901 14.405 13.9941 14.405C15.3982 14.405 16.5359 13.2533 16.5359 11.8309H11.4524ZM7.36241 19.5905C7.29831 19.9636 7.26133 20.3466 7.26133 20.7384V22.6187C5.90047 23.1228 4.92789 24.4404 4.92789 25.9913H12.0305C12.0305 24.4229 11.0358 23.0928 9.65147 22.6025V20.7384C9.65147 20.3454 9.70201 19.9661 9.79569 19.6055H7.74577C7.61634 19.6055 7.48937 19.5992 7.36241 19.5905ZM20.6271 19.5905C20.5002 19.6005 20.3732 19.6055 20.245 19.6055H18.1938C18.2875 19.9661 18.3381 20.3454 18.3381 20.7384V22.6025C16.9538 23.0928 15.959 24.4229 15.959 25.9913H23.0604C23.0604 24.4404 22.0878 23.1228 20.727 22.62V20.7384C20.7282 20.3466 20.6912 19.9636 20.6271 19.5905Z" fill="#4F7EF3"/>
                            </svg>
                            <span>Obot</span>
                        </p>
                        <div><?= the_sub_field( 'answer' ); ?></div>
                    </div>
                    </div>
                <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if(get_field( 'bottom_description' )) :?>
            <div class="chat__footer">
                <?= get_field( 'bottom_description' ); ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>