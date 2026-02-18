<?php

/**
 * Block template: MCP List — catalog of MCP servers with search and category filters.
 *
 * @param array  $block     Block settings and attributes.
 * @param string $content   Block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int|string $post_id Post ID this block is saved to.
 */

$id = 'mcp-list-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$classes   = 'mcp-list';
$wrapper   = get_block_wrapper_attributes( array( 'class' => $classes ) );
$data_src  = get_field( 'data_source' );
$cache_hrs = (int) get_field( 'cache_duration' );
if ( $cache_hrs < 1 ) {
	$cache_hrs = 24;
}

$servers = array();

if ( 'manual' === $data_src && have_rows( 'servers' ) ) {
	while ( have_rows( 'servers' ) ) {
		the_row();
		$icon = get_sub_field( 'icon' );
		$cat  = get_sub_field( 'category' );
		$servers[] = array(
			'name'              => (string) get_sub_field( 'server_name' ),
			'short_description' => (string) get_sub_field( 'short_description' ),
			'icon'              => $icon && ! empty( $icon['url'] ) ? $icon['url'] : '',
			'link'              => (string) get_sub_field( 'catalog_link' ),
			'categories'        => $cat ? array_map( 'trim', preg_split( '/,\s*/', $cat ) ) : array(),
		);
	}
} elseif ( 'automatic' === $data_src ) {
	$raw = MCP_Catalog_Fetcher::get_catalog( $cache_hrs );
	foreach ( $raw as $row ) {
		$servers[] = array(
			'name'              => isset( $row['name'] ) ? (string) $row['name'] : '',
			'short_description' => isset( $row['short_description'] ) ? (string) $row['short_description'] : '',
			'icon'              => isset( $row['icon'] ) ? (string) $row['icon'] : '',
			'link'              => isset( $row['link'] ) ? (string) $row['link'] : '',
			'categories'        => isset( $row['categories'] ) && is_array( $row['categories'] ) ? $row['categories'] : array(),
		);
	}
}

usort( $servers, function ( $a, $b ) {
	return strcasecmp( $a['name'], $b['name'] );
} );

$all_categories = array();
foreach ( $servers as $s ) {
	foreach ( $s['categories'] as $c ) {
		if ( '' !== $c && ! in_array( $c, $all_categories, true ) ) {
			$all_categories[] = $c;
		}
	}
}
sort( $all_categories );

$block_title            = get_field( 'block_title' );
$enable_search          = get_field( 'enable_search' );
$enable_search          = ( $enable_search === null || $enable_search === '' ) ? true : (bool) $enable_search;
$enable_category_filter = get_field( 'enable_category_filter' );
$enable_category_filter = ( $enable_category_filter === null || $enable_category_filter === '' ) ? true : (bool) $enable_category_filter;
$show_bar               = $enable_search || $enable_category_filter;
$catalog_url           = 'https://github.com/obot-platform/mcp-catalog';
$refresh_url = '';
if ( $is_preview && 'automatic' === $data_src ) {
	$redirect_to = get_edit_post_link( $post_id, 'raw' );
	if ( ! $redirect_to ) {
		$redirect_to = isset( $_GET['redirect'] ) ? sanitize_url( wp_unslash( $_GET['redirect'] ) ) : '';
	}
	$refresh_url = add_query_arg(
		array(
			'action'   => 'mcp_list_refresh_cache',
			'nonce'    => wp_create_nonce( 'mcp_list_refresh_cache' ),
			'redirect' => $redirect_to,
		),
		admin_url( 'admin-ajax.php' )
	);
}

