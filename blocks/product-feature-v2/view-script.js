(function () {
	'use strict';

	const messageType = 'obot-product-feature-v2-html-height';
	const frameSelector = 'iframe[data-product-feature-v2-html-auto]';
	const minimumHeight = 180;
	const maximumHeight = 2400;

	window.addEventListener('message', function (event) {
		const data = event.data;

		if (!data || data.type !== messageType) {
			return;
		}

		const requestedHeight = Number(data.height);
		if (!Number.isFinite(requestedHeight) || requestedHeight <= 0) {
			return;
		}

		document.querySelectorAll(frameSelector).forEach(function (frame) {
			if (frame.contentWindow !== event.source) {
				return;
			}

			const height = Math.min(
				Math.max(Math.ceil(requestedHeight), minimumHeight),
				maximumHeight
			);

			if (Math.abs(frame.getBoundingClientRect().height - height) < 1) {
				return;
			}

			frame.style.height = height + 'px';
		});
	});
})();
