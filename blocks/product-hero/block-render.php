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

$has_heading = trim( wp_strip_all_tags( $title ) ) !== '';

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
							<div class="obot-product-hero__app-address" data-product-hero-address>Claude Code</div>
							<div class="obot-product-hero__app-status">
								<span class="obot-product-hero__status-dot"></span>
								<span data-product-hero-status>Connected</span>
							</div>
						</div>

						<div class="obot-product-hero__app-toolbar">
							<div class="obot-product-hero__code-path" data-product-hero-code-toolbar>
								<span class="obot-product-hero__code-mark">C</span>
								<span>acme-platform / infrastructure</span>
							</div>
							<div class="obot-product-hero__dashboard-progress" data-product-hero-dashboard-toolbar>
								<span>AI Control Plane</span>
								<div><i data-product-hero-dashboard-progress></i></div>
							</div>
						</div>

						<div class="obot-product-hero__app-screen">
							<div class="obot-product-hero__scene obot-product-hero__scene--code is-active" data-product-hero-scene="code">
								<div class="obot-product-hero__file-tree">
									<strong>EXPLORER</strong>
									<span class="is-folder">v acme-platform</span>
									<span class="is-folder">&nbsp;&nbsp;v infrastructure</span>
									<span class="is-file is-active">&nbsp;&nbsp;&nbsp;&nbsp;gateway.yaml</span>
									<span class="is-file">&nbsp;&nbsp;&nbsp;&nbsp;policies.yaml</span>
									<span class="is-folder">&nbsp;&nbsp;v services</span>
									<span class="is-file">&nbsp;&nbsp;&nbsp;&nbsp;agents.ts</span>
									<span class="is-file">&nbsp;&nbsp;&nbsp;&nbsp;tools.ts</span>
									<span class="is-file">&nbsp;&nbsp;README.md</span>
								</div>
								<div class="obot-product-hero__code-editor">
									<div class="obot-product-hero__code-tab">gateway.yaml</div>
									<ol class="obot-product-hero__code-lines">
										<li><span class="is-key">gateway</span>:</li>
										<li>&nbsp;&nbsp;<span class="is-key">name</span>: <span class="is-string">acme-production</span></li>
										<li>&nbsp;&nbsp;<span class="is-key">authentication</span>:</li>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;<span class="is-key">provider</span>: <span class="is-string">oidc</span></li>
										<li>&nbsp;&nbsp;<span class="is-key">policies</span>:</li>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;- <span class="is-string">production-access</span></li>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;- <span class="is-string">audit-all-tools</span></li>
										<li>&nbsp;&nbsp;<span class="is-key">servers</span>:</li>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;- <span class="is-string">github</span></li>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;- <span class="is-string">postgres</span></li>
									</ol>
								</div>
								<div class="obot-product-hero__assistant-pane">
									<div class="obot-product-hero__assistant-title"><span>AI</span> Claude Code</div>
									<div class="obot-product-hero__assistant-message">Review this MCP configuration before deployment.</div>
									<div class="obot-product-hero__assistant-action">
										<strong>Using tool</strong>
										<span>mcp_security_scan</span>
									</div>
									<div class="obot-product-hero__assistant-result">
										<span class="is-success">&#10003;</span>
										<div><strong>Configuration ready</strong><small>Connecting to Obot Control Plane...</small></div>
									</div>
								</div>
							</div>

							<div class="obot-product-hero__scene obot-product-hero__scene--dashboard" data-product-hero-scene="dashboard">
								<div class="obot-product-hero__dashboard-nav">
									<div class="obot-product-hero__dashboard-brand"><span>O</span> OBOT</div>
									<?php
									$dashboard_tabs = array(
										'Dashboard',
										'Audit Logs',
										'MCP Catalog',
										'Obot CLI',
										'Access Policies',
										'Devices',
										'Users',
										'Authentication',
									);
									?>
									<?php foreach ( $dashboard_tabs as $tab_index => $dashboard_tab ) : ?>
										<div class="obot-product-hero__dashboard-tab<?php echo $tab_index === 0 ? ' is-active' : ''; ?>" data-product-hero-tab="<?php echo esc_attr( $tab_index ); ?>">
											<span class="obot-product-hero__dashboard-tab-icon"></span>
											<span><?php echo esc_html( $dashboard_tab ); ?></span>
										</div>
									<?php endforeach; ?>
								</div>

								<div class="obot-product-hero__dashboard-content">
									<div class="obot-product-hero__dashboard-panel is-active" data-product-hero-panel="0">
										<div class="obot-product-hero__panel-heading"><div><strong>Dashboard</strong><span>Control plane overview</span></div><span class="obot-product-hero__live-pill">Live</span></div>
										<div class="obot-product-hero__metric-grid">
											<div><span>Active users</span><strong>1,284</strong><small>+12.4%</small></div>
											<div><span>MCP servers</span><strong>48</strong><small>All healthy</small></div>
											<div><span>Tool calls</span><strong>24.8k</strong><small>Last 24h</small></div>
											<div><span>Policies</span><strong>36</strong><small>Enforced</small></div>
										</div>
										<div class="obot-product-hero__activity-card">
											<div class="obot-product-hero__activity-head"><strong>Tool activity</strong><span>Last 7 days</span></div>
											<div class="obot-product-hero__chart-bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
										</div>
									</div>

									<div class="obot-product-hero__dashboard-panel" data-product-hero-panel="1">
										<div class="obot-product-hero__panel-heading"><div><strong>Audit Logs</strong><span>Every request, identity, and outcome</span></div><span class="obot-product-hero__filter-pill">24 hours</span></div>
										<div class="obot-product-hero__data-table">
											<div class="is-head"><span>Time</span><span>User</span><span>Tool</span><span>Status</span></div>
											<div><span>10:42:18</span><span>maya@acme.io</span><span>github.create_pr</span><span class="is-ok">Allowed</span></div>
											<div><span>10:41:52</span><span>liam@acme.io</span><span>postgres.query</span><span class="is-ok">Allowed</span></div>
											<div><span>10:39:06</span><span>agent-prod-02</span><span>slack.post</span><span class="is-blocked">Blocked</span></div>
											<div><span>10:37:44</span><span>sofia@acme.io</span><span>notion.search</span><span class="is-ok">Allowed</span></div>
										</div>
									</div>

									<div class="obot-product-hero__dashboard-panel" data-product-hero-panel="2">
										<div class="obot-product-hero__panel-heading"><div><strong>MCP Catalog</strong><span>Approved tools for every team</span></div><span class="obot-product-hero__filter-pill">48 servers</span></div>
										<div class="obot-product-hero__catalog-grid">
											<div><b>GH</b><strong>GitHub</strong><span>12 tools</span></div>
											<div><b>SL</b><strong>Slack</strong><span>8 tools</span></div>
											<div><b>PG</b><strong>Postgres</strong><span>6 tools</span></div>
											<div><b>NT</b><strong>Notion</strong><span>10 tools</span></div>
											<div><b>SF</b><strong>Salesforce</strong><span>9 tools</span></div>
											<div><b>+</b><strong>Add server</strong><span>Catalog</span></div>
										</div>
									</div>

									<div class="obot-product-hero__dashboard-panel" data-product-hero-panel="3">
										<div class="obot-product-hero__panel-heading"><div><strong>Obot CLI</strong><span>Discover shadow MCP usage</span></div><span class="obot-product-hero__live-pill">Scanning</span></div>
										<div class="obot-product-hero__terminal">
											<span><b>$</b> obot discover --organization acme</span>
											<span>Scanning managed developer devices...</span>
											<span class="is-terminal-ok">found 127 MCP configurations</span>
											<span class="is-terminal-warn">18 servers require policy review</span>
											<div><i data-product-hero-cli-progress></i></div>
											<small><span data-product-hero-cli-value>42</span>% complete</small>
										</div>
									</div>

									<div class="obot-product-hero__dashboard-panel" data-product-hero-panel="4">
										<div class="obot-product-hero__panel-heading"><div><strong>Access Policies</strong><span>Least-privilege controls by default</span></div><span class="obot-product-hero__filter-pill">36 active</span></div>
										<div class="obot-product-hero__policy-list">
											<div><i></i><span><strong>Production database access</strong><small>Engineering leads / Read only</small></span><b>Active</b></div>
											<div><i></i><span><strong>Source control write access</strong><small>Developers / Approved repositories</small></span><b>Active</b></div>
											<div><i></i><span><strong>External communication tools</strong><small>All employees / Monitored</small></span><b>Active</b></div>
										</div>
									</div>

									<div class="obot-product-hero__dashboard-panel" data-product-hero-panel="5">
										<div class="obot-product-hero__panel-heading"><div><strong>Devices</strong><span>Managed endpoints and MCP clients</span></div><span class="obot-product-hero__live-pill">243 online</span></div>
										<div class="obot-product-hero__device-grid">
											<div><i></i><span><strong>Maya's MacBook Pro</strong><small>macOS 15.3 / Compliant</small></span><b>Online</b></div>
											<div><i></i><span><strong>Build Agent 04</strong><small>Ubuntu 24.04 / Compliant</small></span><b>Online</b></div>
											<div><i></i><span><strong>Design Workstation</strong><small>Windows 11 / Review due</small></span><b>Online</b></div>
										</div>
									</div>

									<div class="obot-product-hero__dashboard-panel" data-product-hero-panel="6">
										<div class="obot-product-hero__panel-heading"><div><strong>Users</strong><span>People, teams, and service identities</span></div><span class="obot-product-hero__filter-pill">1,284 users</span></div>
										<div class="obot-product-hero__user-list">
											<div><i>MA</i><span><strong>Maya Anderson</strong><small>Engineering / Admin</small></span><b>Active</b></div>
											<div><i>LC</i><span><strong>Liam Chen</strong><small>Platform / Member</small></span><b>Active</b></div>
											<div><i>SR</i><span><strong>Sofia Reyes</strong><small>Product / Member</small></span><b>Active</b></div>
										</div>
									</div>

									<div class="obot-product-hero__dashboard-panel" data-product-hero-panel="7">
										<div class="obot-product-hero__panel-heading"><div><strong>Authentication</strong><span>One identity layer for every connection</span></div><span class="obot-product-hero__live-pill">Protected</span></div>
										<div class="obot-product-hero__auth-grid">
											<div><b>SSO</b><strong>Single Sign-On</strong><span>Microsoft Entra ID</span><small>Connected</small></div>
											<div><b>SC</b><strong>SCIM Provisioning</strong><span>Automated lifecycle</span><small>Active</small></div>
											<div><b>2F</b><strong>Multi-factor Auth</strong><span>Required for admins</span><small>Enforced</small></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="obot-product-hero__scan" data-product-hero-scan>
						<div class="obot-product-hero__scan-beam"></div>
						<div class="obot-product-hero__scan-card">
							<div class="obot-product-hero__scan-header">
								<div class="obot-product-hero__scan-logo">O</div>
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
								<div><i>!</i><span><strong>12 unapproved MCP servers</strong><small>filesystem, postgres, stripe, salesforce, notion +7 more</small></span></div>
								<div><i>!</i><span><strong>Zero audit trail</strong><small>Salesforce and Notion calls are unlogged</small></span></div>
								<div class="is-warning"><i>!</i><span><strong>Sensitive data exposure risk</strong><small>Environment and CRM data flowing to a personal account</small></span></div>
							</div>
							<div class="obot-product-hero__scan-actions">
								<span>Bring Under Management <i aria-hidden="true">-&gt;</i></span>
								<span>Dismiss</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
