document.addEventListener("DOMContentLoaded", () => {
    const tabs_blocks = document.querySelectorAll(".tabs__wrapper");
    if(tabs_blocks) {
        tabs_blocks.forEach(block => {
            const titles = block.querySelectorAll(".tab__title");
            if(titles) {
                titles.forEach(title => {
                    title.addEventListener("mouseover", ()=>{
                     
                    
                        if(document.querySelector(".tab__title.active")) {
                            document.querySelector(".tab__title.active").classList.remove("active"); 
                        }
                        title.classList.add("active");

                        const img = title.getAttribute("data-image");
                        block.querySelector(".tabs__image img").setAttribute("src", img)

                    })

                     title.addEventListener("click", ()=>{
                        if(document.querySelector(".tab__title.active")) {
                            document.querySelector(".tab__title.active").classList.remove("active"); 
                        }
                        title.classList.add("active");

                        const img = title.getAttribute("data-image");
                        block.querySelector(".tabs__image img").setAttribute("src", img)

                    })
                });
            }
        });
    }
})
