(function () {
    const ROTATOR_SELECTOR = '[data-obot-landing-hero-rotator]';
    const CURRENT_SELECTOR = '[data-obot-landing-hero-current]';
    const TYPE_SPEED_MS = 72;
    const DELETE_SPEED_MS = 34;
    const HOLD_AFTER_TYPE_MS = 1300;
    const HOLD_AFTER_DELETE_MS = 240;

    function shouldReduceMotion() {
        return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function getItems(rotator) {
        try {
            const parsedItems = JSON.parse(rotator.dataset.obotLandingHeroTexts || '[]');
            return Array.isArray(parsedItems)
                ? parsedItems.map((item) => String(item).trim()).filter(Boolean)
                : [];
        } catch (error) {
            return [];
        }
    }

    function setText(element, value) {
        element.textContent = value;
    }

    function initRotator(rotator) {
        if (rotator.dataset.obotLandingHeroInitialized === 'true') {
            return;
        }

        const current = rotator.querySelector(CURRENT_SELECTOR);
        const items = getItems(rotator);

        if (!current || items.length < 2) {
            return;
        }

        rotator.dataset.obotLandingHeroInitialized = 'true';

        if (shouldReduceMotion()) {
            setText(current, items[0]);
            return;
        }

        let itemIndex = 0;
        let characterIndex = Array.from(items[itemIndex]).length;
        let isDeleting = true;

        function tick() {
            const item = items[itemIndex];
            const characters = Array.from(item);

            setText(current, characters.slice(0, characterIndex).join(''));

            if (isDeleting) {
                if (characterIndex > 0) {
                    characterIndex -= 1;
                    window.setTimeout(tick, DELETE_SPEED_MS);
                    return;
                }

                itemIndex = (itemIndex + 1) % items.length;
                isDeleting = false;
                window.setTimeout(tick, HOLD_AFTER_DELETE_MS);
                return;
            }

            if (characterIndex < Array.from(items[itemIndex]).length) {
                characterIndex += 1;
                window.setTimeout(tick, TYPE_SPEED_MS);
                return;
            }

            isDeleting = true;
            window.setTimeout(tick, HOLD_AFTER_TYPE_MS);
        }

        window.setTimeout(tick, HOLD_AFTER_TYPE_MS);
    }

    function initRotators(root) {
        const scope = root && root.querySelectorAll ? root : document;
        const rotators = scope.matches && scope.matches(ROTATOR_SELECTOR)
            ? [scope]
            : Array.from(scope.querySelectorAll(ROTATOR_SELECTOR));

        rotators.forEach(initRotator);
    }

    function observeDynamicBlocks() {
        if (!window.MutationObserver || !document.body) {
            return;
        }

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        initRotators(node);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initRotators(document);
            observeDynamicBlocks();
        });
    } else {
        initRotators(document);
        observeDynamicBlocks();
    }
})();
