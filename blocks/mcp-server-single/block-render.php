<?php

/**
 * Block template: synchronized MCP server detail page.
 *
 * Every public value is rendered from the normalized catalog payload. Sections
 * disappear when their source fields are absent instead of showing placeholders.
 *
 * @param array $block ACF block settings.
 */

$post_id = get_the_ID();
$server  = class_exists( 'MCP_Server_Sync' ) ? MCP_Server_Sync::get_payload( $post_id ) : array();

if ( empty( $server ) || MCP_Server_Sync::POST_TYPE !== get_post_type( $post_id ) ) {
	if ( is_admin() ) {
		echo '<p>' . esc_html__( 'MCP server details are generated after the catalog synchronization runs.', 'oboto' ) . '</p>';
	}
	return;
}

$name              = isset( $server['name'] ) ? trim( (string) $server['name'] ) : get_the_title( $post_id );
$short_description = isset( $server['short_description'] ) ? trim( (string) $server['short_description'] ) : '';
$description       = isset( $server['description'] ) ? (string) $server['description'] : '';
$icon              = isset( $server['icon'] ) ? (string) $server['icon'] : '';
$repository_url    = isset( $server['external_url'] ) ? (string) $server['external_url'] : '';
$categories        = isset( $server['categories'] ) && is_array( $server['categories'] ) ? array_values( array_filter( array_map( 'strval', $server['categories'] ) ) ) : array();
$tools             = isset( $server['tool_preview'] ) && is_array( $server['tool_preview'] ) ? $server['tool_preview'] : array();
$environment       = isset( $server['env'] ) && is_array( $server['env'] ) ? $server['env'] : array();
$runtime           = isset( $server['runtime'] ) ? trim( (string) $server['runtime'] ) : '';
$container_config  = isset( $server['containerized_config'] ) && is_array( $server['containerized_config'] ) ? $server['containerized_config'] : array();
$remote_config     = isset( $server['remote_config'] ) && is_array( $server['remote_config'] ) ? $server['remote_config'] : array();
$metadata          = isset( $server['metadata'] ) && is_array( $server['metadata'] ) ? $server['metadata'] : array();
$source_file       = isset( $server['source_file'] ) ? sanitize_file_name( (string) $server['source_file'] ) : '';
$catalog_url       = home_url( '/mcp-catalog/' );
$source_url        = $source_file ? 'https://github.com/obot-platform/mcp-catalog/blob/main/' . rawurlencode( $source_file ) : 'https://github.com/obot-platform/mcp-catalog';

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

$user_type_labels = array(
	'singleUser' => __( 'Single user', 'oboto' ),
	'multiUser'  => __( 'Multiple users', 'oboto' ),
);
$server_user_type = isset( $server['server_user_type'] ) ? (string) $server['server_user_type'] : '';
$user_type_label  = isset( $user_type_labels[ $server_user_type ] ) ? $user_type_labels[ $server_user_type ] : $server_user_type;
$runtime_label    = 'containerized' === $runtime ? __( 'Containerized', 'oboto' ) : ( 'remote' === $runtime ? __( 'Remote', 'oboto' ) : ucfirst( $runtime ) );
$deprecated       = isset( $metadata['deprecated'] ) && $truthy( $metadata['deprecated'] );
$unsupported      = isset( $metadata['unsupportedTools'] ) ? $format_value( $metadata['unsupportedTools'] ) : '';

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

$repository_host = '';
$repository_owner = '';
if ( $repository_url ) {
	$repository_host = (string) wp_parse_url( $repository_url, PHP_URL_HOST );
	$repository_path = trim( (string) wp_parse_url( $repository_url, PHP_URL_PATH ), '/' );
	$path_parts      = $repository_path ? explode( '/', $repository_path ) : array();
	$repository_owner = isset( $path_parts[0] ) ? $path_parts[0] : '';
}

$sync_status = class_exists( 'MCP_Catalog_Fetcher' ) ? MCP_Catalog_Fetcher::get_sync_status() : array();
$last_sync   = isset( $sync_status['last_success_gmt'] ) ? strtotime( (string) $sync_status['last_success_gmt'] ) : false;
$last_sync_label = $last_sync
	? sprintf( __( '%s ago', 'oboto' ), human_time_diff( $last_sync, time() ) )
	: '';

