<?php

/**
 * Glossary block.
 *
 * Registers block assets and provides the term-collection helpers used by
 * `block-render.php`. Glossary entries are built from existing public
 * taxonomies (by default Learning Center categories and blog tags), so the
 * glossary stays in sync with the content the editors already maintain.
 */

if ( ! defined( 'OBOTO_GLOSSARY_DEFAULT_TAXONOMIES' ) ) {
	define( 'OBOTO_GLOSSARY_DEFAULT_TAXONOMIES', 'learning-center-category,post_tag' );
}

if ( ! defined( 'OBOTO_GLOSSARY_DEFAULT_POPULAR_COUNT' ) ) {
	define( 'OBOTO_GLOSSARY_DEFAULT_POPULAR_COUNT', 4 );
}

/**
 * Bucket used for terms that do not start with a latin letter.
 */
if ( ! defined( 'OBOTO_GLOSSARY_OTHER_LETTER' ) ) {
	define( 'OBOTO_GLOSSARY_OTHER_LETTER', '#' );
}

/**
 * Taxonomies that are never useful as glossary sources.
 */
if ( ! defined( 'OBOTO_GLOSSARY_EXCLUDED_TAXONOMIES' ) ) {
	define( 'OBOTO_GLOSSARY_EXCLUDED_TAXONOMIES', 'post_format,nav_menu,link_category,wp_theme,wp_template_part_area' );
}

/**
 * Register block style and view script.
 * Runs on both frontend and admin so the block.json handles always exist.
 */
function oboto_glossary_register_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	wp_register_style(
		'glossary',
		$theme_uri . '/css/glossary.css',
		array(),
		filemtime( $theme_dir . '/css/glossary.css' )
	);

	wp_register_script(
		'glossary-script',
		$theme_uri . '/blocks/glossary/view-script.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'oboto_glossary_register_assets' );
add_action( 'admin_enqueue_scripts', 'oboto_glossary_register_assets' );

/**
 * Enqueue the block assets inside the editor content iframe so search and
 * filters can be tried out directly in the editor preview.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/enqueueing-assets-in-the-editor/
 */
function oboto_glossary_editor_content_assets() {
	if ( ! is_admin() ) {
		return;
	}

	wp_enqueue_style( 'glossary' );
	wp_enqueue_script( 'glossary-script' );
}
add_action( 'enqueue_block_assets', 'oboto_glossary_editor_content_assets' );

/**
 * Editor style for the block preview.
 */
function oboto_glossary_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/css/glossary.css' );
}
add_action( 'init', 'oboto_glossary_editor_styles' );

/**
 * Populate the "Term sources" checkbox with every public taxonomy of the site,
 * so newly registered taxonomies become available without touching the JSON.
 *
 * @param array $field ACF field definition.
 * @return array
 */
function oboto_glossary_load_term_sources_field( $field ) {
	$excluded   = array_map( 'trim', explode( ',', OBOTO_GLOSSARY_EXCLUDED_TAXONOMIES ) );
	$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

	if ( empty( $taxonomies ) ) {
		return $field;
	}

	$choices = array();

	foreach ( $taxonomies as $taxonomy ) {
		if ( in_array( $taxonomy->name, $excluded, true ) ) {
			continue;
		}

		$label = ! empty( $taxonomy->labels->name ) ? $taxonomy->labels->name : $taxonomy->name;
		$choices[ $taxonomy->name ] = sprintf( '%s (%s)', $label, $taxonomy->name );
	}

	if ( ! empty( $choices ) ) {
		$field['choices'] = $choices;
	}

	return $field;
}
add_filter( 'acf/load_field/key=field_glossary_term_sources', 'oboto_glossary_load_term_sources_field' );
add_filter( 'acf/load_field/key=field_glossary_source_button_taxonomy', 'oboto_glossary_load_term_sources_field' );

/**
 * Resolve the alphabet bucket for a glossary entry.
 *
 * @param string $name Term name.
 * @return string Single uppercase A-Z letter, or OBOTO_GLOSSARY_OTHER_LETTER.
 */
function oboto_glossary_get_letter( $name ) {
	$normalized = remove_accents( (string) $name );
	$first      = strtoupper( substr( trim( $normalized ), 0, 1 ) );

	return preg_match( '/^[A-Z]$/', $first ) ? $first : OBOTO_GLOSSARY_OTHER_LETTER;
}

