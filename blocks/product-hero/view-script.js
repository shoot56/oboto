(function () {
    var SELECTOR = '[data-product-hero-animation]';
    var STAGE_WIDTH = 900;
    var STAGE_HEIGHT = 560;
    var CODE_SCENE_MS = 5000;
    var SCAN_SCENE_MS = 5000;
    var DASHBOARD_TAB_MS = 3500;
    var DASHBOARD_TAB_COUNT = 8;
    var CYCLE_PAUSE_MS = 2000;
    var DASHBOARD_START_MS = CODE_SCENE_MS + SCAN_SCENE_MS;
    var DASHBOARD_DURATION_MS = DASHBOARD_TAB_MS * DASHBOARD_TAB_COUNT;
    var CYCLE_MS = DASHBOARD_START_MS + DASHBOARD_DURATION_MS + CYCLE_PAUSE_MS;

    function setActive(elements, activeIndex) {
        Array.prototype.forEach.call(elements, function (element, index) {
            element.classList.toggle('is-active', index === activeIndex);
        });
    }

    function initAnimation(animation) {
        if (!animation || animation.dataset.productHeroInitialized === 'true') {
            return;
        }

        var viewport = animation.closest('[data-product-hero-animation-viewport]');
        if (!viewport) {
            return;
        }

        animation.dataset.productHeroInitialized = 'true';

        var codeScene = animation.querySelector('[data-product-hero-scene="code"]');
        var dashboardScene = animation.querySelector('[data-product-hero-scene="dashboard"]');
        var tabs = animation.querySelectorAll('[data-product-hero-tab]');
        var panels = animation.querySelectorAll('[data-product-hero-panel]');
        var address = animation.querySelector('[data-product-hero-address]');
        var status = animation.querySelector('[data-product-hero-status]');
        var scanBar = animation.querySelector('[data-product-hero-scan-bar]');
        var scanValue = animation.querySelector('[data-product-hero-scan-value]');
        var dashboardProgress = animation.querySelector('[data-product-hero-dashboard-progress]');
        var reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        var startTime = performance.now();
        var frameId = null;
        var isVisible = true;

        function fitStage() {
            var width = viewport.clientWidth;
            var height = viewport.clientHeight;
            var scale;

            if (!width || !height) {
                return;
            }

            scale = Math.min(width / STAGE_WIDTH, height / STAGE_HEIGHT);
            animation.style.left = ((width - STAGE_WIDTH * scale) / 2) + 'px';
            animation.style.top = ((height - STAGE_HEIGHT * scale) / 2) + 'px';
            animation.style.transform = 'scale(' + scale + ')';
        }

        function renderStaticDashboard() {
            animation.dataset.scene = 'dashboard';
            codeScene.classList.remove('is-active');
            dashboardScene.classList.add('is-active');
            setActive(tabs, 0);
            setActive(panels, 0);
            address.textContent = 'control.acme-corp.obot.ai';
            status.textContent = 'live';
            dashboardProgress.style.width = '100%';
            scanBar.style.width = '100%';
            scanValue.textContent = '100';
        }

        function renderFrame(now) {
            var elapsed;
            var cycleTime;
            var scene;
            var tabIndex = 0;
            var scanPercent = 0;
            var dashboardPercent = 0;
            var tabElapsed;

            if (!document.documentElement.contains(animation)) {
                frameId = null;
                return;
            }

            if (reduceMotionQuery.matches) {
                renderStaticDashboard();
                frameId = null;
                return;
            }

            elapsed = now - startTime;
            cycleTime = ((elapsed % CYCLE_MS) + CYCLE_MS) % CYCLE_MS;

            if (cycleTime < CODE_SCENE_MS) {
                scene = 'code';
            } else if (cycleTime < DASHBOARD_START_MS) {
                scene = 'scan';
                scanPercent = Math.min(Math.floor((cycleTime - CODE_SCENE_MS) / 60) * 2, 100);
            } else {
                scene = 'dashboard';
                tabElapsed = cycleTime - DASHBOARD_START_MS;

                if (tabElapsed >= DASHBOARD_DURATION_MS) {
                    tabIndex = DASHBOARD_TAB_COUNT - 1;
                    dashboardPercent = 100;
                } else {
                    tabIndex = Math.floor(tabElapsed / DASHBOARD_TAB_MS);
                    dashboardPercent = ((tabElapsed % DASHBOARD_TAB_MS) / DASHBOARD_TAB_MS) * 100;
                }
            }

            animation.dataset.scene = scene;
            codeScene.classList.toggle('is-active', scene !== 'dashboard');
            dashboardScene.classList.toggle('is-active', scene === 'dashboard');

            if (scene === 'dashboard') {
                address.textContent = 'control.acme-corp.obot.ai';
                status.textContent = 'live';
                setActive(tabs, tabIndex);
                setActive(panels, tabIndex);
            } else {
                address.textContent = 'claude.ai/code';
                status.textContent = scene === 'scan' ? 'scanning' : 'active';
            }

            scanBar.style.width = scanPercent + '%';
            scanValue.textContent = scanPercent;
            dashboardProgress.style.width = dashboardPercent + '%';

            if (isVisible) {
                frameId = window.requestAnimationFrame(renderFrame);
            } else {
                frameId = null;
            }
        }

        function start() {
            if (frameId === null && isVisible) {
                frameId = window.requestAnimationFrame(renderFrame);
            }
        }

        function handleMotionChange() {
            if (frameId !== null) {
                window.cancelAnimationFrame(frameId);
                frameId = null;
            }

            if (reduceMotionQuery.matches) {
                renderStaticDashboard();
            } else {
                startTime = performance.now();
                start();
            }
        }

        fitStage();

        if (window.ResizeObserver) {
            new ResizeObserver(fitStage).observe(viewport);
        } else {
            window.addEventListener('resize', fitStage);
        }

        if (window.IntersectionObserver) {
            new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    isVisible = entry.isIntersecting;
                    if (isVisible) {
                        start();
                    } else if (frameId !== null) {
                        window.cancelAnimationFrame(frameId);
                        frameId = null;
                    }
                });
            }, { rootMargin: '120px' }).observe(viewport);
        }

        if (reduceMotionQuery.addEventListener) {
            reduceMotionQuery.addEventListener('change', handleMotionChange);
        } else if (reduceMotionQuery.addListener) {
            reduceMotionQuery.addListener(handleMotionChange);
        }

        if (reduceMotionQuery.matches) {
            renderStaticDashboard();
        } else {
            start();
        }
    }

    function initAll(context) {
        var root = context && context.querySelectorAll ? context : document;
        var animations = root.querySelectorAll(SELECTOR);

        Array.prototype.forEach.call(animations, initAnimation);

        if (root.matches && root.matches(SELECTOR)) {
            initAnimation(root);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initAll(document);
        });
    } else {
        initAll(document);
    }

    if (window.MutationObserver) {
        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                Array.prototype.forEach.call(mutation.addedNodes, function (node) {
                    if (node.nodeType === 1) {
                        initAll(node);
                    }
                });
            });
        }).observe(document.documentElement, { childList: true, subtree: true });
    }
}());
