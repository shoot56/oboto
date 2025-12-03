// Function to open the selected tab and display its content
function openTab(event, contentId) {
  // Hide all content elements
  const contents = document.querySelectorAll(".content");
  contents.forEach((content) => {
    content.classList.remove("active");
  });

  // Remove the active class from all tabs
  const tabs = document.querySelectorAll(".tab");
  tabs.forEach((tab) => {
    tab.classList.remove("active");
  });

  // Show the clicked tab's content
  const content = document.getElementById(contentId);
  content.classList.add("active");

  // Add the active class to the clicked tab
  event.currentTarget.classList.add("active");
}

// By default, open the first tab
document.addEventListener("DOMContentLoaded", () => {});
