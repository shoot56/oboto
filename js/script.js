// Function to copy text to clipboard
function copyToClipboard(element) {
  const text = element.textContent;
  console.log(text);
  if (navigator.clipboard) {
    navigator.clipboard
      .writeText(text)
      .then(() => {
        console.log("Text successfully copied to clipboard!");
        element.textContent = "Successfully copied to clipboard!";
        setTimeout(() => {
          element.textContent = text;
        }, 1000);
      })
      .catch((err) => {
        console.error("Failed to copy text: ", err);
      });
  }
}

document.addEventListener("DOMContentLoaded", function () {

  // Select all elements with a specific class
  const animatedElement = document.querySelectorAll(".aos-animated");

  // Loop through and set AOS animation for each element
  animatedElement.forEach((element) => {
    // You can set AOS properties dynamically
    element.setAttribute("data-aos", "fade-up");
    element.setAttribute("delay", "1000");
    element.setAttribute("mirror", "true");
    element.setAttribute("once", "false");
    element.setAttribute("easing", "ease-in-out");
  });

  AOS.init({});

  // Smooth scroll for anchore tag
  if (document.querySelectorAll('a[href^="#"]')) {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", function (e) {
        e.preventDefault();
        if (document.querySelector("nav.menu-opened")) {
          document
            .querySelector("nav.menu-opened")
            .classList.remove("menu-opened");
        }
        var target = this.getAttribute("href");

        jQuery("html, body").animate(
          {
            scrollTop: jQuery(target).offset().top,
          },
          1800
        );
      });
    });
  }

  // Copy to clipboard code
  const codes = document.querySelectorAll(
    "code, .footer-get-started .get-started__overview pre"
  );
  if (codes) {
    codes.forEach((code) => {
      code.addEventListener("click", () => {
        copyToClipboard(code);
      });
    });
  }

   // Highlight code
  document.querySelectorAll(" code").forEach((codeElement) => {
    // Detect language using highlight.js
    const detectedLang = hljs.highlightAuto(codeElement.textContent).language;
    // Apply detected language class for PrismJS
     if (detectedLang) {
        codeElement.classList.add("language-" + detectedLang);
      } else {
        codeElement.classList.add("language-markup"); // or whatever default makes sense
      }

    // Apply PrismJS highlighting
    Prism.highlightElement(codeElement);
  });

function removeAnchor(text) {
  return text.replace(/\s*\{#[^}]+\}/, '');
}

  document.querySelectorAll(".single-post main .entry-content .wp-block-heading").forEach((heading) => {
     console.log(heading.textContent)
     heading.textContent = removeAnchor(heading.textContent);
  })

  document.querySelectorAll(".site-header .navigation__responsive-container-menu .main-menu .menu-item .menu-item__link svg").forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation()
      const parentItem = item.closest(".menu-item");
      parentItem.classList.toggle("submenu-opened");
    });
  })
});

// Get the initial position of the header
let lastScrollTop = 0;

window.addEventListener("scroll", function () {
  let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

  if (scrollTop > lastScrollTop) {
    // Scroll down
    document.querySelector("header").classList.add("sticky");
  } else {
    // Scroll up
    if (scrollTop <= 0) {
      // If at the top, remove sticky class
      document.querySelector("header").classList.remove("sticky");
    }
  }

  lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // Avoid negative scroll values
});
