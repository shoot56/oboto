document.addEventListener("DOMContentLoaded", function () {
  async function fetchLearningCenterPosts(data, wrapper) {
    await fetch("/wp-admin/admin-ajax.php", {
      method: "POST",
      body: data,
    })
      .then((res) => res.text())
      .then((data) => {
        let content = JSON.parse(data);
         
          wrapper.innerHTML += content.data;
     
      });
  }

  var page = 2;

  const learning_center_section = document.querySelectorAll(".learning-center-block");
  if (learning_center_section) {
    learning_center_section.forEach((block) => {
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

          load__more.classList.add("hidden");
          const data = new FormData();
          data.append("action", "load_more_learning_center");
          data.append("paged", page);

          fetchLearningCenterPosts(data, block.querySelector(".learning-center_list")).then(
            () => {
              if (page > load__more.getAttribute("data-max-page")) {
                load__more.classList.add("hidden");
                 console.log("hidden")
              } else {
                load__more.classList.remove("hidden");
                 console.log("not hidden")
              }
            }
          );
          page = page + 1;
        });
      }
    });
  }
});
