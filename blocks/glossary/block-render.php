<?php

/**
 * Block template file: block-render.php
 *
 * Renders the glossary: hero with search, sticky alphabet navigation,
 * featured concepts, the A-Z term list and an optional sidebar.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'glossary-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'glossary' ) );

/**
 * Read a text field with a fallback, so a freshly inserted block still renders
 * a meaningful preview before the editor fills the fields in.
 */
$glossary_text = function ( $field_name, $default_value = '' ) {
	$value = get_field( $field_name );

	return ( is_string( $value ) && '' !== trim( $value ) ) ? $value : $default_value;
};

/**
 * Read a true/false field, treating "never saved" as the documented default.
 */
$glossary_flag = function ( $field_name, $default_value = true ) {
	$value = get_field( $field_name );

	return ( null === $value || '' === $value ) ? (bool) $default_value : (bool) $value;
};

// --- Data sources -----------------------------------------------------------

$term_sources = get_field( 'term_sources' );
if ( empty( $term_sources ) || ! is_array( $term_sources ) ) {
	$term_sources = array_map( 'trim', explode( ',', OBOTO_GLOSSARY_DEFAULT_TAXONOMIES ) );
}

$group_mode = get_field( 'group_mode' );
if ( 'parent' !== $group_mode ) {
	$group_mode = 'source';
}

$exclude_slugs = array_filter(
	array_map(
		'sanitize_title',
		preg_split( '/[\s,]+/', (string) get_field( 'exclude_slugs' ) )
	)
);

$entries = oboto_glossary_collect_terms(
	array(
		'taxonomies'          => $term_sources,
		'hide_empty'          => $glossary_flag( 'hide_empty', false ),
		'require_description' => $glossary_flag( 'require_description', false ),
		'exclude_slugs'       => $exclude_slugs,
		'group_mode'          => $group_mode,
	)
);

$entries_by_letter = oboto_glossary_group_by_letter( $entries );
$available_letters = array_keys( $entries_by_letter );
$filter_groups     = oboto_glossary_get_groups( $entries );
$total_terms       = count( $entries );

// --- Presentation options ---------------------------------------------------

$eyebrow            = $glossary_text( 'eyebrow', __( 'Learning Center', 'oboto' ) );
$title              = $glossary_text( 'title', __( 'AI & MCP Glossary', 'oboto' ) );
$title_tag          = get_field( 'title_tag' );
$title_tag          = in_array( $title_tag, array( 'h1', 'h2' ), true ) ? $title_tag : 'h1';
$intro              = $glossary_text( 'intro' );
$enable_search      = $glossary_flag( 'enable_search', true );
$search_placeholder = $glossary_text( 'search_placeholder', __( 'Search terms, for example “MCP gateway”', 'oboto' ) );
$show_alphabet      = $glossary_flag( 'show_alphabet', true );
$show_filters       = $glossary_flag( 'show_filters', true ) && count( $filter_groups ) > 1;
$show_counts        = $glossary_flag( 'show_counts', false );
$link_terms         = $glossary_flag( 'link_terms', true );
$browse_eyebrow     = $glossary_text( 'browse_eyebrow', __( 'Browse', 'oboto' ) );
$browse_title       = $glossary_text( 'browse_title', __( 'All glossary terms', 'oboto' ) );
$empty_text         = $glossary_text( 'empty_text', __( 'No terms match your search. Try a shorter phrase or another category.', 'oboto' ) );
$enable_schema      = $glossary_flag( 'enable_schema', true );

$alphabet_letters = range( 'A', 'Z' );
if ( in_array( OBOTO_GLOSSARY_OTHER_LETTER, $available_letters, true ) ) {
	$alphabet_letters[] = OBOTO_GLOSSARY_OTHER_LETTER;
}

// --- Hero meta items --------------------------------------------------------

$meta_items = array();
if ( have_rows( 'meta_items' ) ) {
	while ( have_rows( 'meta_items' ) ) {
		the_row();
		$meta_text = trim( (string) get_sub_field( 'text' ) );

		if ( '' === $meta_text ) {
			continue;
		}

		// {count} is replaced with the number of rendered terms.
		$meta_items[] = str_replace( '{count}', number_format_i18n( $total_terms ), $meta_text );
	}
}

// --- Featured concepts ------------------------------------------------------

$show_featured     = $glossary_flag( 'featured_enabled', true );
$featured_eyebrow  = $glossary_text( 'featured_eyebrow', __( 'Start here', 'oboto' ) );
$featured_title    = $glossary_text( 'featured_title', __( 'Essential concepts', 'oboto' ) );
$featured_subtitle = $glossary_text( 'featured_subtitle' );
$featured_cards    = array();