$has_configuration = ! empty( $environment ) || ! empty( $container_config ) || ! empty( $remote_config );
$section_links      = array(
	'about'    => __( 'About', 'oboto' ),
	'mcp-info' => __( 'MCP Info', 'oboto' ),
);
if ( $tools ) {
	$section_links['tools'] = __( 'Tools', 'oboto' );
}
if ( $has_configuration ) {
	$section_links['configuration'] = __( 'Configuration', 'oboto' );
}

$related = array();
if ( $categories ) {
	$related_ids = get_posts(
		array(
			'post_type'              => MCP_Server_Sync::POST_TYPE,
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'post__not_in'           => array( $post_id ),
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		)
	);
	foreach ( $related_ids as $related_id ) {
		$related_server     = MCP_Server_Sync::get_payload( $related_id );
		$related_categories = isset( $related_server['categories'] ) && is_array( $related_server['categories'] ) ? $related_server['categories'] : array();
		$shared_categories  = array_intersect( $categories, $related_categories );
		if ( $shared_categories ) {
			$related[] = array(
				'id'     => (int) $related_id,
				'score'  => count( $shared_categories ),
				'name'   => isset( $related_server['name'] ) ? (string) $related_server['name'] : get_the_title( $related_id ),
				'icon'   => isset( $related_server['icon'] ) ? (string) $related_server['icon'] : '',
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
	$related = array_slice( $related, 0, 4 );
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
						<?php if ( $user_type_label ) : ?><span class="mcp-server-single__badge"><?php echo esc_html( $user_type_label ); ?></span><?php endif; ?>
					</div>
					<h1><?php echo esc_html( $name ); ?></h1>
					<?php if ( $short_description ) : ?><p class="mcp-server-single__lede"><?php echo esc_html( $short_description ); ?></p><?php endif; ?>
				</div>
			</div>

			<div class="mcp-server-single__hero-meta" aria-label="<?php esc_attr_e( 'Server summary', 'oboto' ); ?>">
				<?php if ( $tools ) : ?><span><strong><?php echo esc_html( count( $tools ) ); ?></strong> <?php esc_html_e( 'tools', 'oboto' ); ?></span><?php endif; ?>
				<?php if ( $runtime_label ) : ?><span><?php echo esc_html( $runtime_label ); ?></span><?php endif; ?>
				<?php if ( $repository_owner ) : ?><span><?php echo esc_html( $repository_owner ); ?></span><?php endif; ?>
				<?php if ( $last_sync_label ) : ?><span><?php echo esc_html( sprintf( __( 'Updated %s', 'oboto' ), $last_sync_label ) ); ?></span><?php endif; ?>
			</div>

			<div class="mcp-server-single__actions">
				<?php if ( $repository_url ) : ?>
					<a class="mcp-server-single__button mcp-server-single__button--primary" href="<?php echo esc_url( $repository_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View on GitHub', 'oboto' ); ?><span aria-hidden="true">↗</span></a>
				<?php endif; ?>
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

				<section id="mcp-info" class="mcp-server-single__section">
					<h2><?php esc_html_e( 'MCP Info', 'oboto' ); ?></h2>
					<dl class="mcp-server-single__info-list">
						<?php if ( $runtime_label ) : ?><div><dt><?php esc_html_e( 'Runtime', 'oboto' ); ?></dt><dd><?php echo esc_html( $runtime_label ); ?></dd></div><?php endif; ?>
						<?php if ( $user_type_label ) : ?><div><dt><?php esc_html_e( 'User type', 'oboto' ); ?></dt><dd><?php echo esc_html( $user_type_label ); ?></dd></div><?php endif; ?>
						<?php if ( $repository_host ) : ?><div><dt><?php esc_html_e( 'Repository host', 'oboto' ); ?></dt><dd><?php echo esc_html( $repository_host ); ?></dd></div><?php endif; ?>
						<?php if ( $tools ) : ?><div><dt><?php esc_html_e( 'Documented tools', 'oboto' ); ?></dt><dd><?php echo esc_html( count( $tools ) ); ?></dd></div><?php endif; ?>
						<?php if ( $unsupported ) : ?><div><dt><?php esc_html_e( 'Unsupported tools', 'oboto' ); ?></dt><dd><?php echo esc_html( $unsupported ); ?></dd></div><?php endif; ?>
					</dl>
				</section>

				<?php if ( $tools ) : ?>
					<section id="tools" class="mcp-server-single__section">
						<div class="mcp-server-single__section-heading"><h2><?php esc_html_e( 'Tools', 'oboto' ); ?></h2><span><?php echo esc_html( count( $tools ) ); ?></span></div>
						<div class="mcp-server-single__tools">
							<?php foreach ( $tools as $tool ) : ?>
								<?php
								if ( ! is_array( $tool ) || empty( $tool['name'] ) ) {
									continue;
								}
								$tool_params = isset( $tool['params'] ) && is_array( $tool['params'] ) ? $tool['params'] : array();
								?>
								<details class="mcp-server-single__tool">
									<summary><code><?php echo esc_html( (string) $tool['name'] ); ?></code><span aria-hidden="true"></span></summary>
									<div class="mcp-server-single__tool-body">
										<?php if ( ! empty( $tool['description'] ) ) : ?><p><?php echo esc_html( (string) $tool['description'] ); ?></p><?php endif; ?>
										<?php if ( $tool_params ) : ?>
											<h3><?php esc_html_e( 'Parameters', 'oboto' ); ?></h3>
											<dl class="mcp-server-single__params">
												<?php foreach ( $tool_params as $param_name => $param_description ) : ?><div><dt><code><?php echo esc_html( (string) $param_name ); ?></code></dt><dd><?php echo esc_html( $format_value( $param_description ) ); ?></dd></div><?php endforeach; ?>
											</dl>
										<?php endif; ?>
									</div>
								</details>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>

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
					<h2><?php esc_html_e( 'At a glance', 'oboto' ); ?></h2>
					<dl>
						<?php if ( $runtime_label ) : ?><div><dt><?php esc_html_e( 'Runtime', 'oboto' ); ?></dt><dd><?php echo esc_html( $runtime_label ); ?></dd></div><?php endif; ?>
						<?php if ( $tools ) : ?><div><dt><?php esc_html_e( 'Tools', 'oboto' ); ?></dt><dd><?php echo esc_html( count( $tools ) ); ?></dd></div><?php endif; ?>
						<?php if ( $required_secrets ) : ?><div><dt><?php esc_html_e( 'Secrets', 'oboto' ); ?></dt><dd><?php echo esc_html( $required_secrets ); ?></dd></div><?php endif; ?>
						<?php if ( $repository_owner ) : ?><div><dt><?php esc_html_e( 'Publisher', 'oboto' ); ?></dt><dd><?php echo esc_html( $repository_owner ); ?></dd></div><?php endif; ?>
					</dl>
				</div>

				<?php if ( $related ) : ?>
					<div class="mcp-server-single__side-card">
						<h2><?php esc_html_e( 'Related servers', 'oboto' ); ?></h2>
						<ul class="mcp-server-single__related">
							<?php foreach ( $related as $related_item ) : ?><li><a href="<?php echo esc_url( get_permalink( $related_item['id'] ) ); ?>"><?php if ( $related_item['icon'] ) : ?><img src="<?php echo esc_url( $related_item['icon'] ); ?>" alt="" loading="lazy" aria-hidden="true"><?php endif; ?><span><?php echo esc_html( $related_item['name'] ); ?></span></a></li><?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="mcp-server-single__side-card">
					<h2><?php esc_html_e( 'Source', 'oboto' ); ?></h2>
					<a class="mcp-server-single__source-link" href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $source_file ? $source_file : __( 'MCP Catalog repository', 'oboto' ) ); ?><span aria-hidden="true">↗</span></a>
					<?php if ( $last_sync_label ) : ?><p><?php echo esc_html( sprintf( __( 'Catalog synchronized %s.', 'oboto' ), $last_sync_label ) ); ?></p><?php endif; ?>
				</div>

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
