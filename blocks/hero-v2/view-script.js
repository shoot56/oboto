(function () {
	'use strict';

	const SELECTOR = '[data-hero-v2-rain]';
	const LINE_COUNT = 70;
	const GLITCH_RATE = 0.12;
	const DURATION_SECONDS = 18;
	const GLITCH_START = 0.5;
	const GLITCH_WINDOW = 2 / DURATION_SECONDS;
	const LINE_COLOR = 'rgba(91, 155, 255, 0.55)';
	const ALERT_COLOR = '#ff4d4d';
	const RANDOM_SEED = 1337;
	const SPEED_OPTIONS = [1, 1, 2];
	const FLAG_LABELS = [
		'PII Data Access Attempt',
		'Unauthorized Access',
		'Unauthorized Agent',
		'Unsafe Query',
		'Restricted Data Source',
		'Expired Access',
		'Unauthenticated Server',
		'Expired Token',
		'Data Exfiltration Attempt',
		'Credentials Exposure',
		'Policy Violation',
		'Prompt Injection',
		'Unauthorized Tool',
		'Restricted Access',
		'Non-Compliant Tool',
		'Tool Poisoning',
		'Revoked Credentials',
		'Expired Policy'
	];

	function createRandom(seed) {
		return function () {
			let value;

			seed |= 0;
			seed = (seed + 0x6D2B79F5) | 0;
			value = Math.imul(seed ^ (seed >>> 15), 1 | seed);
			value = (value + Math.imul(value ^ (value >>> 7), 61 | value)) ^ value;

			return ((value ^ (value >>> 14)) >>> 0) / 4294967296;
		};
	}

	function buildLineData() {
		const random = createRandom(RANDOM_SEED);
		const lines = [];
		let glitchCount = 0;

		for (let index = 0; index < LINE_COUNT; index += 1) {
			const x = random();
			const rawPhase = random();
			const length = 0.14 + random() * 0.32;
			const thickness = 2 + random();
			const opacity = 0.55 + random() * 0.4;

			random();
			const isGlitch = random() < GLITCH_RATE;
			const label = isGlitch ? FLAG_LABELS[glitchCount % FLAG_LABELS.length] : '';

			lines.push({
				x,
				rawPhase,
				phase: rawPhase,
				length,
				thickness,
				opacity,
				speed: isGlitch ? 1 : SPEED_OPTIONS[Math.floor(random() * SPEED_OPTIONS.length)],
				isGlitch,
				glitchIndex: isGlitch ? glitchCount : -1,
				label
			});

			if (isGlitch) {
				glitchCount += 1;
			}
		}

		const totalGlitches = Math.max(1, glitchCount);
		lines.forEach((line) => {
			if (!line.isGlitch) {
				return;
			}

			const hitPoint = (line.glitchIndex + 0.15 + random() * 0.7) / totalGlitches;
			line.phase = ((GLITCH_START - hitPoint) % 1 + 1) % 1;
		});

		return lines;
	}

	function initRain(rain) {
		if (rain.dataset.heroV2RainInitialized === 'true') {
			return;
		}

		const container = rain.querySelector('[data-hero-v2-rain-lines]');
		if (!container) {
			return;
		}

		rain.dataset.heroV2RainInitialized = 'true';
		const lines = buildLineData().map((lineData) => {
			const line = document.createElement('span');
			line.className = 'obot-hero-v2__rain-line';
			container.appendChild(line);

			let label = null;
			if (lineData.isGlitch) {
				label = document.createElement('span');
				label.className = 'obot-hero-v2__rain-label';
				label.textContent = lineData.label;
				container.appendChild(label);
			}

			return { ...lineData, element: line, labelElement: label };
		});

		const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		let width = 0;
		let height = 0;
		let frameId = 0;
		let isVisible = true;
		const startedAt = performance.now();

		function updateBounds() {
			width = rain.clientWidth;
			height = rain.clientHeight;
		}

		function render(timestamp) {
			if (!width || !height) {
				updateBounds();
			}

			const elapsedSeconds = reducedMotion ? 4 : (timestamp - startedAt) / 1000;
			const progress = (elapsedSeconds % DURATION_SECONDS) / DURATION_SECONDS;

			lines.forEach((line) => {
				const cycle = (line.speed * progress + line.phase) % 1;
				const lineHeight = height * line.length;
				const x = line.x * width;
				const y = cycle * (height + lineHeight) - lineHeight;
				let color = LINE_COLOR;
				let opacity = line.opacity;
				let scaleY = 1;
				let visible = true;
				let labelOpacity = 0;

				if (line.isGlitch) {
					if (cycle >= GLITCH_START && cycle < GLITCH_START + GLITCH_WINDOW) {
						const glitchProgress = (cycle - GLITCH_START) / GLITCH_WINDOW;
						color = ALERT_COLOR;

						if (glitchProgress < 0.35) {
							opacity = 0.5 + 0.5 * Math.abs(Math.sin(glitchProgress * 18));
							labelOpacity = Math.min(1, glitchProgress / 0.15);
						} else {
							const fadeProgress = (glitchProgress - 0.35) / 0.65;
							opacity = Math.max(0, 1 - fadeProgress) * (line.opacity + 0.3);
							scaleY = 1 - fadeProgress * 0.5;
							labelOpacity = Math.max(0, 1 - fadeProgress * 1.4);
						}
					} else if (cycle >= GLITCH_START + GLITCH_WINDOW) {
						visible = false;
					}
				}

				line.element.hidden = !visible;
				if (visible) {
					line.element.style.width = `${line.thickness}px`;
					line.element.style.height = `${lineHeight}px`;
					line.element.style.background = `linear-gradient(to bottom, transparent, ${color})`;
					line.element.style.opacity = String(opacity);
					line.element.style.transform = `translate3d(${x}px, ${y}px, 0) scaleY(${scaleY})`;
				}

				if (line.labelElement) {
					const estimatedLabelWidth = line.label.length * 6.4;
					const labelX = x + line.thickness + 8 + estimatedLabelWidth + 16 > width
						? x - estimatedLabelWidth - 16
						: x + line.thickness + 8;

					line.labelElement.style.opacity = String(labelOpacity);
					line.labelElement.style.transform = `translate3d(${labelX}px, ${y}px, 0)`;
				}
			});

			if (!reducedMotion && isVisible) {
				frameId = requestAnimationFrame(render);
			}
		}

		if ('ResizeObserver' in window) {
			const resizeObserver = new ResizeObserver(updateBounds);
			resizeObserver.observe(rain);
		} else {
			window.addEventListener('resize', updateBounds);
		}

		if ('IntersectionObserver' in window) {
			const intersectionObserver = new IntersectionObserver((entries) => {
				isVisible = entries[0].isIntersecting;

				if (isVisible && !frameId && !reducedMotion) {
					frameId = requestAnimationFrame(render);
				} else if (!isVisible && frameId) {
					cancelAnimationFrame(frameId);
					frameId = 0;
				}
			});
			intersectionObserver.observe(rain);
		}

		updateBounds();
		frameId = requestAnimationFrame(render);
	}

	function initAll(root) {
		const scope = root && root.querySelectorAll ? root : document;
		scope.querySelectorAll(SELECTOR).forEach(initRain);
	}

	initAll(document);

	const observer = new MutationObserver((mutations) => {
		mutations.forEach((mutation) => {
			mutation.addedNodes.forEach((node) => {
				if (node.nodeType !== Node.ELEMENT_NODE) {
					return;
				}

				if (node.matches && node.matches(SELECTOR)) {
					initRain(node);
				} else {
					initAll(node);
				}
			});
		});
	});

	observer.observe(document.documentElement, { childList: true, subtree: true });
}());