if ( $show_featured && have_rows( 'featured_terms' ) ) {
	while ( have_rows( 'featured_terms' ) ) {
		the_row();

		$reference     = (string) get_sub_field( 'term_reference' );
		$linked_entry  = oboto_glossary_find_entry( $entries, $reference );
		$card_title    = trim( (string) get_sub_field( 'title' ) );
		$card_text     = trim( (string) get_sub_field( 'description' ) );
		$card_tag      = trim( (string) get_sub_field( 'tag' ) );
		$card_link     = trim( (string) get_sub_field( 'link' ) );
		$card_icon     = trim( (string) get_sub_field( 'icon' ) );

		if ( '' === $card_title && $linked_entry ) {
			$card_title = $linked_entry['name'];
		}

		if ( '' === $card_text && $linked_entry ) {
			$card_text = $linked_entry['plain_text'];
		}

		if ( '' === $card_tag && $linked_entry ) {
			$card_tag = $linked_entry['group'];
		}

		if ( '' === $card_link && $linked_entry ) {
			$card_link = '' !== $linked_entry['link'] ? $linked_entry['link'] : '#' . $linked_entry['anchor'];
		}

		if ( '' === $card_title ) {
			continue;
		}

		$featured_cards[] = array(
			'title'       => $card_title,
			'description' => $card_text,
			'tag'         => $card_tag,
			'link'        => $card_link,
			'icon'        => $card_icon,
		);
	}
}

$show_featured = $show_featured && ! empty( $featured_cards );

// --- Sidebar ----------------------------------------------------------------

$show_sidebar   = $glossary_flag( 'show_sidebar', true );
$popular_title  = $glossary_text( 'popular_title', __( 'Popular terms', 'oboto' ) );
$popular_mode   = get_field( 'popular_mode' );
$popular_mode   = ( 'manual' === $popular_mode ) ? 'manual' : 'auto';
$popular_count  = (int) get_field( 'popular_count' );
$popular_count  = $popular_count > 0 ? $popular_count : OBOTO_GLOSSARY_DEFAULT_POPULAR_COUNT;
$popular_terms  = array();

if ( $show_sidebar && 'manual' === $popular_mode && have_rows( 'popular_terms' ) ) {
	while ( have_rows( 'popular_terms' ) ) {
		the_row();
		$popular_entry = oboto_glossary_find_entry( $entries, (string) get_sub_field( 'term_reference' ) );

		if ( $popular_entry ) {
			$popular_terms[] = $popular_entry;
		}
	}
} elseif ( $show_sidebar && ! empty( $entries ) ) {
	$popular_terms = $entries;

	usort(
		$popular_terms,
		function ( $first, $second ) {
			return $second['count'] <=> $first['count'];
		}
	);

	$popular_terms = array_slice( $popular_terms, 0, $popular_count );
}

$show_cta   = $glossary_flag( 'show_cta', true );
$cta_title  = $glossary_text( 'cta_title' );
$cta_text   = $glossary_text( 'cta_text' );
$cta_link   = get_field( 'cta_link' );
$cta_url    = ( is_array( $cta_link ) && ! empty( $cta_link['url'] ) ) ? $cta_link['url'] : '';
$cta_label  = ( is_array( $cta_link ) && ! empty( $cta_link['title'] ) ) ? $cta_link['title'] : __( 'Learn more', 'oboto' );
$cta_target = ( is_array( $cta_link ) && ! empty( $cta_link['target'] ) ) ? $cta_link['target'] : '';
$show_cta   = $show_cta && ( '' !== $cta_title || '' !== $cta_url );

$show_sidebar = $show_sidebar && ( ! empty( $popular_terms ) || $show_cta );

// --- Structured data --------------------------------------------------------

$schema = null;
if ( $enable_schema && ! $is_preview && ! is_admin() ) {
	$schema = oboto_glossary_build_schema( $entries, $title, (string) get_permalink( $post_id ) );
}

