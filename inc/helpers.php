<?php

require_once get_template_directory() . '/inc/class-mcp-catalog-fetcher.php';

// Setup acf gutenberg blocks
if (function_exists('acf_register_block_type')) {
    add_action('acf/init', 'register_my_blocks');
}

function register_my_blocks() {
    $blocks = glob( STYLESHEETPATH . '/blocks/*');
    
    foreach ($blocks as $block){
        if ( is_dir( $block ) ) {
            register_block_type( $block );

            // add block php file
            require $block . "/block.php";
        }
    }
}

// register new Gutenberg blocks category
function add_custom_block_categories( $categories, $post ) {
	$custom_category_one = array(
		'slug' => "theme_block_category",
		'title' => __( wp_get_theme()->get( 'Name' ) ),
		'icon'  => 'admin-appearance',
	);
	array_unshift( $categories, $custom_category_one);

	return $categories;
}
add_filter( 'block_categories_all', 'add_custom_block_categories', 10, 2 );

// Register pattern category
if(function_exists('register_block_pattern_category')) {
    register_block_pattern_category(
        'theme_pattern_category',
        array('label' => __( wp_get_theme()->get( 'Name' )))
    );
}

//Enable Customize
add_action( 'customize_register', '__return_true' );

//remove paragraf form contact form 7
add_filter('wpcf7_autop_or_not', '__return_false');

/**
 * Beautify var_dump()
 * Kill Operation
 *
 * @param mixed $data
 * @param string $label
 * @param bool $return
 *
 * @return string|void
 */
function dd(mixed $data, string $label = '', bool $return = false)
{
    $debug           = debug_backtrace();
    $callingFile     = $debug[0]['file'];
    $callingFileLine = $debug[0]['line'];

    ob_start();
    var_dump($data);
    $c = ob_get_contents();
    ob_end_clean();

    $c = preg_replace("/\r\n|\r/", "\n", $c);
    $c = str_replace("]=>\n", '] = ', $c);
    $c = preg_replace('/= {2,}/', '= ', $c);
    $c = preg_replace("/\[\"(.*?)\"] = /i", "[$1] = ", $c);
    $c = preg_replace('/ {2}/', "    ", $c);
    $c = preg_replace("/\"\"(.*?)\"/i", "\"$1\"", $c);
    $c = preg_replace("/(int|float)\(([0-9.]+)\)/i", "$1() <span class=\"number\">$2</span>", $c);

    // Syntax Highlighting of Strings. This seems cryptic, but it will also allow non-terminated strings to get parsed.
    $c = preg_replace("/(\[[\w ]+] = string\([0-9]+\) )\"(.*?)/sim", "$1<span class=\"string\">\"", $c);
    $c = preg_replace("/(\"\n+)( *})/im", "$1</span>$2", $c);
    $c = preg_replace("/(\"\n+)( *\[)/im", "$1</span>$2", $c);
    $c = preg_replace("/(string\([0-9]+\) )\"(.*?)\"\n/sim", "$1<span class=\"string\">\"$2\"</span>\n", $c);

    $regex = array(
        // Numbers
        'numbers'  => array(
            '/(^|] = )(array|float|int|string|resource|object\(.*\)|\&amp;object\(.*\))\(([0-9\.]+)\)/i',
            '$1$2(<span class="number">$3</span>)'
        ),
        // Keywords
        'null'     => array('/(^|] = )(null)/i', '$1<span class="keyword">$2</span>'),
        'bool'     => array('/(bool)\((true|false)\)/i', '$1(<span class="keyword">$2</span>)'),
        // Types
        'types'    => array('/(of type )\((.*)\)/i', '$1(<span class="type">$2</span>)'),
        // Objects
        'object'   => array('/(object|\&amp;object)\(([\w]+)\)/i', '$1(<span class="object">$2</span>)'),
        // Function
        'function' => array(
            '/(^|] = )(array|string|int|float|bool|resource|object|\&amp;object)\(/i',
            '$1<span class="function">$2</span>('
        ),
    );

    foreach ($regex as $x) {
        $c = preg_replace($x[0], $x[1], $c);
    }

    $style = '
        /* outside div - it will float and match the screen */
        .dumper {
            margin: 2px;
            padding: 2px;
            background-color: #fbfbfb;
            float: left;
            clear: both;
            box-sizing: unset;
            white-space: unset;
        }
        /* font size and family */
        .dumper pre {
            color: #000000;
            font-size: 9pt;
            font-family: "Courier New",Courier,Monaco,monospace;
            margin: 0px;
            padding-top: 5px;
            padding-bottom: 7px;
            padding-left: 9px;
            padding-right: 9px;
            box-sizing: unset;
            white-space: pre;
        }
        /* inside div */
        .dumper div {
            background-color: #fcfcfc;
            border: 1px solid #d9d9d9;
            float: left;
            clear: both;
            box-sizing: unset;
            white-space: pre;
        }
        /* syntax highlighting */
        .dumper span.string {color: #c40000;}
        .dumper span.number {color: #ff0000;}
        .dumper span.keyword {color: #007200;}
        .dumper span.function {color: #0000c4;}
        .dumper span.object {color: #ac00ac;}
        .dumper span.type {color: #0072c4;}
        ';

    $style = preg_replace("/ {2,}/", "", $style);
    $style = preg_replace("/\t|\r\n|\r|\n/", "", $style);
    $style = preg_replace("/\/\*.*?\*\//i", '', $style);
    $style = str_replace('}', '} ', $style);
    $style = str_replace(' {', '{', $style);
    $style = trim($style);

    $c = trim($c);
    $c = preg_replace("/\n<\/span>/", "</span>\n", $c);

    if ($label == '') {
        $line1 = '';
    } else {
        $line1 = "<strong>$label</strong> \n";
    }

    $out = "\n<!-- dumper Begin -->\n" .
        "<style>" . $style . "</style>\n" .
        "<div class=\"dumper\">
        <div><pre>$line1 $callingFile : $callingFileLine \n$c\n</pre></div></div><div style=\"clear:both;\">&nbsp;</div>" .
        "\n<!-- dumper End -->\n";
    if ($return) {
        return $out;
    } else {
        echo $out;
    }

    die();
}