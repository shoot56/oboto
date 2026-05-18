(function () {
    var ROTATION_INTERVAL = 5000;
    var USER_PAUSE_INTERVAL = 10000;

    function initCapabilities(root) {
        if (!root || root.dataset.capabilitiesInitialized === 'true') {
            return;
        }

        var tabs = Array.prototype.slice.call(root.querySelectorAll('[data-capability-tab]'));
        var panels = Array.prototype.slice.call(root.querySelectorAll('[data-capability-panel]'));

        if (!tabs.length || !panels.length) {
            return;
        }

        root.dataset.capabilitiesInitialized = 'true';

        var activeIndex = 0;
        var rotationTimer = null;
        var resumeTimer = null;
        var reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        var canRotate = tabs.length > 1 && !reduceMotionQuery.matches;

        function setActive(nextIndex) {
            if (nextIndex < 0 || nextIndex >= tabs.length) {
                return;
            }

            activeIndex = nextIndex;

            tabs.forEach(function (tab, index) {
                var isActive = index === activeIndex;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            panels.forEach(function (panel, index) {
                var isActive = index === activeIndex;
                panel.classList.toggle('is-active', isActive);
                panel.hidden = !isActive;
            });

            restartProgress();
        }

        function restartProgress() {
            root.classList.remove('is-progressing');

            if (!canRotate || !rotationTimer) {
                return;
            }

            window.requestAnimationFrame(function () {
                root.classList.add('is-progressing');
            });
        }

        function stopRotation() {
            if (rotationTimer) {
                window.clearInterval(rotationTimer);
                rotationTimer = null;
            }

            root.classList.remove('is-progressing');
        }

        function startRotation() {
            if (!canRotate || rotationTimer) {
                return;
            }

            rotationTimer = window.setInterval(function () {
                setActive((activeIndex + 1) % tabs.length);
            }, ROTATION_INTERVAL);

            restartProgress();
        }

        tabs.forEach(function (tab, index) {
            tab.addEventListener('click', function () {
                if (resumeTimer) {
                    window.clearTimeout(resumeTimer);
                }

                stopRotation();
                setActive(index);

                if (canRotate) {
                    resumeTimer = window.setTimeout(startRotation, USER_PAUSE_INTERVAL);
                }
            });
        });

        reduceMotionQuery.addEventListener('change', function (event) {
            canRotate = tabs.length > 1 && !event.matches;

            if (canRotate) {
                startRotation();
            } else {
                stopRotation();
            }
        });

        startRotation();
    }

    function initAllCapabilities() {
        document.querySelectorAll('[data-landing-capabilities]').forEach(initCapabilities);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllCapabilities);
    } else {
        initAllCapabilities();
    }
}());