$arrow_icon = '<svg class="glossary__arrow-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 18V6M18 6H6M18 6L6 17.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?> data-glossary data-total="<?php echo esc_attr( $total_terms ); ?>">

	<div class="glossary__hero">
		<?php if ( '' !== $eyebrow ) : ?>
			<p class="glossary__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<<?php echo esc_html( $title_tag ); ?> class="glossary__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>

		<?php if ( '' !== $intro ) : ?>
			<p class="glossary__intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( $enable_search ) : ?>
			<div class="glossary__search-shell">
				<div class="glossary__search-box">
					<svg class="glossary__search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
						<circle cx="9" cy="9" r="6.25" stroke="currentColor" stroke-width="1.5"/>
						<path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
					</svg>
					<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>-search"><?php esc_html_e( 'Search glossary terms', 'oboto' ); ?></label>
					<input
						type="search"
						id="<?php echo esc_attr( $id ); ?>-search"
						class="glossary__search-input"
						data-glossary-search
						placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
						autocomplete="off"
						role="combobox"
						aria-expanded="false"
						aria-autocomplete="list"
						aria-controls="<?php echo esc_attr( $id ); ?>-suggestions"
					/>
					<span class="glossary__shortcut" aria-hidden="true">⌘ K</span>
				</div>
				<div
					id="<?php echo esc_attr( $id ); ?>-suggestions"
					class="glossary__suggestions"
					data-glossary-suggestions
					data-empty-label="<?php echo esc_attr__( 'No matching terms', 'oboto' ); ?>"
					role="listbox"
					hidden
				></div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $meta_items ) ) : ?>
			<ul class="glossary__meta">
				<?php foreach ( $meta_items as $meta_item ) : ?>
					<li class="glossary__meta-item"><?php echo esc_html( $meta_item ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<?php if ( $show_alphabet && ! empty( $entries ) ) : ?>
		<div class="glossary__alphabet-wrap">
			<nav class="glossary__alphabet" data-glossary-alphabet aria-label="<?php esc_attr_e( 'Alphabet navigation', 'oboto' ); ?>">
				<?php foreach ( $alphabet_letters as $letter ) : ?>
					<?php $letter_has_terms = in_array( $letter, $available_letters, true ); ?>
					<button
						type="button"
						class="glossary__letter<?php echo $letter_has_terms ? '' : ' is-disabled'; ?>"
						data-glossary-letter="<?php echo esc_attr( $letter ); ?>"
						<?php disabled( ! $letter_has_terms ); ?>
					><?php echo esc_html( $letter ); ?></button>
				<?php endforeach; ?>
			</nav>
		</div>
	<?php endif; ?>

	<?php if ( $show_featured ) : ?>
		<section class="glossary__featured">
			<div class="glossary__section-heading">
				<div>
					<?php if ( '' !== $featured_eyebrow ) : ?>
						<p class="glossary__eyebrow"><?php echo esc_html( $featured_eyebrow ); ?></p>
					<?php endif; ?>
					<h2 class="glossary__section-title"><?php echo esc_html( $featured_title ); ?></h2>
				</div>
				<?php if ( '' !== $featured_subtitle ) : ?>
					<p class="glossary__section-note"><?php echo esc_html( $featured_subtitle ); ?></p>
				<?php endif; ?>
			</div>

			<div class="glossary__featured-grid">
				<?php foreach ( $featured_cards as $card ) : ?>
					<a class="glossary__card" href="<?php echo esc_url( $card['link'] ? $card['link'] : '#' . $id ); ?>">
						<?php if ( '' !== $card['icon'] ) : ?>
							<span class="glossary__card-icon" aria-hidden="true"><?php echo esc_html( $card['icon'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $card['tag'] ) : ?>
							<span class="glossary__card-tag"><?php echo esc_html( $card['tag'] ); ?></span>
						<?php endif; ?>
						<h3 class="glossary__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
						<?php if ( '' !== $card['description'] ) : ?>
							<p class="glossary__card-desc"><?php echo esc_html( $card['description'] ); ?></p>
						<?php endif; ?>
						<span class="glossary__card-arrow">
							<?php esc_html_e( 'Explore term', 'oboto' ); ?>
							<?php echo $arrow_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG. ?>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<div class="glossary__layout<?php echo $show_sidebar ? '' : ' glossary__layout--full'; ?>">
		<div class="glossary__main">
			<div class="glossary__section-heading">
				<div>
					<?php if ( '' !== $browse_eyebrow ) : ?>
						<p class="glossary__eyebrow"><?php echo esc_html( $browse_eyebrow ); ?></p>
					<?php endif; ?>
					<h2 class="glossary__section-title"><?php echo esc_html( $browse_title ); ?></h2>
				</div>
				<p
					class="glossary__count"
					data-glossary-count
					data-label-all="<?php echo esc_attr__( '%s terms', 'oboto' ); ?>"
					data-label-filtered="<?php echo esc_attr__( 'Showing %1$s of %2$s terms', 'oboto' ); ?>"
					aria-live="polite"
				>
					<?php
					printf(
						/* translators: %s: number of glossary terms. */
						esc_html( _n( '%s term', '%s terms', $total_terms, 'oboto' ) ),
						esc_html( number_format_i18n( $total_terms ) )
					);
					?>
				</p>
			</div>

			<?php if ( $show_filters ) : ?>
				<div class="glossary__chips" data-glossary-chips role="group" aria-label="<?php esc_attr_e( 'Filter terms by category', 'oboto' ); ?>">
					<button type="button" class="glossary__chip is-active" data-glossary-group="all" aria-pressed="true"><?php esc_html_e( 'All terms', 'oboto' ); ?></button>
					<?php foreach ( $filter_groups as $filter_group ) : ?>
						<button type="button" class="glossary__chip" data-glossary-group="<?php echo esc_attr( $filter_group ); ?>" aria-pressed="false"><?php echo esc_html( $filter_group ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="glossary__list" data-glossary-list>
				<?php foreach ( $entries_by_letter as $letter => $letter_entries ) : ?>
					<section class="glossary__letter-section" data-glossary-letter-section="<?php echo esc_attr( $letter ); ?>" id="<?php echo esc_attr( $id . '-letter-' . sanitize_title( $letter ) ); ?>">
						<h3 class="glossary__letter-title"><?php echo esc_html( $letter ); ?></h3>

						<?php foreach ( $letter_entries as $entry ) : ?>
							<?php
							$term_tag        = ( $link_terms && '' !== $entry['link'] ) ? 'a' : 'div';
							$term_attributes = array(
								'class="glossary__term"',
								'id="' . esc_attr( $entry['anchor'] ) . '"',
								'data-glossary-term',
								'data-glossary-name="' . esc_attr( $entry['name'] ) . '"',
								'data-glossary-group="' . esc_attr( $entry['group'] ) . '"',
								'data-glossary-search="' . esc_attr( strtolower( $entry['name'] . ' ' . $entry['plain_text'] ) ) . '"',
							);

							if ( 'a' === $term_tag ) {
								$term_attributes[] = 'href="' . esc_url( $entry['link'] ) . '"';
							}
							?>
							<<?php echo esc_html( $term_tag ) . ' ' . implode( ' ', $term_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes escaped individually above. ?>>
								<div class="glossary__term-head">
									<span class="glossary__term-name"><?php echo esc_html( $entry['name'] ); ?></span>
									<?php if ( $show_counts && $entry['count'] > 0 ) : ?>
										<span class="glossary__term-count"><?php echo esc_html( number_format_i18n( $entry['count'] ) ); ?></span>
									<?php endif; ?>
								</div>
								<div class="glossary__term-body">
									<?php if ( '' !== $entry['plain_text'] ) : ?>
										<p class="glossary__term-desc"><?php echo esc_html( $entry['plain_text'] ); ?></p>
									<?php else : ?>
										<p class="glossary__term-desc glossary__term-desc--empty"><?php esc_html_e( 'Definition coming soon.', 'oboto' ); ?></p>
									<?php endif; ?>
								</div>
								<?php if ( 'a' === $term_tag ) : ?>
									<span class="glossary__term-arrow" aria-hidden="true">
										<?php echo $arrow_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG. ?>
									</span>
								<?php endif; ?>
							</<?php echo esc_html( $term_tag ); ?>>
						<?php endforeach; ?>
					</section>
				<?php endforeach; ?>
			</div>

			<p class="glossary__empty" data-glossary-empty hidden><?php echo esc_html( $empty_text ); ?></p>

			<?php if ( empty( $entries ) ) : ?>
				<p class="glossary__empty is-visible">
					<?php esc_html_e( 'No glossary terms found. Pick at least one taxonomy in the block settings and make sure its terms have descriptions.', 'oboto' ); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( $show_sidebar ) : ?>
			<aside class="glossary__sidebar">
				<div class="glossary__sidebar-inner">
					<?php if ( ! empty( $popular_terms ) ) : ?>
						<div class="glossary__side-block">
							<p class="glossary__side-title"><?php echo esc_html( $popular_title ); ?></p>
							<?php foreach ( $popular_terms as $popular_term ) : ?>
								<a class="glossary__side-link" href="<?php echo esc_url( '' !== $popular_term['link'] ? $popular_term['link'] : '#' . $popular_term['anchor'] ); ?>">
									<span><?php echo esc_html( $popular_term['name'] ); ?></span>
									<span class="glossary__side-arrow" aria-hidden="true">→</span>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_cta ) : ?>
						<div class="glossary__side-cta">
							<?php if ( '' !== $cta_title ) : ?>
								<h3 class="glossary__side-cta-title"><?php echo esc_html( $cta_title ); ?></h3>
							<?php endif; ?>
							<?php if ( '' !== $cta_text ) : ?>
								<p class="glossary__side-cta-text"><?php echo esc_html( $cta_text ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== $cta_url ) : ?>
								<a class="glossary__side-cta-link" href="<?php echo esc_url( $cta_url ); ?>" <?php echo $cta_target ? 'target="' . esc_attr( $cta_target ) . '" rel="noopener noreferrer"' : ''; ?>>
									<?php echo esc_html( $cta_label ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</aside>
		<?php endif; ?>
	</div>

	<?php if ( $schema ) : ?>
		<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php endif; ?>
</section>
