<?php

/**
 * Block template: synchronized MCP server detail page.
 *
 * Every public value is rendered from the normalized catalog payload. Sections
 * disappear when their source fields are absent instead of showing placeholders.
 *
 * @param array $block ACF block settings.
 */

$acf_post_id    = isset( $post_id ) ? absint( $post_id ) : 0;
$post_id        = 0;
$server         = array();
$queried_object = get_queried_object();
$candidate_ids  = array_filter(
	array_unique(
		array(
			get_queried_object_id(),
			$queried_object instanceof WP_Post ? (int) $queried_object->ID : 0,
			$acf_post_id,
			get_the_ID(),
		)
	)
);

if ( class_exists( 'MCP_Server_Sync' ) ) {
	foreach ( $candidate_ids as $candidate_id ) {
		$candidate_post = get_post( $candidate_id );
		if ( $candidate_post instanceof WP_Post && MCP_Server_Sync::POST_TYPE === $candidate_post->post_type ) {
			$post_id = (int) $candidate_post->ID;
			$server  = MCP_Server_Sync::get_payload( $post_id );
			break;
		}
	}

	if ( empty( $server ) ) {
		$request_path    = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
		$request_slug    = $request_path ? sanitize_title( basename( untrailingslashit( $request_path ) ) ) : '';
		$query_slug      = sanitize_title( (string) get_query_var( MCP_Server_Sync::POST_TYPE ) );
		$name_slug       = sanitize_title( (string) get_query_var( 'name' ) );
		$slug_candidates = array_filter( array_unique( array( $query_slug, $name_slug, $request_slug ) ) );

		foreach ( $slug_candidates as $slug_candidate ) {
			$server = MCP_Server_Sync::get_catalog_payload_by_slug( $slug_candidate );
			if ( ! empty( $server ) ) {
				$resolved_post = get_page_by_path( $slug_candidate, OBJECT, MCP_Server_Sync::POST_TYPE );
				$post_id      = $resolved_post instanceof WP_Post ? (int) $resolved_post->ID : 0;
				break;
			}
		}
	}
}

if ( empty( $server ) ) {
	if ( is_admin() ) {
		echo '<p>' . esc_html__( 'MCP server details are generated after the catalog synchronization runs.', 'oboto' ) . '</p>';
	} else {
		echo '<!-- MCP server detail payload is unavailable. -->';
	}
	return;
}

$name              = isset( $server['name'] ) ? trim( (string) $server['name'] ) : get_the_title( $post_id );
$short_description = isset( $server['short_description'] ) ? trim( (string) $server['short_description'] ) : '';
$description       = isset( $server['description'] ) ? (string) $server['description'] : '';
$icon              = isset( $server['icon'] ) ? (string) $server['icon'] : '';
$official_url      = isset( $server['external_url'] ) ? (string) $server['external_url'] : '';
$categories        = isset( $server['categories'] ) && is_array( $server['categories'] ) ? array_values( array_filter( array_map( 'strval', $server['categories'] ) ) ) : array();
$environment       = isset( $server['env'] ) && is_array( $server['env'] ) ? $server['env'] : array();
$runtime           = isset( $server['runtime'] ) ? trim( (string) $server['runtime'] ) : '';
$container_config  = isset( $server['containerized_config'] ) && is_array( $server['containerized_config'] ) ? $server['containerized_config'] : array();
$remote_config     = isset( $server['remote_config'] ) && is_array( $server['remote_config'] ) ? $server['remote_config'] : array();
$metadata          = isset( $server['metadata'] ) && is_array( $server['metadata'] ) ? $server['metadata'] : array();
$source_file       = isset( $server['source_file'] ) && class_exists( 'MCP_Catalog_Fetcher' ) ? MCP_Catalog_Fetcher::sanitize_source_path( $server['source_file'] ) : '';
$catalog_url       = home_url( '/mcp-catalog/' );
$source_url        = class_exists( 'MCP_Catalog_Fetcher' ) ? MCP_Catalog_Fetcher::get_source_url( $source_file ) : 'https://github.com/obot-platform/mcp-catalog';
$official_host     = $official_url ? (string) wp_parse_url( $official_url, PHP_URL_HOST ) : '';
$mcp_url           = '';
$mcp_url_href      = '';

