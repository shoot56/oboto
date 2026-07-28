<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'product-hero-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-product-hero';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow = trim( (string) get_field( 'eyebrow' ) );
$title   = trim( (string) get_field( 'title' ) );
$text    = trim( (string) get_field( 'text' ) );
$buttons = get_field( 'buttons' );

$allowed_title_tags = array(
	'br'   => array(),
	'span' => array(
		'class' => true,
	),
);

$button_items = array();
if ( is_array( $buttons ) ) {
	foreach ( $buttons as $index => $row ) {
		if ( ! is_array( $row ) || empty( $row['button'] ) || ! is_array( $row['button'] ) || empty( $row['button']['url'] ) ) {
			continue;
		}

		$variant = isset( $row['variant'] ) ? sanitize_key( (string) $row['variant'] ) : '';
		if ( ! in_array( $variant, array( 'primary', 'secondary' ), true ) ) {
			$variant = count( $button_items ) === 0 ? 'primary' : 'secondary';
		}

		$button_items[] = array(
			'button'  => $row['button'],
			'variant' => $variant,
		);
	}
}

$dashboard_tabs = array(
	array(
		'label' => 'Dashboard',
		'image' => 'launch.png',
	),
	array(
		'label' => 'Audit Logs',
		'image' => 'audit.png',
	),
	array(
		'label' => 'MCP Catalog',
		'image' => 'build.png',
	),
	array(
		'label' => 'Filters',
		'image' => 'compliance.png',
	),
	array(
		'label' => 'Access Policies',
		'image' => 'policy.png',
	),
	array(
		'label' => 'Devices',
		'image' => 'discover.png',
	),
	array(
		'label' => 'Users',
		'image' => 'users.png',
	),
	array(
		'label' => 'Authentication',
		'image' => 'auth.png',
	),
);