/**
 * Human readable label for a taxonomy, used as the default filter group.
 *
 * @param WP_Taxonomy $taxonomy_object Taxonomy object.
 * @return string
 */
function oboto_glossary_get_source_label( $taxonomy_object ) {
	if ( empty( $taxonomy_object ) ) {
		return '';
	}

	if ( ! empty( $taxonomy_object->labels->name ) ) {
		return (string) $taxonomy_object->labels->name;
	}

	return (string) $taxonomy_object->name;
}

/**
 * Resolve the filter group of a single term.
 *
 * - "source" groups entries by the taxonomy they come from.
 * - "parent" groups entries by their top level ancestor (hierarchical
 *   taxonomies only) and falls back to the source label otherwise.
 *
 * @param WP_Term $term         Term object.
 * @param string  $taxonomy     Taxonomy name.
 * @param string  $group_mode   Either "source" or "parent".
 * @param string  $source_label Taxonomy label.
 * @return string
 */
function oboto_glossary_get_term_group( $term, $taxonomy, $group_mode, $source_label ) {
	if ( 'parent' !== $group_mode || ! is_taxonomy_hierarchical( $taxonomy ) ) {
		return $source_label;
	}

	if ( empty( $term->parent ) ) {
		return (string) $term->name;
	}

	$ancestors = get_ancestors( (int) $term->term_id, $taxonomy, 'taxonomy' );

	if ( empty( $ancestors ) ) {
		return $source_label;
	}

	$top_level_term = get_term( (int) end( $ancestors ), $taxonomy );

	if ( ! $top_level_term || is_wp_error( $top_level_term ) ) {
		return $source_label;
	}

	return (string) $top_level_term->name;
}

/**
 * Default button labels per source taxonomy.
 *
 * Used when a glossary term exists in more than one taxonomy (for example
 * both as a Learning Center category and as a blog tag) and the row shows
 * one button per source. Editors can override them in the block settings.
 *
 * @return array Map of taxonomy name => button label.
 */
function oboto_glossary_default_source_labels() {
	return array(
		'learning-center-category' => __( 'Visit Learning Center', 'oboto' ),
		'post_tag'                 => __( 'View Blogs', 'oboto' ),
	);
}

/**
 * Resolve the button label for one source taxonomy.
 *
 * @param string $taxonomy       Taxonomy name.
 * @param string $taxonomy_label Human readable taxonomy label (fallback).
 * @param array  $overrides      Map of taxonomy name => label from the block settings.
 * @return string
 */
function oboto_glossary_get_source_button_label( $taxonomy, $taxonomy_label, $overrides = array() ) {
	if ( ! empty( $overrides[ $taxonomy ] ) ) {
		return (string) $overrides[ $taxonomy ];
	}

	$defaults = oboto_glossary_default_source_labels();

	if ( ! empty( $defaults[ $taxonomy ] ) ) {
		return (string) $defaults[ $taxonomy ];
	}

	/* translators: %s: taxonomy label, for example "Tags". */
	return sprintf( __( 'View %s', 'oboto' ), $taxonomy_label );
}

/**
 * Taxonomies that always come first, in this order, when a term has several sources.
 *
 * The order decides the button order on the row ("Visit Learning Center" before
 * "View Blogs"), whose description is used as the definition and which archive
 * is the primary link of a merged term.
 *
 * @return array Taxonomy names.
 */
function oboto_glossary_source_priority() {
	return array( 'learning-center-category', 'post_tag' );
}

/**
 * Order source taxonomies by oboto_glossary_source_priority().
 *
 * Taxonomies outside the priority list follow in the order they were configured.
 * The selected order of the ACF checkbox cannot be relied on: ACF returns the
 * values in the order of its choices, which follows taxonomy registration
 * (WordPress registers post_tag before the Learning Center taxonomy).
 *
 * @param array $taxonomies Taxonomy names.
 * @return array
 */