foreach ( array( 'fixedURL', 'url', 'urlTemplate' ) as $remote_url_key ) {
	if ( ! empty( $remote_config[ $remote_url_key ] ) && is_scalar( $remote_config[ $remote_url_key ] ) ) {
		$mcp_url = trim( (string) $remote_config[ $remote_url_key ] );
		break;
	}
}

if ( ! $mcp_url && ! empty( $remote_config['hostname'] ) && is_scalar( $remote_config['hostname'] ) ) {
	$mcp_url = 'https://' . ltrim( trim( (string) $remote_config['hostname'] ), '/' );
}

if ( $mcp_url && false === strpos( $mcp_url, '${' ) ) {
	$mcp_url_href = esc_url_raw( $mcp_url );
}

$format_value = static function ( $value ) {
	if ( is_bool( $value ) ) {
		return $value ? __( 'Yes', 'oboto' ) : __( 'No', 'oboto' );
	}
	if ( is_array( $value ) ) {
		$contains_nested_value = (bool) array_filter( $value, 'is_array' );
		if ( ! $contains_nested_value ) {
			return implode( ', ', array_map( 'strval', $value ) );
		}
		return (string) wp_json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}
	return (string) $value;
};

$markdown_to_html = static function ( $markdown ) {
	if ( class_exists( 'Parsedown' ) ) {
		$parser = new Parsedown();
		$parser->setSafeMode( true );
		return wp_kses_post( $parser->text( $markdown ) );
	}

	return wp_kses_post( wpautop( esc_html( $markdown ) ) );
};

$truthy = static function ( $value ) {
	if ( is_bool( $value ) ) {
		return $value;
	}
	return in_array( strtolower( trim( (string) $value ) ), array( '1', 'true', 'yes', 'on' ), true );
};

$runtime_label    = 'containerized' === $runtime ? __( 'Containerized', 'oboto' ) : ( 'remote' === $runtime ? __( 'Remote', 'oboto' ) : ucfirst( $runtime ) );
$deprecated       = isset( $metadata['deprecated'] ) && $truthy( $metadata['deprecated'] );

$required_secrets = 0;
foreach ( $environment as $variable ) {
	if ( is_array( $variable ) && isset( $variable['required'], $variable['sensitive'] ) && $truthy( $variable['required'] ) && $truthy( $variable['sensitive'] ) ) {
		++$required_secrets;
	}
}
if ( ! empty( $remote_config['headers'] ) && is_array( $remote_config['headers'] ) ) {
	foreach ( $remote_config['headers'] as $header ) {
		if ( is_array( $header ) && isset( $header['required'], $header['sensitive'] ) && $truthy( $header['required'] ) && $truthy( $header['sensitive'] ) ) {
			++$required_secrets;
		}
	}
}

$sync_status = class_exists( 'MCP_Catalog_Fetcher' ) ? MCP_Catalog_Fetcher::get_sync_status() : array();
$last_sync   = isset( $sync_status['last_success_gmt'] ) ? strtotime( (string) $sync_status['last_success_gmt'] ) : false;
$last_sync_label = $last_sync
	? sprintf( __( '%s ago', 'oboto' ), human_time_diff( $last_sync, time() ) )
	: '';

$has_configuration = ! empty( $environment ) || ! empty( $container_config ) || ! empty( $remote_config );
$section_links      = array(
	'about' => __( 'About', 'oboto' ),
);
if ( $has_configuration ) {
	$section_links['configuration'] = __( 'Configuration', 'oboto' );
}