$animation_assets_url = trailingslashit( get_template_directory_uri() ) . 'images/product-hero/';
$scan_logo_url        = $animation_assets_url . 'obot-icon-blue.svg';
$has_heading          = trim( wp_strip_all_tags( $title ) ) !== '';

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?> data-product-hero>
	<div class="obot-product-hero__grid" aria-hidden="true"></div>

	<div class="obot-product-hero__inner">
		<div class="obot-product-hero__copy">
			<?php if ( $eyebrow ) : ?>
				<div class="obot-product-hero__eyebrow"<?php oboto_the_aos_attributes( 100 ); ?>>
					<span class="obot-product-hero__eyebrow-status" aria-hidden="true">
						<span class="obot-product-hero__eyebrow-pulse"></span>
						<span class="obot-product-hero__eyebrow-dot"></span>
					</span>
					<span><?php echo esc_html( $eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $has_heading ) : ?>
				<h1 class="obot-product-hero__title"<?php oboto_the_aos_attributes( 180 ); ?>>
					<?php echo wp_kses( $title, $allowed_title_tags ); ?>
				</h1>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="obot-product-hero__text"<?php oboto_the_aos_attributes( 260 ); ?>><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>

			<?php if ( $button_items ) : ?>
				<div class="obot-product-hero__actions"<?php oboto_the_aos_attributes( 340 ); ?>>
					<?php foreach ( $button_items as $item ) : ?>
						<?php
						$button      = $item['button'];
						$link_target = ! empty( $button['target'] ) ? $button['target'] : '';
						$link_title  = ! empty( $button['title'] ) ? $button['title'] : $button['url'];
						?>
						<a
							class="obot-product-hero__button obot-product-hero__button--<?php echo esc_attr( $item['variant'] ); ?>"
							href="<?php echo esc_url( $button['url'] ); ?>"
							<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
							<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						>
							<span><?php echo esc_html( $link_title ); ?></span>
							<?php if ( $item['variant'] === 'primary' ) : ?>
								<span class="obot-product-hero__button-arrow" aria-hidden="true"></span>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="obot-product-hero__visual"<?php oboto_the_aos_attributes( 260 ); ?>>
			<div class="obot-product-hero__animation-viewport" data-product-hero-animation-viewport aria-hidden="true">
				<div class="obot-product-hero__animation" data-product-hero-animation>
					<div class="obot-product-hero__app-window">
						<div class="obot-product-hero__app-bar">
							<div class="obot-product-hero__app-lights">
								<span></span><span></span><span></span>
							</div>
							<div class="obot-product-hero__app-address" data-product-hero-address>claude.ai/code</div>
							<div class="obot-product-hero__app-status">
								<span class="obot-product-hero__status-dot"></span>
								<span data-product-hero-status>active</span>
							</div>
						</div>

						<div class="obot-product-hero__app-toolbar">
							<div class="obot-product-hero__code-path" data-product-hero-code-toolbar>
								<span>acme-crm</span>
								<b>/</b>
								<span>src</span>
								<b>/</b>
								<strong>api.ts</strong>
								<div class="obot-product-hero__code-flags">
									<span class="is-managed">2 MCPs active</span>
									<span class="is-unmanaged">! unmanaged</span>
								</div>
							</div>
							<div class="obot-product-hero__dashboard-progress" data-product-hero-dashboard-toolbar>
								<div><i data-product-hero-dashboard-progress></i></div>
							</div>
						</div>

						<div class="obot-product-hero__app-screen">
							<div class="obot-product-hero__scene obot-product-hero__scene--code is-active" data-product-hero-scene="code">
								<div class="obot-product-hero__file-tree">
									<strong>EXPLORER</strong>
									<span class="is-folder">v acme-crm</span>
									<span class="is-folder">&nbsp;&nbsp;v src</span>
									<span class="is-file is-active">&nbsp;&nbsp;&nbsp;&nbsp;api.ts</span>
									<span class="is-file">&nbsp;&nbsp;&nbsp;&nbsp;routes.ts</span>
									<span class="is-file">&nbsp;&nbsp;&nbsp;&nbsp;models.ts</span>
									<span class="is-file">&nbsp;&nbsp;package.json</span>
									<span class="is-file is-sensitive">&nbsp;&nbsp;! .env.local</span>
									<strong class="obot-product-hero__tree-section">ACTIVE MCPS</strong>
									<span class="is-mcp"><i></i>salesforce</span>
									<span class="is-mcp"><i></i>notion</span>
									<span class="is-mcp is-muted"><i></i>+10 more</span>
								</div>

								<div class="obot-product-hero__chat">
									<div class="obot-product-hero__model-bar">
										<span>C</span>
										<strong>claude-3-5-sonnet-20241022</strong>
										<small>A. Chen / Personal account</small>
									</div>

									<div class="obot-product-hero__messages">
										<div class="obot-product-hero__message obot-product-hero__message--user">
											<strong>A. Chen</strong>
											<p>Pull the top 10 open deals from Salesforce and add a summary to our Q2 Pipeline Notion doc.</p>
										</div>

										<div class="obot-product-hero__tool-card">
											<small>tool use</small>
											<strong>salesforce_mcp &rarr; list_opportunities</strong>
											<code>{ stage: "Open", limit: 10, sort: "amount:desc" }</code>
											<span>&rarr; 10 records returned</span>
										</div>

										<div class="obot-product-hero__tool-card">
											<small>tool use</small>
											<strong>notion_mcp &rarr; append_to_page</strong>
											<code>{ page_id: "q2-pipeline-doc", content: "..." }</code>
											<span>&rarr; success / page updated</span>
										</div>

										<div class="obot-product-hero__message obot-product-hero__message--claude">
											<strong><i>C</i> Claude</strong>
											<p>Done! Fetched your top 10 open deals totalling <b>$4.2M</b> and added the summary to <em>Q2 Pipeline Doc</em>. Top deal: Acme Corp at $840K.</p>
										</div>

										<div class="obot-product-hero__typing">
											<span>C</span>
											<i></i><i></i><i></i>
										</div>
									</div>
								</div>
							</div>

							<div class="obot-product-hero__scene obot-product-hero__scene--dashboard" data-product-hero-scene="dashboard">
								<div class="obot-product-hero__dashboard-nav">
									<?php foreach ( $dashboard_tabs as $tab_index => $dashboard_tab ) : ?>
										<div class="obot-product-hero__dashboard-tab<?php echo $tab_index === 0 ? ' is-active' : ''; ?>" data-product-hero-tab="<?php echo esc_attr( $tab_index ); ?>">
											<?php echo esc_html( $dashboard_tab['label'] ); ?>
										</div>
									<?php endforeach; ?>
								</div>

								<div class="obot-product-hero__dashboard-content">
									<?php foreach ( $dashboard_tabs as $tab_index => $dashboard_tab ) : ?>
										<div class="obot-product-hero__dashboard-panel<?php echo $tab_index === 0 ? ' is-active' : ''; ?>" data-product-hero-panel="<?php echo esc_attr( $tab_index ); ?>">
											<img
												class="obot-product-hero__dashboard-image"
												src="<?php echo esc_url( $animation_assets_url . $dashboard_tab['image'] ); ?>"
												alt=""
												decoding="async"
											>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>

					<div class="obot-product-hero__scan" data-product-hero-scan>
						<div class="obot-product-hero__scan-beam"></div>
						<div class="obot-product-hero__scan-card">
							<div class="obot-product-hero__scan-header">
								<div class="obot-product-hero__scan-logo">
									<img src="<?php echo esc_url( $scan_logo_url ); ?>" alt="">
								</div>
								<div class="obot-product-hero__scan-copy">
									<strong>Obot AI Control Plane</strong>
									<span>Unmanaged AI session detected on this device</span>
								</div>
								<div class="obot-product-hero__scan-state"><i></i><span>SCANNING</span></div>
							</div>
							<div class="obot-product-hero__scan-meta">
								<span>Analyzing MCPs / permissions / data flows</span>
								<b><span data-product-hero-scan-value>0</span>%</b>
							</div>
							<div class="obot-product-hero__scan-progress"><i data-product-hero-scan-bar></i></div>
							<div class="obot-product-hero__scan-findings">
								<div><i>&#9888;</i><span><strong>12 unapproved MCP servers</strong><small>filesystem, postgres, stripe, salesforce, notion +7 more - outside policy</small></span></div>
								<div><i>&#9888;</i><span><strong>Zero audit trail</strong><small>All Salesforce and Notion calls are unlogged - no visibility for the security team</small></span></div>
								<div class="is-warning"><i>!</i><span><strong>Sensitive data exposure risk</strong><small>.env.local is readable by Claude / CRM data is flowing to a personal account</small></span></div>
							</div>
							<div class="obot-product-hero__scan-actions">
								<span>Bring Under Management <i aria-hidden="true">&rarr;</i></span>
								<span>Dismiss</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
