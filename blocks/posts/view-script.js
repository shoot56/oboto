document.addEventListener("DOMContentLoaded", () => {
  console.log("fis");
  new Swiper(".articleSwiper", {
    slidesPerView: 1.3,
    spaceBetween: 16,
    pagination: {
      el: ".article-swiper-pagination",
      clickable: true,
    },
    breakpoints: {
      576: {
        slidesPerView: 2.3,
      },
      1024: {
        slidesPerView: 3.5,
      },
    },
  });
});
