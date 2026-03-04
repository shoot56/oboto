(function () {
	'use strict';

	var DEBOUNCE_MS = 200;
	var MCP_REFRESH_ACTION = 'mcp_list_refresh_cache';

	function queryAll(el, sel) {
		return (el || document).querySelectorAll(sel);
	}

	function debounce(fn, ms) {
		var t;
		return function () {
			var args = arguments;
			clearTimeout(t);
			t = setTimeout(function () {
				fn.apply(null, args);
			}, ms);
		};
	}

	/**
	 * Refresh button: use event delegation so it works when preview is injected after load (e.g. in editor iframe).
	 */
	function runRefreshButton() {
		document.addEventListener('click', function (e) {
			var btn = e.target && e.target.closest ? e.target.closest('.mcp-list__refresh-btn') : null;
			if (!btn) return;

			var actions = btn.closest('.mcp-list__editor-actions');
			if (!actions) return;

			var okEl = actions.querySelector('.mcp-list__refresh-ok');
			var ajaxUrl = actions.getAttribute('data-mcp-refresh-ajax-url');
			var nonce = actions.getAttribute('data-mcp-refresh-nonce');
			if (!ajaxUrl || !nonce) return;

			e.preventDefault();
			e.stopPropagation();
			btn.disabled = true;

			console.log('[MCP List] Refreshing catalog cache…');

			var formData = new FormData();
			formData.append('action', MCP_REFRESH_ACTION);
			formData.append('nonce', nonce);

			fetch(ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			})
				.then(function (res) { return res.json(); })
				.then(function (data) {
					if (data.success && okEl) {
						okEl.textContent = 'Cache refreshed.';
						okEl.classList.remove('mcp-list__refresh-ok--hidden');
					}
					console.log('[MCP List] Cache cleared successfully.', data.data && data.data.message ? data.data.message : '');
					btn.disabled = false;
				})
				.catch(function (err) {
					console.warn('[MCP List] Cache refresh failed.', err);
					btn.disabled = false;
					if (okEl) {
						okEl.textContent = 'Refresh failed.';
						okEl.classList.remove('mcp-list__refresh-ok--hidden');
					}
				});
		});
	}

	function run() {
		var grid = document.querySelector('[data-mcp-list-grid]');
		if (!grid) return;

		var container = grid.closest('.mcp-list');
		if (!container) return;

		var searchInput = container.querySelector('.mcp-list__search');
		var categorySelect = container.querySelector('.mcp-list__select');
		var countEl = container.querySelector('.mcp-list__count');
		var cards = queryAll(grid, '.mcp-list__card');
		var total = cards.length;

		var currentCategory = '';
		var currentQuery = '';

		function getCategories(card) {
			var raw = (card.getAttribute('data-category') || '').trim();
			return raw ? raw.split('|') : [];
		}

		function matchesCategory(card) {
			if (!currentCategory) return true;
			return getCategories(card).indexOf(currentCategory) !== -1;
		}

		function matchesSearch(card) {
			if (!currentQuery) return true;
			var text = (card.getAttribute('data-search') || '').toLowerCase();
			return text.indexOf(currentQuery) !== -1;
		}

		function updateVisibility() {
			var visible = 0;
			cards.forEach(function (card) {
				var show = matchesCategory(card) && matchesSearch(card);
				card.style.display = show ? '' : 'none';
				if (show) visible++;
			});

			if (countEl) {
				var msg = visible === total
					? (total === 1 ? 'Showing 1 server' : 'Showing ' + total + ' servers')
					: 'Showing ' + visible + ' of ' + total + ' servers';
				countEl.textContent = msg;
			}
		}

		function setCategory(category) {
			currentCategory = category || '';
			updateVisibility();
		}

		function setQuery(query) {
			currentQuery = (query || '').toLowerCase().trim();
			updateVisibility();
		}

		if (categorySelect) {
			categorySelect.addEventListener('change', function () {
				setCategory(categorySelect.value);
			});
		}

		if (searchInput) {
			searchInput.addEventListener('input', debounce(function () {
				setQuery(searchInput.value);
			}, DEBOUNCE_MS));
		}

		updateVisibility();
	}

	function init() {
		console.log('[MCP List] view-script loaded');
		runRefreshButton();
		run();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
