
document.addEventListener("DOMContentLoaded", function () {
  const banner = document.querySelector(".banner");

  if (banner) {
    const closeBanner = banner.querySelector("button");
    closeBanner.addEventListener("click", () => {
      banner.remove();
    });

    setTimeout(() => {
      banner.remove();
    }, 10000);
  }
});