if ( isset( $block['data']['preview_image_help'] ) ) {
	$file_url = str_replace( get_stylesheet_directory(), '', dirname( __FILE__ ) );
	echo '<img src="' . esc_url( get_stylesheet_directory_uri() . $file_url . '/' . $block['data']['preview_image_help'] ) . '" style="width:100%; height:auto;">';
	return;
}
?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper; ?>>
	<?php if ( $block_title ) : ?>
		<h2 class="mcp-list__title"><?php echo esc_html( $block_title ); ?></h2>
	<?php endif; ?>

	<?php if ( $refresh_url && $is_preview ) : ?>
		<div class="mcp-list__editor-actions" role="region" aria-label="<?php esc_attr_e( 'Catalog cache', 'oboto' ); ?>">
			<a href="<?php echo esc_url( $refresh_url ); ?>" class="mcp-list__refresh-link mcp-list__refresh-btn"><?php esc_html_e( 'Refresh catalog from GitHub', 'oboto' ); ?></a>
			<?php if ( isset( $_GET['mcp_cache_refreshed'] ) ) : ?>
				<span class="mcp-list__refresh-ok"><?php esc_html_e( 'Cache refreshed.', 'oboto' ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( empty( $servers ) ) : ?>
		<p class="mcp-list__empty"><?php esc_html_e( 'No MCP servers to display. Add servers manually or switch to Automatic and refresh the cache.', 'oboto' ); ?></p>
	<?php else : ?>
		<?php if ( $show_bar ) : ?>
		<div class="mcp-list__bar">
			<?php if ( $enable_search ) : ?>
			<div class="mcp-list__search-wrap">
				<label for="<?php echo esc_attr( $id ); ?>-search" class="mcp-list__search-label"><?php esc_html_e( 'Search', 'oboto' ); ?></label>
				<input type="search" id="<?php echo esc_attr( $id ); ?>-search" class="mcp-list__search" placeholder="<?php esc_attr_e( 'Search servers…', 'oboto' ); ?>" aria-label="<?php esc_attr_e( 'Search servers', 'oboto' ); ?>">
			</div>
			<?php endif; ?>
			<?php if ( $enable_category_filter && ! empty( $all_categories ) ) : ?>
			<div class="mcp-list__filter-wrap">
				<label for="<?php echo esc_attr( $id ); ?>-category" class="mcp-list__filter-label"><?php esc_html_e( 'Category', 'oboto' ); ?></label>
				<select id="<?php echo esc_attr( $id ); ?>-category" class="mcp-list__select" aria-label="<?php esc_attr_e( 'Filter by category', 'oboto' ); ?>">
					<option value=""><?php esc_html_e( 'All', 'oboto' ); ?></option>
					<?php foreach ( $all_categories as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php endif; ?>
			<p class="mcp-list__count" aria-live="polite"><?php echo esc_html( sprintf( _n( 'Showing %1$d of %2$d server', 'Showing %1$d of %2$d servers', count( $servers ), 'oboto' ), count( $servers ), count( $servers ) ) ); ?></p>
		</div>
		<?php endif; ?>

		<div class="mcp-list__grid" data-mcp-list-grid>
			<?php foreach ( $servers as $s ) : ?>
				<?php
				$link = $s['link'] ? $s['link'] : $catalog_url;
				$cat_attr = implode( '|', array_map( 'esc_attr', $s['categories'] ) );
				$search_text = $s['name'] . ' ' . $s['short_description'];
				?>
				<a class="mcp-list__card" href="<?php echo esc_url( $link ); ?>" data-category="<?php echo esc_attr( $cat_attr ); ?>" data-search="<?php echo esc_attr( $search_text ); ?>" target="_blank" rel="noopener noreferrer">
					<figure class="mcp-list__card-figure">
						<?php if ( $s['icon'] ) : ?>
							<img src="<?php echo esc_url( $s['icon'] ); ?>" alt="" loading="lazy">
						<?php endif; ?>
					</figure>
					<div class="mcp-list__card-body">
						<h3 class="mcp-list__card-title"><?php echo esc_html( $s['name'] ); ?></h3>
						<p class="mcp-list__card-desc"><?php echo esc_html( $s['short_description'] ); ?></p>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
