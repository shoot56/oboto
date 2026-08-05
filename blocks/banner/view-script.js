const initializeBanners = () => {
  const banners = document.querySelectorAll(".banner");
  const MILLISECONDS_PER_SECOND = 1000;
  const DEFAULT_VISIBLE_DURATION_SECONDS = 10;

  banners.forEach((banner) => {
    if (banner.dataset.bannerTimersInitialized === "true") {
      return;
    }

    banner.dataset.bannerTimersInitialized = "true";
    const closeBanner = banner.querySelector("button");
    const bannerDelay = Number.parseInt(banner.dataset.bannerDelay, 10);
    const delay = Number.isFinite(bannerDelay) ? Math.max(0, bannerDelay) : 0;
    let revealTimeout;
    let removeTimeout;

    const removeBanner = () => {
      window.clearTimeout(revealTimeout);
      window.clearTimeout(removeTimeout);

      if (banner.parentNode) {
        banner.parentNode.removeChild(banner);
      }
    };

    if (closeBanner && closeBanner.dataset.bannerCloseInitialized !== "true") {
      closeBanner.dataset.bannerCloseInitialized = "true";
      closeBanner.addEventListener("click", removeBanner);

      // Mobile Safari and Firefox can occasionally miss the synthetic click
      // after a touch on a transformed fixed element.
      if ("PointerEvent" in window) {
        closeBanner.addEventListener("pointerup", removeBanner);
      } else {
        closeBanner.addEventListener("touchend", removeBanner, { passive: true });
      }
    }

    if (delay > 0) {
      revealTimeout = window.setTimeout(() => {
        banner.classList.remove("banner--pending");
      }, delay * MILLISECONDS_PER_SECOND);
    }

    removeTimeout = window.setTimeout(
      removeBanner,
      (delay + DEFAULT_VISIBLE_DURATION_SECONDS) * MILLISECONDS_PER_SECOND
    );
  });
};

initializeBanners();
