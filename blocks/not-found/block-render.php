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
$id = 'not-found-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'not-found';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' =>  $classes
]);

if (get_field('add_animation') == 1) {
    $data_aos = get_field('animations');
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
        <div class="not-found__wrap">

            <div class="not-found__content">
                <h1>Oops!</h1>
                <p>Sorry we couldn't find the page you're looking for</p>

                <a class="btn btn--primary" href="/">
                    <span>Go to Home Page</span>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                </a>
            </div>
            <p class="not-found__letters">
                <span>4</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="362" height="331" viewBox="0 0 362 331" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M181 0C169.599 0 160.212 9.39315 160.212 20.8017C160.233 28.3835 164.409 35.3991 171.073 39.0165V87.6507H100.149C73.7583 87.6507 50.6406 101.799 37.9652 122.861C37.0423 122.708 36.1143 122.601 35.1864 122.514C15.8826 122.514 0 138.402 0 157.724C0 177.041 15.8826 192.935 35.1864 192.935C35.4107 192.919 35.63 192.899 35.8543 192.884C47.9638 215.94 72.2593 231.727 100.149 231.727H261.851C289.746 231.727 314.041 215.94 326.151 192.863C326.375 192.878 326.594 192.914 326.814 192.935C346.117 192.935 362 177.046 362 157.724C362 138.407 346.117 122.514 326.814 122.514C325.881 122.601 324.958 122.703 324.035 122.861C311.359 101.799 288.242 87.6354 261.851 87.6354H190.922V39.0165C197.586 35.3786 201.762 28.3835 201.782 20.8017C201.782 9.39315 192.401 0 181 0ZM100.149 110.452H130.604C130.574 110.881 130.538 111.315 130.538 111.764C130.538 122.05 138.818 130.341 149.107 130.341H212.887C223.177 130.341 231.457 122.05 231.457 111.764C231.457 111.315 231.421 110.886 231.391 110.452H261.846C289.623 110.452 311.543 132.254 311.543 159.689C311.543 187.108 289.623 208.925 261.846 208.925H100.149C72.3714 208.925 50.452 187.108 50.452 159.689C50.452 132.254 72.3714 110.452 100.149 110.452ZM96.7842 167C83.3949 167 72.3816 146.28 72.3816 159.689C72.3816 173.082 83.3949 184.108 96.7842 184.108C110.173 184.108 121.187 173.077 121.187 159.689C121.187 146.28 110.168 167 96.7842 167ZM265.216 167C251.827 167 240.813 146.28 240.813 159.689C240.813 173.082 251.827 184.108 265.216 184.108C278.605 184.108 289.618 173.077 289.618 159.689C289.618 146.28 278.6 167 265.216 167ZM95.1985 249.135C94.3725 253.911 93.8983 258.804 93.8983 263.819V287.861C76.2975 294.305 63.7138 311.163 63.7138 331H155.588C155.588 310.938 142.714 293.927 124.802 287.657V263.824C124.802 258.794 125.465 253.952 126.673 249.329H100.149C98.4821 249.324 96.8352 249.253 95.1985 249.135ZM266.801 249.135C265.165 249.258 263.518 249.324 261.851 249.324H235.327C236.535 253.942 237.198 258.784 237.198 263.819V287.652C219.281 293.922 206.412 310.933 206.412 330.995H298.291C298.291 311.158 285.708 294.305 268.107 287.876V263.819C268.102 258.804 267.627 253.906 266.801 249.135Z" fill="#4F7DF3" />
                    <path d="M149.107 192.04H212.887V180.725V173.41H149.107V180.725V192.04Z" fill="#4F7DF3" />
                </svg>
                <span>4</span>
            </p>

        </div>
    </section>
<?php endif; ?>