$related = array();
if ( $categories && class_exists( 'MCP_Catalog_Fetcher' ) ) {
	$current_slug = isset( $server['slug'] ) ? sanitize_title( (string) $server['slug'] ) : '';
	foreach ( MCP_Catalog_Fetcher::get_catalog( 24 ) as $related_server ) {
		if ( ! is_array( $related_server ) ) {
			continue;
		}

		$related_slug = isset( $related_server['slug'] ) ? sanitize_title( (string) $related_server['slug'] ) : '';
		if ( ! $related_slug || $current_slug === $related_slug ) {
			continue;
		}

		$related_categories = isset( $related_server['categories'] ) && is_array( $related_server['categories'] ) ? $related_server['categories'] : array();
		$shared_categories  = array_intersect( $categories, $related_categories );
		if ( $shared_categories ) {
			$related[] = array(
				'score'  => count( $shared_categories ),
				'name'   => isset( $related_server['name'] ) ? (string) $related_server['name'] : '',
				'icon'   => isset( $related_server['icon'] ) ? (string) $related_server['icon'] : '',
				'server' => $related_server,
			);
		}
	}
	usort(
		$related,
		static function ( $left, $right ) {
			if ( $left['score'] === $right['score'] ) {
				return strcasecmp( $left['name'], $right['name'] );
			}
			return $right['score'] <=> $left['score'];
		}
	);
	$related_candidates = array_slice( $related, 0, 12 );
	$related            = array();
	foreach ( $related_candidates as $related_candidate ) {
		$related_url = MCP_Server_Sync::get_internal_url( $related_candidate['server'] );
		if ( $related_url ) {
			$related_candidate['url'] = $related_url;
			unset( $related_candidate['server'] );
			$related[] = $related_candidate;
		}
		if ( 4 === count( $related ) ) {
			break;
		}
	}
}

