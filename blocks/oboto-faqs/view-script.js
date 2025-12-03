document.addEventListener("DOMContentLoaded", () => {
  const oboto_faqs = document.querySelectorAll(".oboto-faqs");
  if (oboto_faqs) {
    oboto_faqs.forEach((faqs) => {
      const questions = faqs.querySelectorAll(".oboto-faq__question");
      if (questions) {
        questions.forEach((question, index) => {
          question.addEventListener("click", () => {
            faqs
              .querySelector(".oboto-faq__question.active")
              .classList.remove("active");

            faqs
              .querySelector(".oboto-faq__answer.active")
              .classList.remove("active");

            question.classList.add("active");
            faqs
              .querySelector(`.oboto-faq__answer--${index + 1}`)
              .classList.add("active");
          });
        });
      }
    });
  }
});
