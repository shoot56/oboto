document.addEventListener("DOMContentLoaded", function () {
  gsap.registerPlugin(ScrollTrigger);
  var hero_chats = document.querySelectorAll(".hero-questions");

  hero_chats.forEach((chat) => {
    chat.querySelector(".pinned-element").style.height = `${
      chat.querySelectorAll(".chat__answer__question")[0].clientHeight
    }px`;
    ScrollTrigger.create({
      trigger: chat.querySelector(".overlay"),
      start: () => `top ${document.querySelector("header").offsetHeight + 100}`,
      pin: true,
      pinSpacing: false,
      scrub: true,
    });

    const chat_questions = chat.querySelectorAll(".chat__answer__question");
    chat_questions.forEach((question, index) => {
      const myScrollTrigger = ScrollTrigger.create({
        trigger: question,
        start: () =>
          `top ${
            chat.querySelector(".pinned-element").getBoundingClientRect()
              .bottom - 50
          }`,
        end: () =>
          `bottom ${
            chat.querySelector(".pinned-element").getBoundingClientRect().bottom
          }`,
        // pin: true,
        // pinSpacing: false,
        scrub: true,
        // markers: true,
        onEnter: () => {
          console.log("enter");

          if (!index == 0) {
            chat.querySelector(
              ".pinned-element"
            ).style.height = `${question.clientHeight}px`;
            chat.querySelector(".pinned-element").classList.add("active");
            chat
              .querySelector(".chat__answer__question.active")
              ?.classList.remove("active");
            chat.querySelector(
              ".pinned-element"
            ).innerHTML = `<div>${question.innerHTML}</div>`;
          }

          myScrollTrigger.vars.start = `top ${
            chat.querySelector(".pinned-element").getBoundingClientRect()
              .bottom - 50
          }`;
          myScrollTrigger.refresh(); // Refresh ScrollTrigger to apply changes
        },
        onEnterBack: () => {
          console.log("enter back");
          chat.querySelector(
            ".pinned-element"
          ).style.height = `${question.clientHeight}px`;
          question.classList.remove("active");
          chat.querySelector(
            ".pinned-element"
          ).innerHTML = `<div>${question.innerHTML}</div>`;

          if (index == chat_questions.length - 1) {
            chat.querySelector(".chat__footer").classList.remove("active");
            chat.querySelector(".pinned-element").classList.add("active");
          }

          if (index == 0) {
            question.classList.add("active");
            chat.querySelector(".pinned-element").innerHTML = `<div></div>`;
            chat.querySelector(".pinned-element").classList.remove("active");
          }

          myScrollTrigger.vars.start = `top ${
            chat.querySelector(".pinned-element").getBoundingClientRect()
              .bottom - 50
          }`;
          myScrollTrigger.refresh(); // Refresh ScrollTrigger to apply changes
        },
        onLeave: () => {
          console.log("leav");
          if (index == chat_questions.length - 1) {
            chat.querySelector(".chat__footer").classList.add("active");
            chat.querySelector(".pinned-element").classList.remove("active");
          }
          myScrollTrigger.vars.end = `bottom ${
            chat.querySelector(".pinned-element").getBoundingClientRect().bottom
          }`; // Update end position dynamically
          myScrollTrigger.refresh();
        },
      });
    });
  });
});

window.addEventListener("scroll", function () {
  var hero_chats = document.querySelectorAll(".hero-questions");
  hero_chats.forEach((chat) => {
    var hero_chat_head = chat.querySelector(".chat__head");
    if (hero_chat_head) {
      if (
        hero_chat_head.getBoundingClientRect().top <
        document.querySelector("header").offsetHeight
      ) {
        hero_chat_head.classList.remove("active");
      } else {
        hero_chat_head.classList.add("active");
      }
    }
  });
});
