document.addEventListener("DOMContentLoaded", () => {
  // In the block editor/admin area, keep content always visible for easier editing.
  if (
    document.body.classList.contains("wp-admin") ||
    document.querySelector(".block-editor")
  ) {
    return;
  }

  const items = document.querySelectorAll(".glossary-item");

  items.forEach((item) => {
    const trigger = item.querySelector(".glossary-item__trigger");
    const panel = item.querySelector(".glossary-item__panel");

    if (!trigger || !panel) return;

    const setOpen = (open) => {
      item.classList.toggle("is-open", open);
      trigger.setAttribute("aria-expanded", open ? "true" : "false");
      panel.setAttribute("aria-hidden", open ? "false" : "true");
    };

    // Ensure closed on load.
    setOpen(item.classList.contains("is-open"));

    trigger.addEventListener("click", () => {
      const isOpen = trigger.getAttribute("aria-expanded") === "true";
      setOpen(!isOpen);
    });
  });
});

