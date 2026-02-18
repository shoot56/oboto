(function () {
	'use strict';

	var DEBOUNCE_MS = 200;

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

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run);
	} else {
		run();
	}
})();
