jQuery(document).ready(function ($) {
  let currentCategory = "all";
  let currentPage = 1;

  // Initialize from server-rendered active category (taxonomy pages)
  const $initialActive = $(".learning-center-archive-block .learning-center-archive__category.active").first();
  if ($initialActive.length) {
    const initialSlug = $initialActive.data("category");
    if (initialSlug) {
      currentCategory = initialSlug;
    }
  }

  // Category filter click handler
  $(document).on(
    "click",
    ".learning-center-archive__category",
    function (e) {
      e.preventDefault();

      const $category = $(this);
      const categorySlug = $category.data("category");
      const $block = $category.closest(".learning-center-archive-block");
      const $list = $block.find(".learning-center-archive__list");
      const $loadMoreBtn = $block.find(".learning-center-archive__load-more");

      // Update active state
      $block
        .find(".learning-center-archive__category")
        .removeClass("active");
      $category.addClass("active");

      // Update current category
      currentCategory = categorySlug;
      currentPage = 1;

      // Show loading state
      $list.css("opacity", "0.5");

      // AJAX request to filter posts
      $.ajax({
        url: "/wp-admin/admin-ajax.php",
        type: "POST",
        data: {
          action: "filter_learning_center_archive",
          category: categorySlug,
        },
        success: function (response) {
          if (response.success) {
            $list.html(response.data);
            $list.css("opacity", "1");

            // Update load more button
            const maxPage = $list.find(".max-page").val();
            $loadMoreBtn.data("max-page", maxPage);
            $loadMoreBtn.data("current-page", 1);
            $loadMoreBtn.data("current-category", categorySlug);

            if (maxPage > 1) {
              $loadMoreBtn.show();
            } else {
              $loadMoreBtn.hide();
            }
          }
        },
        error: function () {
          $list.css("opacity", "1");
          console.error("Error filtering posts");
        },
      });
    }
  );

  // Load More button click handler
  $(document).on(
    "click",
    ".learning-center-archive__load-more",
    function (e) {
      e.preventDefault();

      const $btn = $(this);
      const $block = $btn.closest(".learning-center-archive-block");
      const $list = $block.find(".learning-center-archive__list");
      const currentPage = parseInt($btn.data("current-page")) || 1;
      const maxPage = parseInt($btn.data("max-page")) || 1;
      const nextPage = currentPage + 1;
      // Prefer per-button category if present
      const btnCategory = $btn.data("current-category");
      const effectiveCategory = btnCategory ? btnCategory : currentCategory;

      if (nextPage > maxPage) {
        return;
      }

      // Show loading state
      $btn.prop("disabled", true);
      const originalText = $btn.find("span:first").text();
      $btn.find("span:first").text("Loading...");

      // AJAX request to load more posts
      $.ajax({
        url: "/wp-admin/admin-ajax.php",
        type: "POST",
        data: {
          action: "load_more_learning_center_archive",
          category: effectiveCategory,
          paged: nextPage,
        },
        success: function (response) {
          if (response.success) {
            // Remove hidden max-page input if exists
            $list.find(".max-page").remove();

            // Append new posts
            $list.append(response.data);

            // Update button state
            $btn.data("current-page", nextPage);
            $btn.prop("disabled", false);
            $btn.find("span:first").text(originalText);

            // Hide button if no more pages
            if (nextPage >= maxPage) {
              $btn.hide();
            }
          }
        },
        error: function () {
          $btn.prop("disabled", false);
          $btn.find("span:first").text(originalText);
          console.error("Error loading more posts");
        },
      });
    }
  );
});
