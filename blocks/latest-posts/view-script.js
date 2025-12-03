document.addEventListener("DOMContentLoaded", () => {
  console.log("fis");
  new Swiper(".articleSwiper", {
    slidesPerView: 1,
    spaceBetween: 4,
    loop: true,
    //  autoplay: {
    //     delay: 2500,
    //     disableOnInteraction: false,
    //   },
    
     breakpoints: {
      576: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      },
    },
   
  });
});
