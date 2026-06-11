<?php

/**
 * Events SEO indexing automation.
 */

if ( ! defined( 'OBOTO_EVENTS_CATEGORY_SLUG' ) ) {
	define( 'OBOTO_EVENTS_CATEGORY_SLUG', 'events' );
}

if ( ! defined( 'OBOTO_EVENT_END_DATE_FIELD' ) ) {
	define( 'OBOTO_EVENT_END_DATE_FIELD', 'event_end_date' );
}

if ( ! defined( 'OBOTO_EVENTS_CRON_HOOK' ) ) {
	define( 'OBOTO_EVENTS_CRON_HOOK', 'oboto_daily_events_noindex_sync' );
}

if ( ! defined( 'OBOTO_YOAST_NOINDEX_META_KEY' ) ) {
	define( 'OBOTO_YOAST_NOINDEX_META_KEY', '_yoast_wpseo_meta-robots-noindex' );
}

if ( ! defined( 'OBOTO_YOAST_NOINDEX_VALUE' ) ) {
	define( 'OBOTO_YOAST_NOINDEX_VALUE', '1' );
}

if ( ! defined( 'OBOTO_YOAST_INDEX_VALUE' ) ) {
	define( 'OBOTO_YOAST_INDEX_VALUE', '2' );
}

/**
 * Register the event end date field for posts in the Events category.
 */
function oboto_register_events_acf_fields()
{
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_oboto_event_settings',
			'title'                 => 'Event Settings',
			'fields'                => array(
				array(
					'key'           => 'field_oboto_event_end_date',
					'label'         => 'Event End Date',
					'name'          => OBOTO_EVENT_END_DATE_FIELD,
					'type'          => 'date_picker',
					'instructions'  => 'Used to automatically set Yoast index/noindex for Events posts.',
					'required'      => 0,
					'display_format' => 'F j, Y',
					'return_format' => 'Ymd',
					'first_day'     => 1,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
					array(
						'param'    => 'post_taxonomy',
						'operator' => '==',
						'value'    => 'category:' . OBOTO_EVENTS_CATEGORY_SLUG,
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'oboto_register_events_acf_fields' );

/**
 * Schedule the daily events indexing sync.
 */
function oboto_schedule_events_noindex_sync()
{
	if ( wp_next_scheduled( OBOTO_EVENTS_CRON_HOOK ) ) {
		return;
	}

	wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', OBOTO_EVENTS_CRON_HOOK );
}
add_action( 'init', 'oboto_schedule_events_noindex_sync' );
add_action( 'after_switch_theme', 'oboto_schedule_events_noindex_sync' );

/**
 * Unschedule the daily sync when switching away from this theme.
 */
function oboto_unschedule_events_noindex_sync()
{
	$timestamp = wp_next_scheduled( OBOTO_EVENTS_CRON_HOOK );

	while ( $timestamp ) {
		wp_unschedule_event( $timestamp, OBOTO_EVENTS_CRON_HOOK );
		$timestamp = wp_next_scheduled( OBOTO_EVENTS_CRON_HOOK );
	}
}
add_action( 'switch_theme', 'oboto_unschedule_events_noindex_sync' );

/**
 * Normalize ACF Date Picker values to Ymd.
 *
 * @param mixed $value Raw date value.
 * @return string
 */
function oboto_normalize_event_end_date( $value )
{
	$value = trim( (string) $value );

	if ( $value === '' ) {
		return '';
	}

	if ( preg_match( '/^\d{8}$/', $value ) ) {
		return $value;
	}

	if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches ) ) {
		return $matches[1] . $matches[2] . $matches[3];
	}

	return '';
}

/**
 * Set the Yoast index/noindex value for a post.
 *
 * @param int    $post_id Post ID.
 * @param string $value   Yoast robots noindex value.
 */
function oboto_set_yoast_noindex_value( $post_id, $value )
{
	if ( class_exists( 'WPSEO_Meta' ) && method_exists( 'WPSEO_Meta', 'set_value' ) ) {
		WPSEO_Meta::set_value( 'meta-robots-noindex', $value, $post_id );
	} else {
		update_post_meta( $post_id, OBOTO_YOAST_NOINDEX_META_KEY, $value );
	}

	if ( (string) get_post_meta( $post_id, OBOTO_YOAST_NOINDEX_META_KEY, true ) !== $value ) {
		update_post_meta( $post_id, OBOTO_YOAST_NOINDEX_META_KEY, $value );
	}

	clean_post_cache( $post_id );
}

/**
 * Sync Yoast index/noindex state for Events posts.
 *
 * @return array<string,int>
 */
function oboto_sync_events_yoast_indexing()
{
	$today    = wp_date( 'Ymd' );
	$post_ids = get_posts(
		array(
			'post_type'              => 'post',
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy'         => 'category',
					'field'            => 'slug',
					'terms'            => array( OBOTO_EVENTS_CATEGORY_SLUG ),
					'include_children' => true,
				),
			),
		)
	);

	$report = array(
		'checked' => 0,
		'noindex' => 0,
		'index'   => 0,
		'updated' => 0,
	);

	foreach ( $post_ids as $post_id ) {
		$report['checked']++;

		$event_end_date = oboto_normalize_event_end_date( get_post_meta( $post_id, OBOTO_EVENT_END_DATE_FIELD, true ) );
		$target_value   = ( $event_end_date !== '' && $event_end_date < $today )
			? OBOTO_YOAST_NOINDEX_VALUE
			: OBOTO_YOAST_INDEX_VALUE;

		$current_value = (string) get_post_meta( $post_id, OBOTO_YOAST_NOINDEX_META_KEY, true );

		if ( $current_value !== $target_value ) {
			oboto_set_yoast_noindex_value( $post_id, $target_value );
			$report['updated']++;
		}

		if ( $target_value === OBOTO_YOAST_NOINDEX_VALUE ) {
			$report['noindex']++;
		} else {
			$report['index']++;
		}
	}

	return $report;
}
add_action( OBOTO_EVENTS_CRON_HOOK, 'oboto_sync_events_yoast_indexing' );

/**
 * Temporary manual trigger for testing:
 * /?oboto_events_noindex_run=1
 */
function oboto_maybe_run_events_noindex_sync_from_get()
{
	if ( ! isset( $_GET['oboto_events_noindex_run'] ) ) {
		return;
	}

	$report = oboto_sync_events_yoast_indexing();
	$lines  = array(
		'Events Yoast indexing sync complete.',
		'Checked: ' . $report['checked'],
		'Noindex: ' . $report['noindex'],
		'Index: ' . $report['index'],
		'Updated: ' . $report['updated'],
	);

	wp_die(
		'<pre>' . esc_html( implode( "\n", $lines ) ) . '</pre>',
		'Events Yoast indexing sync'
	);
}
add_action( 'init', 'oboto_maybe_run_events_noindex_sync_from_get', 30 );
