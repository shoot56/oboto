(function () {
	'use strict';

	const rootSelector = '[data-how-obot-works]';
	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	function initBlock(root) {
		if (root.dataset.howObotInitialized === 'true') {
			return;
		}

		const tabs = Array.from(root.querySelectorAll('[data-how-obot-tab]'));
		const slides = Array.from(root.querySelectorAll('[data-how-obot-slide]'));
		const tabList = tabs[0] ? tabs[0].parentElement : null;
		const progress = root.querySelector('[data-how-obot-progress]');
		const carousel = root.querySelector('[data-how-obot-carousel]');
		const previous = root.querySelector('[data-how-obot-prev]');
		const next = root.querySelector('[data-how-obot-next]');

		if (!tabs.length || tabs.length !== slides.length || !carousel) {
			return;
		}

		root.dataset.howObotInitialized = 'true';

		const interval = Math.max(Number(root.dataset.howObotInterval) || 5000, 1000);
		const pauseReasons = new Set(['offscreen']);
		let current = 0;
		let timer = null;
		let startedAt = 0;
		let remaining = interval;

		function isPaused() {
			return pauseReasons.size > 0 || tabs.length < 2;
		}

		function syncVideoPlayback(resetActiveVideo) {
			slides.forEach(function (slide, slideIndex) {
				const shouldPlay = slideIndex === current && pauseReasons.size === 0;

				slide.querySelectorAll('[data-how-obot-video]').forEach(function (video) {
					if (!shouldPlay) {
						video.pause();
						return;
					}

					if (resetActiveVideo && video.readyState > 0) {
						video.currentTime = 0;
					}

					const playPromise = video.play();
					if (playPromise && typeof playPromise.catch === 'function') {
						playPromise.catch(function () {});
					}
				});
			});
		}

		function restartProgress() {
			if (!progress || reducedMotion.matches || tabs.length < 2) {
				return;
			}

			progress.style.animation = 'none';
			void progress.offsetWidth;
			progress.style.animation = 'obot-how-obot-progress ' + interval + 'ms linear forwards';
		}

		function schedule() {
			window.clearTimeout(timer);
			if (isPaused()) {
				return;
			}

			startedAt = window.performance.now();
			timer = window.setTimeout(function () {
				show(current + 1);
			}, remaining);
		}

		function syncPausedState() {
			const paused = isPaused();
			root.classList.toggle('is-paused', paused);

			if (paused) {
				if (timer) {
					remaining = Math.max(0, remaining - (window.performance.now() - startedAt));
				}
				window.clearTimeout(timer);
				timer = null;
				syncVideoPlayback(false);
				return;
			}

			schedule();
			syncVideoPlayback(false);
		}

		function setPauseReason(reason, shouldPause) {
			if (shouldPause) {
				pauseReasons.add(reason);
			} else {
				pauseReasons.delete(reason);
			}

			syncPausedState();
		}

		function restartSlideAnimations(slide) {
			const animatedElements = Array.from(
				slide.querySelectorAll(
					'.obot-how-obot-works__image, .obot-how-obot-works__copy > *'
				)
			);

			animatedElements.forEach(function (element) {
				element.style.animation = 'none';
			});
			void slide.offsetWidth;
			animatedElements.forEach(function (element) {
				element.style.removeProperty('animation');
			});
		}

		function show(index, focusTab) {
			current = (index + slides.length) % slides.length;

			tabs.forEach(function (tab, tabIndex) {
				const active = tabIndex === current;
				tab.classList.toggle('is-active', active);
				tab.setAttribute('aria-selected', active ? 'true' : 'false');
				tab.tabIndex = active ? 0 : -1;
			});

			if (tabList && tabList.scrollWidth > tabList.clientWidth) {
				const activeTab = tabs[current];
				const targetLeft = activeTab.offsetLeft - ((tabList.clientWidth - activeTab.offsetWidth) / 2);
				tabList.scrollTo({
					left: Math.max(0, targetLeft),
					behavior: reducedMotion.matches ? 'auto' : 'smooth'
				});
			}

			slides.forEach(function (slide, slideIndex) {
				const active = slideIndex === current;
				slide.hidden = !active;
				slide.classList.toggle('is-active', active);
			});
			restartSlideAnimations(slides[current]);
			syncVideoPlayback(true);

			if (focusTab) {
				tabs[current].focus();
			}

			remaining = interval;
			restartProgress();
			schedule();
		}

		tabs.forEach(function (tab, index) {
			tab.addEventListener('click', function () {
				show(index);
			});

			tab.addEventListener('keydown', function (event) {
				let nextIndex = null;

				if (event.key === 'ArrowLeft') {
					nextIndex = current - 1;
				} else if (event.key === 'ArrowRight') {
					nextIndex = current + 1;
				} else if (event.key === 'Home') {
					nextIndex = 0;
				} else if (event.key === 'End') {
					nextIndex = tabs.length - 1;
				}

				if (nextIndex === null) {
					return;
				}

				event.preventDefault();
				show(nextIndex, true);
			});
		});

		if (previous) {
			previous.addEventListener('click', function () {
				show(current - 1);
			});
		}

		if (next) {
			next.addEventListener('click', function () {
				show(current + 1);
			});
		}

		carousel.addEventListener('mouseenter', function () {
			setPauseReason('hover', true);
		});

		carousel.addEventListener('mouseleave', function () {
			setPauseReason('hover', false);
		});

		document.addEventListener('visibilitychange', function () {
			setPauseReason('hidden', document.hidden);
		});
		setPauseReason('hidden', document.hidden);

		function handleMotionPreference() {
			setPauseReason('reduced-motion', reducedMotion.matches);
		}

		if (typeof reducedMotion.addEventListener === 'function') {
			reducedMotion.addEventListener('change', handleMotionPreference);
		} else if (typeof reducedMotion.addListener === 'function') {
			reducedMotion.addListener(handleMotionPreference);
		}
		handleMotionPreference();

		if ('IntersectionObserver' in window) {
			const observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					setPauseReason('offscreen', !entry.isIntersecting);
				});
			}, { threshold: 0.15 });
			observer.observe(root);
		} else {
			setPauseReason('offscreen', false);
		}

		show(0);
	}

	function initAll(scope) {
		if (scope.matches && scope.matches(rootSelector)) {
			initBlock(scope);
		}

		scope.querySelectorAll(rootSelector).forEach(initBlock);
	}

	function boot() {
		initAll(document);

		const observer = new MutationObserver(function (mutations) {
			mutations.forEach(function (mutation) {
				mutation.addedNodes.forEach(function (node) {
					if (node.nodeType === Node.ELEMENT_NODE) {
						initAll(node);
					}
				});
			});
		});

		observer.observe(document.body, { childList: true, subtree: true });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
