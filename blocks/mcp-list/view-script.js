(function () {
	'use strict';

	var DEBOUNCE_MS = 200;
	var DROPDOWN_MAX_HEIGHT = 320;
	var TYPEAHEAD_RESET_MS = 500;
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

	function enhanceCategorySelect(select) {
		if (!select || select.getAttribute('data-mcp-select-enhanced') === 'true') return;

		var selectId = select.id || ('mcp-list-category-' + Math.random().toString(36).slice(2));
		var triggerId = selectId + '-trigger';
		var listboxId = selectId + '-listbox';
		var wrapper = document.createElement('div');
		var trigger = document.createElement('button');
		var triggerText = document.createElement('span');
		var listbox = document.createElement('div');
		var optionEls = [];
		var typeahead = '';
		var typeaheadTimer;

		select.id = selectId;
		select.setAttribute('data-mcp-select-enhanced', 'true');
		select.setAttribute('aria-hidden', 'true');
		select.tabIndex = -1;
		select.classList.add('mcp-list__select--native');

		wrapper.className = 'mcp-list__custom-select';
		trigger.type = 'button';
		trigger.id = triggerId;
		trigger.className = 'mcp-list__select-trigger';
		trigger.setAttribute('aria-haspopup', 'listbox');
		trigger.setAttribute('aria-expanded', 'false');
		trigger.setAttribute('aria-controls', listboxId);
		triggerText.className = 'mcp-list__select-value';
		triggerText.id = triggerId + '-value';
		trigger.appendChild(triggerText);

		listbox.id = listboxId;
		listbox.className = 'mcp-list__select-dropdown';
		listbox.setAttribute('role', 'listbox');
		listbox.setAttribute('aria-labelledby', triggerId);
		listbox.hidden = true;

		Array.prototype.forEach.call(select.options, function (option, index) {
			var optionEl = document.createElement('div');
			optionEl.id = listboxId + '-option-' + index;
			optionEl.className = 'mcp-list__select-option';
			optionEl.setAttribute('role', 'option');
			optionEl.setAttribute('data-value', option.value);
			optionEl.tabIndex = -1;
			optionEl.textContent = option.textContent;
			listbox.appendChild(optionEl);
			optionEls.push(optionEl);
		});

		wrapper.appendChild(trigger);
		wrapper.appendChild(listbox);
		select.parentNode.insertBefore(wrapper, select.nextSibling);

		var filterWrap = select.closest('.mcp-list__filter-wrap');
		var label = filterWrap ? filterWrap.querySelector('label[for="' + selectId + '"]') : null;
		if (label) {
			label.id = label.id || (selectId + '-label');
			label.setAttribute('for', triggerId);
			trigger.setAttribute('aria-labelledby', label.id + ' ' + triggerText.id);
			listbox.setAttribute('aria-labelledby', label.id);
		}

		function selectedIndex() {
			return Math.max(select.selectedIndex, 0);
		}

		function syncSelectedState() {
			var index = selectedIndex();
			triggerText.textContent = select.options[index] ? select.options[index].textContent : '';
			optionEls.forEach(function (optionEl, optionIndex) {
				var isSelected = optionIndex === index;
				optionEl.setAttribute('aria-selected', isSelected ? 'true' : 'false');
				optionEl.classList.toggle('is-selected', isSelected);
			});
		}

		function focusOption(index) {
			if (!optionEls.length) return;
			var safeIndex = Math.max(0, Math.min(index, optionEls.length - 1));
			optionEls[safeIndex].focus();
			optionEls[safeIndex].scrollIntoView({ block: 'nearest' });
		}

		function openListbox(focusIndex) {
			var triggerRect = trigger.getBoundingClientRect();
			var preferredHeight = Math.min(DROPDOWN_MAX_HEIGHT, window.innerHeight * 0.5);
			var spaceBelow = window.innerHeight - triggerRect.bottom;
			wrapper.classList.toggle('is-above', spaceBelow < preferredHeight && triggerRect.top > preferredHeight);
			wrapper.classList.add('is-open');
			trigger.setAttribute('aria-expanded', 'true');
			listbox.hidden = false;
			if (typeof focusIndex === 'number') {
				window.requestAnimationFrame(function () { focusOption(focusIndex); });
			} else {
				window.requestAnimationFrame(function () {
					if (optionEls[selectedIndex()]) optionEls[selectedIndex()].scrollIntoView({ block: 'nearest' });
				});
			}
		}

		function closeListbox(restoreFocus) {
			wrapper.classList.remove('is-open');
			trigger.setAttribute('aria-expanded', 'false');
			listbox.hidden = true;
			if (restoreFocus) trigger.focus();
		}

		function chooseOption(index) {
			if (!select.options[index]) return;
			select.value = select.options[index].value;
			syncSelectedState();
			select.dispatchEvent(new Event('change', { bubbles: true }));
			closeListbox(true);
		}

		function focusedOptionIndex() {
			return optionEls.indexOf(document.activeElement);
		}

		function findTypeaheadMatch(query) {
			var normalized = query.toLowerCase();
			return optionEls.findIndex(function (optionEl) {
				return optionEl.textContent.toLowerCase().indexOf(normalized) === 0;
			});
		}

		trigger.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			if (wrapper.classList.contains('is-open')) {
				closeListbox(false);
			} else {
				openListbox();
			}
		});

		trigger.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
				event.preventDefault();
				openListbox(event.key === 'ArrowDown' ? selectedIndex() : optionEls.length - 1);
			} else if (event.key === 'Escape') {
				closeListbox(false);
			}
		});

		optionEls.forEach(function (optionEl, index) {
			optionEl.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				chooseOption(index);
			});
		});

		listbox.addEventListener('keydown', function (event) {
			var currentIndex = focusedOptionIndex();
			if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
				event.preventDefault();
				focusOption(currentIndex + (event.key === 'ArrowDown' ? 1 : -1));
			} else if (event.key === 'Home' || event.key === 'End') {
				event.preventDefault();
				focusOption(event.key === 'Home' ? 0 : optionEls.length - 1);
			} else if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				if (currentIndex !== -1) chooseOption(currentIndex);
			} else if (event.key === 'Escape') {
				event.preventDefault();
				closeListbox(true);
			} else if (event.key === 'Tab') {
				closeListbox(false);
			} else if (event.key.length === 1 && /\S/.test(event.key)) {
				typeahead += event.key;
				clearTimeout(typeaheadTimer);
				typeaheadTimer = setTimeout(function () { typeahead = ''; }, TYPEAHEAD_RESET_MS);
				var matchIndex = findTypeaheadMatch(typeahead);
				if (matchIndex !== -1) focusOption(matchIndex);
			}
		});

		document.addEventListener('click', function (event) {
			if (!wrapper.contains(event.target)) closeListbox(false);
		});

		select.addEventListener('change', syncSelectedState);
		syncSelectedState();
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

			console.log('[MCP List] Scheduling catalog refresh…');

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
						okEl.textContent = 'Catalog refresh scheduled.';
						okEl.classList.remove('mcp-list__refresh-ok--hidden');
					}
					console.log('[MCP List] Catalog refresh scheduled.', data.data && data.data.message ? data.data.message : '');
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
			enhanceCategorySelect(categorySelect);
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
