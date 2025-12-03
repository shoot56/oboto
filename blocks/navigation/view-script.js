document.addEventListener("DOMContentLoaded", function () {
  // Code to be executed when the DOM is ready
  const navigations = document.querySelectorAll(".navigation");
  if (navigations) {
    navigations.forEach((navigation) => {
      const openBtn = navigation.querySelector(
        ".navigation__responsive-container-open"
      );

      openBtn.addEventListener("click", () => {
        navigation.classList.add("menu-opened");
        document.querySelector("body").classList.add("menu-active");

        document.querySelector("body").addEventListener("click", (e) => {
          if (
            e.target !=
              navigation.querySelector(".navigation__responsive-container") &&
            !navigation
              .querySelector(".navigation__responsive-container")
              .contains(e.target) &&
            e.target != openBtn &&
            !openBtn.contains(e.target)
          ) {
            navigation.classList.remove("menu-opened");
            document.querySelector("body").classList.remove("menu-active");
          }
        });
      });

      const closeBtn = navigation.querySelector(
        ".navigation__responsive-container-close"
      );
      closeBtn.addEventListener("click", () => {
        navigation.classList.remove("menu-opened");
        document.querySelector("body").classList.remove("menu-active");
      });
    });
  }
});
