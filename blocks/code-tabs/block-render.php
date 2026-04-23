<?php

/**
 * Block template file: Code Tabs.
 *
 * @param array       $block      The block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID this block is saved to.
 */

$id = 'code-tabs-' . $block['id'];
if (!empty($block['anchor'])) {
	$id = $block['anchor'];
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'code-tabs',
	)
);

$language_labels = array(
	'bash'       => 'Bash',
	'css'        => 'CSS',
	'html'       => 'HTML',
	'javascript' => 'JavaScript',
	'json'       => 'JSON',
	'jsx'        => 'JSX',
	'markdown'   => 'Markdown',
	'php'        => 'PHP',
	'python'     => 'Python',
	'scss'       => 'SCSS',
	'sql'        => 'SQL',
	'text'       => 'Plain text',
	'tsx'        => 'TSX',
	'typescript' => 'TypeScript',
	'xml'        => 'XML',
	'yaml'       => 'YAML',
);

$tabs = get_field('tabs');

if (!is_array($tabs)) {
	$tabs = array();
}

if (empty($tabs)) {
	if ($is_preview) {
		?>
		<section id="<?php echo esc_attr($id); ?>" <?php echo $wrapper_attributes; ?>>
			<div class="code-tabs__empty">
				<?php esc_html_e('Add at least one code tab in block settings.', 'oboto'); ?>
			</div>
		</section>
		<?php
	}
	return;
}
?>
<section
	id="<?php echo esc_attr($id); ?>"
	<?php echo $wrapper_attributes; ?>
	data-code-tabs
	data-shiki-theme="github-dark"
	data-copy-label="<?php echo esc_attr__('Copy', 'oboto'); ?>"
	data-copied-label="<?php echo esc_attr__('Copied', 'oboto'); ?>"
>
	<div class="code-tabs__shell">
		<div class="code-tabs__header">
			<div class="code-tabs__tablist" role="tablist" aria-label="<?php esc_attr_e('Code language selector', 'oboto'); ?>">
				<?php foreach ($tabs as $index => $tab) : ?>
					<?php
					$tab_number = $index + 1;
					$language = isset($tab['language']) ? sanitize_text_field($tab['language']) : 'text';
					$custom_language = isset($tab['custom_language']) ? trim((string) $tab['custom_language']) : '';
					$resolved_language = 'custom' === $language && '' !== $custom_language ? $custom_language : $language;
					$label = isset($tab['tab_label']) ? trim((string) $tab['tab_label']) : '';
					if ('' === $label) {
						$label = isset($language_labels[$resolved_language]) ? $language_labels[$resolved_language] : ucfirst($resolved_language);
					}
					$is_active = 0 === $index;
					$tab_id = $id . '-tab-' . $tab_number;
					$panel_id = $id . '-panel-' . $tab_number;
					?>
					<button
						type="button"
						id="<?php echo esc_attr($tab_id); ?>"
						class="code-tabs__tab<?php echo $is_active ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
						aria-controls="<?php echo esc_attr($panel_id); ?>"
						data-code-tabs-trigger
					>
						<?php echo esc_html($label); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<button
				type="button"
				class="code-tabs__copy"
				data-code-tabs-copy
				aria-label="<?php echo esc_attr__('Copy', 'oboto'); ?>"
				title="<?php echo esc_attr__('Copy', 'oboto'); ?>"
			>
				<span class="screen-reader-text code-tabs__copy-label"><?php esc_html_e('Copy', 'oboto'); ?></span>
				<span class="code-tabs__copy-icon" aria-hidden="true">
					<svg class="code-tabs__copy-icon-copy" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M5.5028 4.62704L5.5 6.75V17.2542C5.5 19.0491 6.95507 20.5042 8.75 20.5042L17.3663 20.5045C17.0573 21.3782 16.224 22.0042 15.2444 22.0042H8.75C6.12665 22.0042 4 19.8776 4 17.2542V6.75C4 5.76929 4.62745 4.93512 5.5028 4.62704ZM17.75 2C18.9926 2 20 3.00736 20 4.25V17.25C20 18.4926 18.9926 19.5 17.75 19.5H8.75C7.50736 19.5 6.5 18.4926 6.5 17.25V4.25C6.5 3.00736 7.50736 2 8.75 2H17.75ZM17.75 3.5H8.75C8.33579 3.5 8 3.83579 8 4.25V17.25C8 17.6642 8.33579 18 8.75 18H17.75C18.1642 18 18.5 17.6642 18.5 17.25V4.25C18.5 3.83579 18.1642 3.5 17.75 3.5Z" fill="currentColor"/>
					</svg>
					<svg class="code-tabs__copy-icon-check" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</span>
			</button>
		</div>

		<div class="code-tabs__panels">
			<?php foreach ($tabs as $index => $tab) : ?>
				<?php
				$tab_number = $index + 1;
				$language = isset($tab['language']) ? sanitize_text_field($tab['language']) : 'text';
				$custom_language = isset($tab['custom_language']) ? trim((string) $tab['custom_language']) : '';
				$resolved_language = 'custom' === $language && '' !== $custom_language ? $custom_language : $language;
				$code = isset($tab['code']) ? (string) $tab['code'] : '';
				$lines = preg_split('/\r\n|\r|\n/', $code);
				$line_count = max(1, is_array($lines) ? count($lines) : 1);
				$is_active = 0 === $index;
				$tab_id = $id . '-tab-' . $tab_number;
				$panel_id = $id . '-panel-' . $tab_number;
				?>
				<div
					id="<?php echo esc_attr($panel_id); ?>"
					class="code-tabs__panel<?php echo $is_active ? ' is-active' : ''; ?>"
					role="tabpanel"
					aria-labelledby="<?php echo esc_attr($tab_id); ?>"
					data-code-tabs-panel
					data-language="<?php echo esc_attr($resolved_language); ?>"
					<?php echo $is_active ? '' : 'hidden'; ?>
				>
					<textarea class="code-tabs__source" data-code-tabs-source hidden><?php echo esc_textarea($code); ?></textarea>

					<div class="code-tabs__editor">
						<div class="code-tabs__lines" aria-hidden="true">
							<?php for ($line = 1; $line <= $line_count; $line++) : ?>
								<span><?php echo esc_html((string) $line); ?></span>
							<?php endfor; ?>
						</div>

						<div class="code-tabs__code" data-code-tabs-output>
							<pre class="code-tabs__fallback"><code><?php echo esc_html($code); ?></code></pre>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