function oboto_glossary_sort_taxonomies( $taxonomies ) {
	$priority   = oboto_glossary_source_priority();
	$taxonomies = array_values( array_unique( array_filter( array_map( 'sanitize_key', (array) $taxonomies ) ) ) );
	$ranked     = array();

	foreach ( $taxonomies as $position => $taxonomy ) {
		$index    = array_search( $taxonomy, $priority, true );
		$ranked[] = array(
			'taxonomy' => $taxonomy,
			'rank'     => false === $index ? count( $priority ) + $position : $index,
		);
	}

	usort(
		$ranked,
		function ( $first, $second ) {
			return $first['rank'] <=> $second['rank'];
		}
	);

	return array_column( $ranked, 'taxonomy' );
}

/**
 * Collect glossary entries from the configured taxonomies.
 *
 * Terms are merged by name (case insensitive). A term that exists in several
 * taxonomies becomes one entry with several sources, so the row can link to
 * every archive that holds related content. When the same name appears twice
 * inside one taxonomy, the term with more posts wins.
 *
 * Entries are sorted alphabetically; sources follow oboto_glossary_sort_taxonomies().
 *
 * @param array $args {
 *     @type array  $taxonomies          Taxonomy names to read terms from.
 *     @type bool   $hide_empty          Skip terms without assigned posts.
 *     @type bool   $require_description Skip terms without a description.
 *     @type array  $exclude_slugs       Term slugs to skip.
 *     @type string $group_mode          "source" or "parent".
 *     @type array  $source_labels       Map of taxonomy name => button label overrides.
 * }
 * @return array List of glossary entries.
 */
function oboto_glossary_collect_terms( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'taxonomies'          => array(),
			'hide_empty'          => false,
			'require_description' => false,
			'exclude_slugs'       => array(),
			'group_mode'          => 'source',
			'source_labels'       => array(),
		)
	);

	// Keyed by lower-cased term name while collecting.
	$merged = array();

	foreach ( oboto_glossary_sort_taxonomies( $args['taxonomies'] ) as $taxonomy ) {
		$taxonomy = sanitize_key( $taxonomy );

		if ( '' === $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$taxonomy_object = get_taxonomy( $taxonomy );
		$source_label    = oboto_glossary_get_source_label( $taxonomy_object );
		$button_label    = oboto_glossary_get_source_button_label( $taxonomy, $source_label, (array) $args['source_labels'] );

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => (bool) $args['hide_empty'],
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			continue;
		}

		foreach ( $terms as $term ) {
			if ( in_array( $term->slug, $args['exclude_slugs'], true ) ) {
				continue;
			}

			$merge_key = strtolower( trim( (string) $term->name ) );

			if ( '' === $merge_key ) {
				continue;
			}

			$term_link         = get_term_link( $term );
			$plain_description = trim( wp_strip_all_tags( (string) $term->description ) );

			$source = array(
				'taxonomy' => $taxonomy,
				'term_id'  => (int) $term->term_id,
				'slug'     => (string) $term->slug,
				'count'    => (int) $term->count,
				'link'     => is_wp_error( $term_link ) ? '' : (string) $term_link,
				'label'    => $button_label,
				'group'    => oboto_glossary_get_term_group( $term, $taxonomy, $args['group_mode'], $source_label ),
			);

			if ( ! isset( $merged[ $merge_key ] ) ) {
				$merged[ $merge_key ] = array(
					'name'        => (string) $term->name,
					'description' => (string) $term->description,
					'plain_text'  => $plain_description,
					'letter'      => oboto_glossary_get_letter( $term->name ),
					'sources'     => array( $taxonomy => $source ),
				);
				continue;
			}

			$existing_sources = $merged[ $merge_key ]['sources'];

			// Same name twice inside one taxonomy: keep the term with more posts.
			if ( ! isset( $existing_sources[ $taxonomy ] ) || $source['count'] > $existing_sources[ $taxonomy ]['count'] ) {
				$merged[ $merge_key ]['sources'][ $taxonomy ] = $source;
			}

			// The first non-empty description, in source priority order, wins.
			if ( '' === $merged[ $merge_key ]['plain_text'] && '' !== $plain_description ) {
				$merged[ $merge_key ]['description'] = (string) $term->description;
				$merged[ $merge_key ]['plain_text']  = $plain_description;
			}
		}
	}

	$entries = array();

	foreach ( $merged as $entry ) {
		if ( $args['require_description'] && '' === $entry['plain_text'] ) {
			continue;
		}

		$sources = array_values( $entry['sources'] );

		// Do not offer a button that leads to an empty archive when another source has content.
		if ( count( $sources ) > 1 ) {
			$sources_with_posts = array_values(
				array_filter(
					$sources,
					function ( $source ) {
						return $source['count'] > 0;
					}
				)
			);

			if ( ! empty( $sources_with_posts ) ) {
				$sources = $sources_with_posts;
			}
		}

		$primary = $sources[0];
		$groups  = array_values( array_unique( array_filter( array_column( $sources, 'group' ) ) ) );

		$entry['sources']  = $sources;
		$entry['term_id']  = $primary['term_id'];
		$entry['slug']     = $primary['slug'];
		$entry['taxonomy'] = $primary['taxonomy'];
		$entry['link']     = $primary['link'];
		$entry['count']    = (int) array_sum( array_column( $sources, 'count' ) );
		$entry['groups']   = $groups;
		$entry['group']    = ! empty( $groups ) ? $groups[0] : '';
		$entry['anchor']   = 'glossary-' . $primary['taxonomy'] . '-' . $primary['slug'];

		$entries[] = $entry;
	}

	usort(
		$entries,
		function ( $first, $second ) {
			return strcasecmp( $first['name'], $second['name'] );
		}
	);

	return $entries;
}

