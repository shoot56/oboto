const BANNER_SELECTOR = ".banner";
const CLOSE_BUTTON_SELECTOR = ".banner button";
const MILLISECONDS_PER_SECOND = 1000;
const DEFAULT_VISIBLE_DURATION_SECONDS = 10;
const bannerTimeouts = new WeakMap();

const removeBanner = (banner) => {
  const timeouts = bannerTimeouts.get(banner);

  if (timeouts) {
    window.clearTimeout(timeouts.revealTimeout);
    window.clearTimeout(timeouts.removeTimeout);
    bannerTimeouts.delete(banner);
  }

  if (banner.parentNode) {
    banner.parentNode.removeChild(banner);
  }
};

const closeBannerFromEvent = (event) => {
  const eventTarget = event.target;

  if (!eventTarget || typeof eventTarget.closest !== "function") {
    return;
  }

  const closeButton = eventTarget.closest(CLOSE_BUTTON_SELECTOR);

  if (closeButton) {
    removeBanner(closeButton.closest(BANNER_SELECTOR));
  }
};

// Register delegated handlers before the banner markup is parsed so a user can
// dismiss it immediately, even while the rest of the page is still loading.
document.addEventListener("click", closeBannerFromEvent);

if ("PointerEvent" in window) {
  document.addEventListener("pointerup", closeBannerFromEvent);
} else {
  document.addEventListener("touchend", closeBannerFromEvent, { passive: true });
}

const initializeBannerTimers = () => {
  document.querySelectorAll(BANNER_SELECTOR).forEach((banner) => {
    if (banner.dataset.bannerTimersInitialized === "true") {
      return;
    }

    banner.dataset.bannerTimersInitialized = "true";
    const bannerDelay = Number.parseInt(banner.dataset.bannerDelay, 10);
    const delay = Number.isFinite(bannerDelay) ? Math.max(0, bannerDelay) : 0;
    let revealTimeout;

    if (delay > 0) {
      revealTimeout = window.setTimeout(() => {
        banner.classList.remove("banner--pending");
      }, delay * MILLISECONDS_PER_SECOND);
    }

    const removeTimeout = window.setTimeout(
      () => removeBanner(banner),
      (delay + DEFAULT_VISIBLE_DURATION_SECONDS) * MILLISECONDS_PER_SECOND
    );

    bannerTimeouts.set(banner, { revealTimeout, removeTimeout });
  });
};

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initializeBannerTimers, {
    once: true,
  });
} else {
  initializeBannerTimers();
}
