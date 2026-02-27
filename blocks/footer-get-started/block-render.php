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
$id = 'footer-get-started-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}


// Create class attribute allowing for custom "className" and "align" values.
$classes = 'footer-get-started';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' =>  $classes
]);

 if ( get_field( 'add_animation' ) == 1 ) {
    $data_aos = get_field( 'animations' );
    $delay = "100";
    $aos_args = 'data-aos="' . $data_aos . '" data-aos-delay="' . $delay . '" data-aos-anchor-placement="top-bottom" data-aos-offset= "-200" data-aos-mirror="true" data-aos-once="false" data-aos-easing="ease-in-out"';
 }


?>
<?php if (isset($block['data']['preview_image_help'])) : ?>
    <?php
    $fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__),);
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else : ?>
    <section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
        <div class="get-started__wrap">
        <svg width="1440" height="945" viewBox="0 0 1440 945" fill="none" xmlns="http://www.w3.org/2000/svg" class="background-image">
            <path d="M441.019 500.494C409.525 513.67 397.563 545.968 418.148 564.406C460.498 602.348 634.846 583.606 643.049 557.887C651.131 532.549 506.736 472.993 441.019 500.494Z" stroke="url(#paint0_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M519.056 416.103C590.994 390.925 711.785 570.187 661.926 612.508C631.271 638.531 557.061 635.279 496.543 626.726C439.73 618.699 358.552 585.875 338.525 531.361C327.92 502.421 334.286 461.189 356.746 446.749C374.128 435.577 397.594 440.603 402.77 438.745C427.448 448.973 431.07 452.949 450.732 455.348C476.431 458.485 499.272 423.026 519.056 416.103Z" stroke="url(#paint1_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M592.046 327.935C675.595 341.263 766.387 603.095 675.755 663.351C618.036 701.727 517.409 684.782 447.27 664.493C382.842 645.857 275.56 583.667 253.855 494.539C241.237 442.751 256.569 369.898 300.497 348.825C335.513 332.032 380.631 346.472 388.575 347.584C435.53 372.823 439.326 385.663 474.289 394.307C520.581 405.738 557.129 322.337 592.046 327.935Z" stroke="url(#paint2_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M659.745 240.353C751.993 264.922 815.698 636.376 684.294 714.773C599.487 765.365 472.414 735.016 392.707 702.839C320.601 673.739 187.665 582.166 163.894 458.295C149.554 383.592 173.485 279.278 238.95 251.48C291.554 229.135 358.735 252.066 369.073 257.001C441.347 291.524 442.262 318.956 492.579 333.845C559.416 353.677 611.433 227.513 659.745 240.353Z" stroke="url(#paint3_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M723.374 156.754C825.532 192.198 860.769 673.267 688.761 770.186C576.753 832.734 423.286 789.317 334.08 745.183C254.213 705.68 95.7908 584.733 69.8318 426.034C53.8753 328.483 86.2992 192.67 173.31 158.102C243.532 130.228 332.243 162.923 345.485 170.379C438.648 222.837 441.057 256.346 506.713 277.374C594.112 305.605 662.223 135.544 723.374 156.754Z" stroke="url(#paint4_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M104.81 65.8367C192.621 32.4413 302.807 74.8538 319.038 84.8762C432.839 155.155 437.047 194.597 518.019 221.961C625.995 258.448 710.604 44.0554 784.089 74.2141C896.967 120.541 903.303 711.841 690.317 826.657C551.343 901.574 371.185 844.729 272.533 788.578C184.921 738.717 1.12627 588.396 -27.0817 394.84C-44.6165 274.449 -3.76058 107.122 104.81 65.8367Z" stroke="url(#paint5_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M1055.01 4.91015C1020.42 4.62075 978.938 -55.3081 1009.96 -65.3153C1027.88 -71.1033 1075.31 -61.9491 1093.94 -45.6589C1120.18 -22.7353 1078.81 5.11578 1055.01 4.91015Z" stroke="url(#paint6_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M961.007 -56.9227C964.392 -64.965 980.242 -68.5368 994.704 -78.3308C1024.04 -98.208 1028.25 -107.652 1049.08 -119.639C1057.21 -124.323 1068.14 -130.476 1078.82 -127.658C1095.16 -123.34 1099.24 -104.651 1119.99 -71.7354C1146.88 -28.965 1157.07 -17.9069 1149.38 4.75782C1147.59 10.028 1145.27 27.095 1132.32 37.5058C1114.38 51.8997 1093.36 40.7806 1067.07 50.308C1035.5 61.7318 1019.85 82.066 1006.15 73.0793C996.496 66.7506 996.137 49.4169 988.91 19.2354C983.406 -3.75671 979.944 -18.8436 973.251 -31.1508C965.223 -45.8036 957.241 -47.9818 961.007 -56.9227Z" stroke="url(#paint7_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M908.136 -40.1603C908.723 -54.4171 931.137 -61.317 949.609 -81.9178C987.492 -124.178 969.942 -154.375 1000.31 -185.249C1013.19 -198.356 1033.41 -211.052 1053.67 -208.592C1082.43 -205.096 1092.09 -175.417 1136.36 -115.245C1194.24 -36.5732 1218.9 -21.1665 1213.49 20.7586C1212.39 29.3036 1211.31 62.1125 1188.69 81.8604C1158.52 108.196 1122.36 84.2365 1075.14 104.03C1022.36 126.116 1002.64 173.334 984.847 163.563C972.488 156.777 977.223 129.452 967.724 76.1561C960.999 38.275 956.844 16.1359 943.404 -1.9974C926.441 -24.8677 907.503 -24.6773 908.136 -40.1603Z" stroke="url(#paint8_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M855.212 -23.5807C852.925 -44.0139 881.948 -54.2877 904.461 -85.6953C950.814 -150.354 910.85 -200.892 951.477 -251.05C968.867 -272.511 998.691 -291.711 1028.48 -289.708C1069.76 -286.936 1085.02 -246.496 1152.75 -158.937C1241.79 -44.5318 1280.14 -24.388 1277.62 36.5767C1277.13 48.465 1277.36 96.932 1245.08 126.055C1202.68 164.309 1151.31 127.464 1083.23 157.584C1009.2 190.332 985.479 264.411 963.538 253.886C948.481 246.606 958.125 209.319 946.529 132.909C938.524 80.1391 933.699 51.0544 913.541 26.9884C887.559 -4.02324 857.659 -1.57096 855.212 -23.5807Z" stroke="url(#paint9_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M802.165 -7.04668C797.035 -33.6335 832.584 -47.3192 859.191 -89.5108C913.915 -176.605 851.453 -247.288 902.533 -316.889C924.367 -346.667 963.874 -372.378 1003.17 -370.87C1057.01 -368.798 1077.61 -317.46 1168.98 -202.667C1288.91 -52.3304 1341.1 -27.5027 1341.56 52.3567C1341.66 67.5884 1343.16 131.668 1301.28 170.204C1246.59 220.331 1180.11 170.623 1091.13 211.101C995.833 254.465 968.105 355.474 942.108 344.172C924.344 336.419 938.906 289.14 925.213 189.624C915.905 121.958 910.438 85.9653 883.565 55.9361C848.541 16.8213 807.685 21.5431 802.165 -7.04668Z" stroke="url(#paint10_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M749.02 9.4796C757.612 44.6647 809.408 37.6581 853.466 84.8762C887.064 120.876 893.171 163.761 903.783 246.331C919.541 368.946 900.085 426.225 920.555 434.442C950.646 446.529 982.369 318.583 1098.95 264.609C1208.78 213.766 1290.51 276.444 1357.4 214.345C1408.97 166.464 1406.1 86.7725 1405.43 68.1214C1401.88 -30.6101 1335.92 -60.1442 1185.1 -246.412C1070.1 -388.439 1044.16 -450.66 977.734 -452.039C928.941 -453.052 879.73 -420.814 853.466 -382.735C791.965 -293.63 877.046 -202.766 813.822 -93.334C783.182 -40.305 741.023 -23.2532 749.02 9.4796Z" stroke="url(#paint11_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M1188.57 575.792C1179.54 571.222 1160.59 580.026 1158.91 593.742C1157.01 609.301 1178.46 622.172 1187.88 618.174C1199.03 613.437 1200.55 581.839 1188.57 575.792Z" stroke="url(#paint12_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M1214 658.461C1229.39 644.09 1236.11 617.717 1243.8 572.509C1251.43 527.683 1250.23 495.59 1243.53 493.259C1239.88 492.003 1229.53 501.835 1220.66 509.039C1204.05 522.489 1175.61 520.996 1168.32 522.451C1141.64 527.782 1133.8 537.682 1122.65 556.646C1117.49 565.404 1105.45 584.824 1100.15 607.672C1092.07 642.27 1055.85 652.932 1060.26 666.801C1063.97 678.377 1099.86 681.187 1123.13 681.515C1156.47 681.987 1190.22 680.654 1214 658.461Z" stroke="url(#paint13_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M1240.67 697.097C1265.96 671.416 1275.91 628.318 1291.23 547.72C1306.8 466.733 1306.7 411.183 1299.03 409.112C1293.27 407.528 1274.63 427.55 1258.79 441.86C1228.93 468.949 1175.47 464.494 1164.46 466.231C1114.21 473.983 1101.83 492.558 1082.36 527.667C1074.25 542.29 1052 577.932 1041.86 620.024C1026.25 684.508 952.263 701.392 958.384 725.298C963.5 745.206 1032.11 748.26 1075.29 746.622C1136.25 744.254 1197.94 740.507 1240.67 697.097Z" stroke="url(#paint14_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M1267.19 735.48C1302.41 698.498 1315.47 638.645 1338.51 522.679C1361.9 405.51 1362.99 326.633 1354.38 324.668C1346.49 322.84 1319.57 352.999 1296.8 374.452C1253.77 415.25 1175.22 407.749 1160.47 409.714C1086.69 419.553 1069.69 447.069 1041.96 498.4C1030.89 518.879 998.432 570.75 983.436 632.065C960.328 726.44 848.564 749.532 856.378 783.483C862.904 811.722 964.262 815.142 1027.33 811.402C1115.88 806.414 1205.62 800.154 1267.19 735.48Z" stroke="url(#paint15_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M1293.63 773.841C1338.8 725.557 1354.95 648.942 1385.71 497.608C1416.96 344.271 1419.25 242.089 1409.66 240.193C1399.63 238.19 1364.41 278.364 1334.74 306.976C1278.45 361.391 1174.9 350.92 1156.41 353.166C1059.09 364.963 1037.35 401.489 1001.47 469.094C987.401 495.414 944.791 563.53 924.954 644.075C894.322 768.335 744.781 797.641 754.311 841.637C762.232 878.193 896.334 881.841 979.296 876.16C1095.41 868.338 1213.24 859.801 1293.63 773.841Z" stroke="url(#paint16_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M1152.32 296.596C1031.46 310.304 1005.18 355.999 960.961 439.773C943.96 471.981 891.112 556.295 866.426 656.063C828.307 810.222 640.968 845.734 652.198 899.769C661.529 944.702 828.368 948.51 931.228 940.894C1074.95 930.232 1220.85 919.433 1320.04 812.187C1375.15 752.593 1394.4 659.238 1432.87 472.521C1471.93 283.009 1475.41 157.546 1464.89 155.703C1452.7 153.571 1409.24 203.683 1372.65 239.477C1303.09 307.502 1174.55 294.075 1152.32 296.596Z" stroke="url(#paint17_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M169.612 -69.7249C183.335 -17.1758 281.834 -41.8587 399.088 76.4988C441.103 118.911 469.273 163.273 524.881 170.173C530.492 170.866 586.199 176.951 625.515 141.233C674.978 96.2999 644.788 33.9644 702.515 -52.9701C723.778 -84.9947 727.315 -75.6957 742.921 -100.95C809.522 -208.752 707.943 -318.191 766.555 -427.668C793.535 -478.062 833.644 -489.631 829.832 -496.211C813.38 -524.443 132.53 -211.326 169.612 -69.7249Z" stroke="url(#paint18_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M540.128 98.3334C543.498 97.8764 580.229 100.618 605.426 75.364C637.286 43.4766 614.574 -0.428542 651.077 -59.7025C664.51 -81.5294 666.378 -75.6119 675.938 -92.877C716.726 -166.552 643.522 -240.67 679.369 -312.791C695.958 -346.179 722.146 -353.833 718.25 -357.093C700.632 -371.959 251.232 -152.341 286.53 -56.778C299.765 -20.938 372.016 -33.6564 454.688 43.5375C484.749 71.5866 505.104 103.078 540.128 98.3334Z" stroke="url(#paint19_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M555.376 26.4857C556.336 24.0715 574.298 24.1248 585.337 9.49483C599.67 -9.48379 584.407 -34.7683 599.639 -66.4273C605.228 -78.049 605.456 -75.5662 608.963 -84.8042C624.02 -124.536 579.04 -163.102 592.191 -197.922C598.343 -214.311 609.779 -217.106 606.73 -217.982C584.438 -214.821 370.392 -93.1436 403.411 -43.8312C416.303 -24.5631 462.449 -25.4846 510.25 10.5763C528.418 24.2847 540.311 34.3224 555.376 26.4857Z" stroke="url(#paint20_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <path d="M570.623 -45.3543C558.791 -79.892 512.21 -91.6356 495.148 -78.8639C469.989 -60.0071 491.45 -35.4537 520.306 -30.8842C541.874 -27.4952 584.163 -5.82818 570.623 -45.3543Z" stroke="url(#paint21_radial_77_1681)" stroke-opacity="0.11" stroke-width="2" stroke-miterlimit="10"/>
            <defs>
            <radialGradient id="paint0_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint1_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint2_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint3_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint4_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint5_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint6_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint7_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint8_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint9_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint10_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint11_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint12_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint13_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint14_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint15_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint16_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint17_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint18_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint19_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint20_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="paint21_radial_77_1681" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(719.5 465.348) rotate(90) scale(478.652 774.849)">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </radialGradient>
            </defs>
            </svg>

        
        <div class="get-started__actions">
            <?php $get_started_link = get_field( 'get_started_link' ); ?>
            <?php if ( $get_started_link ) : ?>
                <h2 class="get-started-title" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>Get started with Obot</h2>
                <a <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?> class="btn btn--primary" href="<?php echo esc_url( $get_started_link['url'] ); ?>" target="<?php echo esc_attr( $get_started_link['target'] ); ?>"><?php echo esc_html( $get_started_link['title'] ); ?></a>
            <?php endif; ?>
       
            <?php if ( have_rows( 'join_comunity_buttons' ) ) : ?>
                <div class="join-community">
                <h2 class="join-community__title" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>Join the Obot community</h2>
                <div>
                <?php while ( have_rows( 'join_comunity_buttons' ) ) : the_row(); ?>

                    <?php $icon = get_sub_field( 'icon' ); ?>
                    <?php $link = get_sub_field( 'link' ); ?>
                    <?php if ( $link ) : ?>
                        <a <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?> href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link['target'] ); ?>">
                            <?php if ( $icon ) : ?>
                                <span><img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" /></span>    
                            <?php endif; ?>
                            <span><?php echo esc_html( $link['title'] ); ?></span>
                           
                        </a>
                    <?php endif; ?>
                <?php endwhile; ?>
                </div>
                </div>
            <?php endif; ?>
         </div>
         <div class="get-started__overview" <?= get_field( 'add_animation' ) == 1 ? $aos_args : "";?>>
            <?= get_field( 'overview' ); ?>
         </div>
         </div>
    </section>
<?php endif; ?>