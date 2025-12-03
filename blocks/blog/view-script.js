document.addEventListener("DOMContentLoaded", function () {
  async function fetchBlogPosts(data, wrapper, action) {
    await fetch("/wp-admin/admin-ajax.php", {
      method: "POST",
      body: data,
    })
      .then((res) => res.text())
      .then((data) => {
        let content = JSON.parse(data);
        if (action == "filter") {
          wrapper.innerHTML = content.data;
        } else {
          wrapper.innerHTML += content.data;
        }
      });
  }

  var page = 2;

  const blog_section = document.querySelectorAll(".blog-block");
  if (blog_section) {
    blog_section.forEach((block) => {
    

      var post_type_slug = block.getAttribute("post-type-slug");
      var taxonomy_slug = block.getAttribute("taxonomy-slug");

      // Filter select
      function updateBlogPosts(categories) {
        const data = new FormData();
        data.append("action", "filter_blog");
        data.append("categories", categories);
        data.append("post_type_slug", post_type_slug);
        data.append("taxonomy_slug", taxonomy_slug);

        fetchBlogPosts(data, block.querySelector(".blog_list"), "filter").then(
          () => {
            load__more.setAttribute(
              "data-max-page",
              block.querySelector(".max-page").value
            );
            if (block.querySelector(".max-page").value < 2) {
              load__more.classList.add("hidden");
            } else {
              load__more.classList.remove("hidden");
            }
          
            page = 2;
          }
        );
      }

      const categories_filters = block.querySelectorAll(".blog_categories li");
      if (categories_filters) {
        categories_filters.forEach((filter) => {
          filter.addEventListener("click", (e) => {
            e.preventDefault();
            filter.classList.toggle("active");

            let active_filters = block.querySelectorAll(
              ".blog_categories li.active"
            );
            let active_filters_slug = [];
            if (active_filters) {
              active_filters.forEach((element) => {
                active_filters_slug.push(element.getAttribute("data-slug"));
              });
            }

            updateBlogPosts(active_filters_slug);
          });
        });
      }

      // Load More
      const load__more = block.querySelector(".load-more");
      if (load__more) {
        if (page > load__more.getAttribute("data-max-page")) {
          load__more.classList.add("hidden");
        } else {
          load__more.classList.remove("hidden");
        }
        load__more.addEventListener("click", (e) => {
          e.preventDefault();

          let active_filters = block.querySelectorAll(
            ".blog_categories li.active"
          );
          let active_filters_slug = [];
          if (active_filters) {
            active_filters.forEach((element) => {
              active_filters_slug.push(element.getAttribute("data-slug"));
            });
          }

          load__more.classList.add("hidden");
          const data = new FormData();
          data.append("action", "load_more_blog");

          data.append("paged", page);
          data.append("post_type_slug", post_type_slug);

          data.append("categories", active_filters_slug);
          data.append("taxonomy_slug", taxonomy_slug);

          fetchBlogPosts(data, block.querySelector(".blog_list"), "load").then(
            () => {
              if (page > load__more.getAttribute("data-max-page")) {
                load__more.classList.add("hidden");
              } else {
                load__more.classList.remove("hidden");
              }
            }
          );
          page = page + 1;
        });
      }
    });
  }
});
