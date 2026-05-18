(function () {
    function initVideo(button) {
        if (!button || button.dataset.landingVideoInitialized === 'true') {
            return;
        }

        var embedSrc = button.getAttribute('data-landing-video-src');
        if (!embedSrc) {
            return;
        }

        button.dataset.landingVideoInitialized = 'true';

        button.addEventListener('click', function () {
            var iframe = document.createElement('iframe');
            iframe.className = 'obot-landing-video__iframe';
            iframe.src = embedSrc;
            iframe.title = 'YouTube video';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;

            button.replaceWith(iframe);
        });
    }

    function initAllVideos() {
        document.querySelectorAll('[data-landing-video-play]').forEach(initVideo);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllVideos);
    } else {
        initAllVideos();
    }
}());