$header_button = function_exists( 'get_field' ) ? get_field( 'header_button', 'option' ) : array();
$wrapper       = get_block_wrapper_attributes( array( 'class' => 'mcp-server-single' ) );
?>
<article <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mcp-server-single__container">
		<nav class="mcp-server-single__breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'oboto' ); ?>">
			<a href="<?php echo esc_url( $catalog_url ); ?>"><?php esc_html_e( 'MCP Catalog', 'oboto' ); ?></a>
			<span aria-hidden="true">/</span>
			<?php if ( $categories ) : ?>
				<span><?php echo esc_html( $categories[0] ); ?></span>
				<span aria-hidden="true">/</span>
			<?php endif; ?>
			<span aria-current="page"><?php echo esc_html( $name ); ?></span>
		</nav>

		<header class="mcp-server-single__hero">
			<div class="mcp-server-single__identity">
				<figure class="mcp-server-single__icon">
					<?php if ( $icon ) : ?>
						<img src="<?php echo esc_url( $icon ); ?>" alt="" width="64" height="64" aria-hidden="true">
					<?php else : ?>
						<span aria-hidden="true"><?php echo esc_html( strtoupper( substr( $name, 0, 1 ) ) ); ?></span>
					<?php endif; ?>
				</figure>
				<div class="mcp-server-single__hero-copy">
					<div class="mcp-server-single__badges">
						<?php if ( $deprecated ) : ?><span class="mcp-server-single__badge mcp-server-single__badge--warning"><?php esc_html_e( 'Deprecated', 'oboto' ); ?></span><?php endif; ?>
						<?php if ( $required_secrets ) : ?><span class="mcp-server-single__badge"><?php echo esc_html( sprintf( _n( '%d required secret', '%d required secrets', $required_secrets, 'oboto' ), $required_secrets ) ); ?></span><?php endif; ?>
					</div>
					<h1><?php echo esc_html( $name ); ?></h1>
					<?php if ( $short_description ) : ?><p class="mcp-server-single__lede"><?php echo esc_html( $short_description ); ?></p><?php endif; ?>
				</div>
			</div>

			<div class="mcp-server-single__hero-meta" aria-label="<?php esc_attr_e( 'Server summary', 'oboto' ); ?>">
				<?php if ( $runtime_label ) : ?><span><?php echo esc_html( $runtime_label ); ?></span><?php endif; ?>
				<?php if ( $last_sync_label ) : ?><span><?php echo esc_html( sprintf( __( 'Updated %s', 'oboto' ), $last_sync_label ) ); ?></span><?php endif; ?>
			</div>

			<div class="mcp-server-single__actions">
				<?php if ( $official_url ) : ?>
					<a class="mcp-server-single__button mcp-server-single__button--primary" href="<?php echo esc_url( $official_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Official page', 'oboto' ); ?><span aria-hidden="true">↗</span></a>
				<?php endif; ?>
				<?php if ( $source_url ) : ?><a class="mcp-server-single__button" href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View catalog entry', 'oboto' ); ?><span aria-hidden="true">↗</span></a><?php endif; ?>
				<a class="mcp-server-single__button" href="<?php echo esc_url( $catalog_url ); ?>"><?php esc_html_e( 'Back to catalog', 'oboto' ); ?></a>
			</div>

			<?php if ( $categories ) : ?>
				<div class="mcp-server-single__categories" aria-label="<?php esc_attr_e( 'Categories', 'oboto' ); ?>">
					<?php foreach ( $categories as $category ) : ?><span><?php echo esc_html( $category ); ?></span><?php endforeach; ?>
				</div>
			<?php endif; ?>
		</header>

		<nav class="mcp-server-single__section-nav" aria-label="<?php esc_attr_e( 'On this page', 'oboto' ); ?>">
			<?php foreach ( $section_links as $anchor => $label ) : ?><a href="#<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?>
		</nav>

		<div class="mcp-server-single__layout">
			<div class="mcp-server-single__content">
				<section id="about" class="mcp-server-single__section">
					<h2><?php esc_html_e( 'About', 'oboto' ); ?></h2>
					<div class="mcp-server-single__prose"><?php echo $markdown_to_html( $description ? $description : $short_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				</section>

				<?php if ( $has_configuration ) : ?>
					<section id="configuration" class="mcp-server-single__section">
						<h2><?php esc_html_e( 'Configuration', 'oboto' ); ?></h2>

						<?php if ( $environment ) : ?>
							<h3><?php esc_html_e( 'Environment variables', 'oboto' ); ?></h3>
							<div class="mcp-server-single__table-wrap"><table><thead><tr><th><?php esc_html_e( 'Variable', 'oboto' ); ?></th><th><?php esc_html_e( 'Description', 'oboto' ); ?></th><th><?php esc_html_e( 'Required', 'oboto' ); ?></th></tr></thead><tbody>
							<?php foreach ( $environment as $variable ) : ?>
								<?php if ( ! is_array( $variable ) ) { continue; } ?>
				<tr><td><code><?php echo esc_html( isset( $variable['key'] ) ? (string) $variable['key'] : ( isset( $variable['name'] ) ? (string) $variable['name'] : '' ) ); ?></code></td><td><?php echo esc_html( isset( $variable['description'] ) ? (string) $variable['description'] : ( isset( $variable['name'] ) ? (string) $variable['name'] : '' ) ); ?></td><td><?php echo isset( $variable['required'] ) && $truthy( $variable['required'] ) ? esc_html__( 'Yes', 'oboto' ) : esc_html__( 'No', 'oboto' ); ?></td></tr>
							<?php endforeach; ?>
							</tbody></table></div>
						<?php endif; ?>

						<?php if ( ! empty( $remote_config['headers'] ) && is_array( $remote_config['headers'] ) ) : ?>
							<h3><?php esc_html_e( 'Request headers', 'oboto' ); ?></h3>
							<div class="mcp-server-single__table-wrap"><table><thead><tr><th><?php esc_html_e( 'Header', 'oboto' ); ?></th><th><?php esc_html_e( 'Description', 'oboto' ); ?></th><th><?php esc_html_e( 'Required', 'oboto' ); ?></th></tr></thead><tbody>
							<?php foreach ( $remote_config['headers'] as $header ) : ?>
								<?php if ( ! is_array( $header ) ) { continue; } ?>
				<tr><td><code><?php echo esc_html( isset( $header['key'] ) ? (string) $header['key'] : ( isset( $header['name'] ) ? (string) $header['name'] : '' ) ); ?></code></td><td><?php echo esc_html( isset( $header['description'] ) ? (string) $header['description'] : ( isset( $header['name'] ) ? (string) $header['name'] : '' ) ); ?></td><td><?php echo isset( $header['required'] ) && $truthy( $header['required'] ) ? esc_html__( 'Yes', 'oboto' ) : esc_html__( 'No', 'oboto' ); ?></td></tr>
							<?php endforeach; ?>
							</tbody></table></div>
						<?php endif; ?>

						<?php if ( $container_config ) : ?>
							<h3><?php esc_html_e( 'Container runtime', 'oboto' ); ?></h3>
							<dl class="mcp-server-single__config-list">
								<?php foreach ( $container_config as $config_key => $config_value ) : ?>
									<?php if ( '' === $format_value( $config_value ) ) { continue; } ?>
									<div><dt><?php echo esc_html( ucwords( str_replace( array( '_', '-' ), ' ', (string) $config_key ) ) ); ?></dt><dd><code><?php echo esc_html( $format_value( $config_value ) ); ?></code></dd></div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>

						<?php if ( $remote_config ) : ?>
							<h3><?php esc_html_e( 'Remote runtime', 'oboto' ); ?></h3>
							<dl class="mcp-server-single__config-list">
								<?php foreach ( $remote_config as $config_key => $config_value ) : ?>
									<?php if ( 'headers' === $config_key || '' === $format_value( $config_value ) ) { continue; } ?>
									<div><dt><?php echo esc_html( ucwords( preg_replace( '/(?<!^)[A-Z]/', ' $0', str_replace( array( '_', '-' ), ' ', (string) $config_key ) ) ) ); ?></dt><dd><code><?php echo esc_html( $format_value( $config_value ) ); ?></code></dd></div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>
					</section>
				<?php endif; ?>
			</div>

			<aside class="mcp-server-single__sidebar" aria-label="<?php esc_attr_e( 'Server details', 'oboto' ); ?>">
				<div class="mcp-server-single__side-card">
					<h2><?php esc_html_e( 'Server links', 'oboto' ); ?></h2>
					<ul class="mcp-server-single__server-links">
						<?php if ( $official_url ) : ?>
							<li>
								<span><?php esc_html_e( 'Official page', 'oboto' ); ?></span>
								<a href="<?php echo esc_url( $official_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $official_host ? $official_host : $official_url ); ?><span aria-hidden="true">↗</span></a>
							</li>
						<?php endif; ?>
						<?php if ( $mcp_url ) : ?>
							<li>
								<span><?php esc_html_e( 'MCP URL', 'oboto' ); ?></span>
								<?php if ( $mcp_url_href ) : ?>
									<a href="<?php echo esc_url( $mcp_url_href ); ?>" target="_blank" rel="noopener noreferrer"><code><?php echo esc_html( $mcp_url ); ?></code><span aria-hidden="true">↗</span></a>
								<?php else : ?>
									<code><?php echo esc_html( $mcp_url ); ?></code>
								<?php endif; ?>
							</li>
						<?php endif; ?>
						<?php if ( $source_url ) : ?>
							<li>
								<span><?php esc_html_e( 'Catalog entry', 'oboto' ); ?></span>
								<a href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $source_file ? $source_file : __( 'MCP Catalog repository', 'oboto' ) ); ?><span aria-hidden="true">↗</span></a>
							</li>
						<?php endif; ?>
					</ul>
					<?php if ( $last_sync_label ) : ?><p><?php echo esc_html( sprintf( __( 'Catalog synchronized %s.', 'oboto' ), $last_sync_label ) ); ?></p><?php endif; ?>
				</div>

				<?php if ( $related ) : ?>
					<div class="mcp-server-single__side-card">
						<h2><?php esc_html_e( 'Related servers', 'oboto' ); ?></h2>
						<ul class="mcp-server-single__related">
							<?php foreach ( $related as $related_item ) : ?><li><a href="<?php echo esc_url( $related_item['url'] ); ?>"><?php if ( $related_item['icon'] ) : ?><img src="<?php echo esc_url( $related_item['icon'] ); ?>" alt="" loading="lazy" aria-hidden="true"><?php endif; ?><span><?php echo esc_html( $related_item['name'] ); ?></span></a></li><?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( is_array( $header_button ) && ! empty( $header_button['url'] ) && ! empty( $header_button['title'] ) ) : ?>
					<div class="mcp-server-single__side-card mcp-server-single__side-card--cta">
						<h2><?php esc_html_e( 'Use MCP with Obot', 'oboto' ); ?></h2>
						<p><?php esc_html_e( 'Connect tools and build AI agents with Obot.', 'oboto' ); ?></p>
						<a class="mcp-server-single__button mcp-server-single__button--primary" href="<?php echo esc_url( $header_button['url'] ); ?>"<?php echo ! empty( $header_button['target'] ) ? ' target="' . esc_attr( $header_button['target'] ) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $header_button['title'] ); ?></a>
					</div>
				<?php endif; ?>
			</aside>
		</div>
	</div>
</article>
