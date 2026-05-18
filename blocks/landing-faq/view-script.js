(function () {
    function closeItem(item) {
        var trigger = item.querySelector('[data-landing-faq-trigger]');
        var panel = item.querySelector('[data-landing-faq-panel]');

        item.dataset.open = 'false';

        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        }

        if (panel) {
            panel.setAttribute('aria-hidden', 'true');
            panel.setAttribute('inert', '');
        }
    }

    function openItem(item) {
        var trigger = item.querySelector('[data-landing-faq-trigger]');
        var panel = item.querySelector('[data-landing-faq-panel]');

        item.dataset.open = 'true';

        if (trigger) {
            trigger.setAttribute('aria-expanded', 'true');
        }

        if (panel) {
            panel.setAttribute('aria-hidden', 'false');
            panel.removeAttribute('inert');
        }
    }

    function initList(list) {
        if (!list || list.dataset.landingFaqInitialized === 'true') {
            return;
        }

        list.dataset.landingFaqInitialized = 'true';

        list.querySelectorAll('[data-landing-faq-trigger]').forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var currentItem = trigger.closest('[data-landing-faq-item]');
                var isOpen = currentItem && currentItem.dataset.open === 'true';

                list.querySelectorAll('[data-landing-faq-item]').forEach(closeItem);

                if (currentItem && !isOpen) {
                    openItem(currentItem);
                }
            });
        });
    }

    function initAll() {
        document.querySelectorAll('[data-landing-faq-list]').forEach(initList);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
}());
