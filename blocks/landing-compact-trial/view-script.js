(function () {
  const BLOCK_SELECTOR = '[data-landing-compact-trial][data-mobile-sticky-enabled="true"]';
  const BUTTON_SELECTOR = '[data-landing-compact-trial-button]';
  const STICKY_CLASS = 'obot-landing-compact-trial__mobile-sticky';
  const BODY_CLASS = 'has-landing-compact-trial-sticky';

  function createPopupStickyButton(sourceButton) {
    const buttonText = (sourceButton.getAttribute('data-fillout-button-text') || '').trim();

    if (!buttonText) {
      return null;
    }

    const button = document.createElement('button');
    const label = document.createElement('span');
    const arrow = document.createElement('span');

    button.type = 'button';
    label.textContent = buttonText;
    arrow.className = 'obot-landing-compact-trial__button-arrow';
    arrow.setAttribute('aria-hidden', 'true');
    button.append(label, arrow);

    button.addEventListener('click', () => {
      const filloutButton = sourceButton.querySelector('button');
      if (filloutButton) {
        filloutButton.click();
      }
    });

    return button;
  }

  function initMobileSticky() {
    if (document.querySelector(`.${STICKY_CLASS}`)) {
      return;
    }

    const sourceBlock = Array.from(document.querySelectorAll(BLOCK_SELECTOR))
      .find((block) => block.querySelector(BUTTON_SELECTOR));
    const sourceButton = sourceBlock && sourceBlock.querySelector(BUTTON_SELECTOR);

    if (!sourceBlock || !sourceButton || !document.body) {
      return;
    }

    const sticky = document.createElement('div');
    const isPopup = sourceButton.getAttribute('data-landing-compact-trial-button-type') === 'popup';
    const stickyButton = isPopup
      ? createPopupStickyButton(sourceButton)
      : sourceButton.cloneNode(true);
    let footerVisible = false;
    let frameRequested = false;

    if (!stickyButton) {
      return;
    }

    sticky.className = STICKY_CLASS;
    sticky.setAttribute('aria-hidden', 'true');
    stickyButton.className = `${STICKY_CLASS}-button`;
    stickyButton.removeAttribute('id');
    stickyButton.tabIndex = -1;
    sticky.appendChild(stickyButton);
    document.body.appendChild(sticky);
    document.body.classList.add(BODY_CLASS);

    function updateVisibility() {
      const sourcePassed = sourceBlock.getBoundingClientRect().bottom <= 0;
      const shouldShow = sourcePassed && !footerVisible;

      sticky.classList.toggle('is-visible', shouldShow);
      sticky.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
      stickyButton.tabIndex = shouldShow ? 0 : -1;
      frameRequested = false;
    }

    function requestVisibilityUpdate() {
      if (frameRequested) {
        return;
      }

      frameRequested = true;
      window.requestAnimationFrame(updateVisibility);
    }

    const footer = document.querySelector('footer');
    if (footer && window.IntersectionObserver) {
      const footerObserver = new IntersectionObserver((entries) => {
        footerVisible = entries.some((entry) => entry.isIntersecting);
        requestVisibilityUpdate();
      }, { threshold: 0.05 });

      footerObserver.observe(footer);
    }

    window.addEventListener('scroll', requestVisibilityUpdate, { passive: true });
    window.addEventListener('resize', requestVisibilityUpdate);
    updateVisibility();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileSticky);
  } else {
    initMobileSticky();
  }
})();
