<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'latest-posts-v2-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-latest-posts-v2';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow            = trim( (string) get_field( 'eyebrow' ) );
$title               = trim( (string) get_field( 'title' ) );
$selected_posts      = get_field( 'posts' );
$included_categories = get_field( 'categories' );
$excluded_categories = get_field( 'exclude_categories' );
$post_limit          = 3;

$normalize_ids = static function ( $values, $object_property = 'ID' ) {
	$ids = array();

	foreach ( (array) $values as $value ) {
		if ( is_object( $value ) && isset( $value->{$object_property} ) ) {
			$value = $value->{$object_property};
		}

		$value = absint( $value );
		if ( $value ) {
			$ids[] = $value;
		}
	}

	return array_values( array_unique( $ids ) );
};

$selected_post_ids      = $normalize_ids( $selected_posts );
$included_category_ids  = $normalize_ids( $included_categories, 'term_id' );
$excluded_category_ids  = $normalize_ids( $excluded_categories, 'term_id' );
$displayed_post_ids     = array();

if ( $selected_post_ids ) {
	foreach ( $selected_post_ids as $selected_post_id ) {
		if ( count( $displayed_post_ids ) >= $post_limit ) {
			break;
		}

		if ( 'post' !== get_post_type( $selected_post_id ) || 'publish' !== get_post_status( $selected_post_id ) ) {
			continue;
		}

		if ( $excluded_category_ids && has_category( $excluded_category_ids, $selected_post_id ) ) {
			continue;
		}

		$displayed_post_ids[] = $selected_post_id;
	}
} else {
	$query_args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $post_limit,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'fields'              => 'ids',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	$tax_query = array();

	if ( $included_category_ids ) {
		$tax_query[] = array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $included_category_ids,
		);
	}

	if ( $excluded_category_ids ) {
		$tax_query[] = array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $excluded_category_ids,
			'operator' => 'NOT IN',
		);
	}

	if ( $tax_query ) {
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		$query_args['tax_query'] = $tax_query;
	}

	$posts_query        = new WP_Query( $query_args );
	$displayed_post_ids = array_map( 'absint', $posts_query->posts );
}

$has_header = $eyebrow || $title;

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-latest-posts-v2__inner">
		<div class="obot-latest-posts-v2__divider" aria-hidden="true"></div>

		<?php if ( $has_header ) : ?>
			<header class="obot-latest-posts-v2__header">
				<?php if ( $eyebrow ) : ?>
					<div class="obot-latest-posts-v2__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>><?php echo esc_html( $eyebrow ); ?></div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="obot-latest-posts-v2__title"<?php oboto_the_aos_attributes( 180 ); ?>><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $displayed_post_ids ) : ?>
			<div class="obot-latest-posts-v2__grid">
				<?php foreach ( $displayed_post_ids as $index => $displayed_post_id ) : ?>
					<?php
					$category_names = wp_list_pluck( get_the_category( $displayed_post_id ), 'name' );
					$post_title     = get_the_title( $displayed_post_id );
					$post_excerpt   = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $displayed_post_id ) ), 24, '...' );
					$post_date      = get_the_date( 'M j, Y', $displayed_post_id );
					$post_date_iso  = get_the_date( DATE_W3C, $displayed_post_id );
					?>
					<article class="obot-latest-posts-v2__card"<?php oboto_the_aos_attributes( 260 + ( $index * 70 ) ); ?>>
						<a class="obot-latest-posts-v2__card-link" href="<?php echo esc_url( get_permalink( $displayed_post_id ) ); ?>">
							<div class="obot-latest-posts-v2__content">
								<div class="obot-latest-posts-v2__meta">
									<?php if ( $category_names ) : ?>
										<span><?php echo esc_html( implode( ', ', $category_names ) ); ?></span>
										<span aria-hidden="true">&middot;</span>
									<?php endif; ?>
									<time datetime="<?php echo esc_attr( $post_date_iso ); ?>"><?php echo esc_html( $post_date ); ?></time>
								</div>

								<h3 class="obot-latest-posts-v2__card-title"><?php echo esc_html( $post_title ); ?></h3>

								<?php if ( $post_excerpt ) : ?>
									<p class="obot-latest-posts-v2__excerpt"><?php echo esc_html( $post_excerpt ); ?></p>
								<?php endif; ?>

								<span class="obot-latest-posts-v2__read-more">
									<span><?php esc_html_e( 'Read more', 'oboto' ); ?></span>
									<span class="obot-latest-posts-v2__arrow" aria-hidden="true"></span>
								</span>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-latest-posts-v2__placeholder">
				<?php esc_html_e( 'No published posts match the current block settings.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
