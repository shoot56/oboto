document.addEventListener("DOMContentLoaded", () => {
  const videos_wrap = document.querySelectorAll(".custom-video__wrap");
  if (videos_wrap) {
    videos_wrap.forEach((wrap) => {
      const playBtn = wrap.querySelector(".play-btn");
      const video = wrap.querySelector("video");
      playBtn.addEventListener("click", () => {
        video.play();
        playBtn.classList.add("hide");
        video.setAttribute("controls", "true");
      });

      video.addEventListener("pause", () => {
        console.log("The video is paused.");
        playBtn.classList.remove("hide");
        video.removeAttribute("controls");
      });
    });
  }
});