/**
 * Group glossary entries by their alphabet letter.
 *
 * @param array $entries Glossary entries.
 * @return array Map of letter => entries, ordered A-Z with "#" last.
 */
function oboto_glossary_group_by_letter( $entries ) {
	$groups = array();

	foreach ( $entries as $entry ) {
		$groups[ $entry['letter'] ][] = $entry;
	}

	uksort(
		$groups,
		function ( $first, $second ) {
			if ( OBOTO_GLOSSARY_OTHER_LETTER === $first ) {
				return 1;
			}

			if ( OBOTO_GLOSSARY_OTHER_LETTER === $second ) {
				return -1;
			}

			return strcmp( $first, $second );
		}
	);

	return $groups;
}

/**
 * Collect the distinct filter groups of a set of entries, preserving A-Z order.
 *
 * @param array $entries Glossary entries.
 * @return array
 */
function oboto_glossary_get_groups( $entries ) {
	$groups = array();

	foreach ( $entries as $entry ) {
		foreach ( $entry['groups'] as $group ) {
			if ( '' !== $group && ! in_array( $group, $groups, true ) ) {
				$groups[] = $group;
			}
		}
	}

	sort( $groups, SORT_NATURAL | SORT_FLAG_CASE );

	return $groups;
}

/**
 * Find a glossary entry by slug or name.
 *
 * @param array  $entries   Glossary entries.
 * @param string $reference Term slug or term name.
 * @return array|null
 */
function oboto_glossary_find_entry( $entries, $reference ) {
	$reference = strtolower( trim( (string) $reference ) );

	if ( '' === $reference ) {
		return null;
	}

	foreach ( $entries as $entry ) {
		if ( strtolower( $entry['name'] ) === $reference ) {
			return $entry;
		}

		// Match the slug of any source, so a tag slug also finds a merged entry.
		foreach ( $entry['sources'] as $source ) {
			if ( strtolower( $source['slug'] ) === $reference ) {
				return $entry;
			}
		}
	}

	return null;
}

/**
 * Build the JSON-LD DefinedTermSet payload for the glossary.
 *
 * @param array  $entries Glossary entries.
 * @param string $name    Glossary name.
 * @param string $url     Canonical URL of the glossary page.
 * @return array|null Schema array, or null when there is nothing to describe.
 */
function oboto_glossary_build_schema( $entries, $name, $url ) {
	$defined_terms = array();

	foreach ( $entries as $entry ) {
		if ( '' === $entry['plain_text'] ) {
			continue;
		}

		$defined_term = array(
			'@type'       => 'DefinedTerm',
			'name'        => $entry['name'],
			'description' => $entry['plain_text'],
		);

		if ( '' !== $entry['link'] ) {
			$defined_term['url'] = $entry['link'];
		}

		$defined_terms[] = $defined_term;
	}

	if ( empty( $defined_terms ) ) {
		return null;
	}

	$schema = array(
		'@context'       => 'https://schema.org',
		'@type'          => 'DefinedTermSet',
		'name'           => $name,
		'hasDefinedTerm' => $defined_terms,
	);

	if ( '' !== $url ) {
		$schema['url'] = $url;
	}

	return $schema;
}
