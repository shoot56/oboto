window.addEventListener("load", function () {
  const banners = document.querySelectorAll(".banner");
  const MILLISECONDS_PER_SECOND = 1000;
  const DEFAULT_VISIBLE_DURATION_SECONDS = 10;

  banners.forEach((banner) => {
    const closeBanner = banner.querySelector("button");
    const bannerDelay = Number.parseInt(banner.dataset.bannerDelay, 10);
    const delay = Number.isFinite(bannerDelay) ? Math.max(0, bannerDelay) : 0;
    const removeBanner = () => banner.remove();

    if (closeBanner) {
      closeBanner.addEventListener("click", removeBanner);
    }

    if (delay > 0) {
      window.setTimeout(() => {
        banner.classList.remove("banner--pending");
      }, delay * MILLISECONDS_PER_SECOND);
    }

    window.setTimeout(
      removeBanner,
      (delay + DEFAULT_VISIBLE_DURATION_SECONDS) * MILLISECONDS_PER_SECOND
    );
  });
}, { once: true });
