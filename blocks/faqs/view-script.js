document.addEventListener("DOMContentLoaded", () => {
  // Get all accordion buttons
  const accordions = document.querySelectorAll(".accordion");

  // Loop through each accordion button and add event listener
  accordions.forEach((acc) => {
    acc.addEventListener("click", function () {
      // Toggle the 'active' class to change appearance
      this.classList.toggle("active");
      // Get the panel associated with the clicked accordion
      const panel = this.nextElementSibling;
      panel.classList.toggle("active");
    });
  });
});
