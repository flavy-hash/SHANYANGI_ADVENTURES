// Protected media on the front end:
//  1. Deterrents: no right-click "Save as", dragging, or long-press saving on media.
//  2. Plays encrypted HLS streams (<video data-stream="...">). hls.js is only loaded
//     when a page has such a video, and never for visitors who prefer reduced motion.

const MEDIA = 'img, video, picture, svg image, [data-protected-media]';

document.addEventListener('contextmenu', (event) => {
    if (event.target.closest?.(MEDIA)) event.preventDefault();
});

document.addEventListener('dragstart', (event) => {
    if (event.target.closest?.(MEDIA)) event.preventDefault();
});

const attachStream = async (video) => {
    const src = video.dataset.stream;
    const play = () => video.play()?.catch(() => {});

    const { default: Hls } = await import('hls.js');

    if (Hls.isSupported()) {
        const hls = new Hls({ enableWorker: true, capLevelToPlayerSize: true });
        let retries = 0;
        hls.on(Hls.Events.MANIFEST_PARSED, play);
        hls.on(Hls.Events.ERROR, (_, data) => {
            if (!data.fatal) return;
            // Retry brief network hiccups; otherwise give up and leave the poster showing.
            if (data.type === Hls.ErrorTypes.NETWORK_ERROR && retries++ < 2) hls.startLoad();
            else hls.destroy();
        });
        hls.loadSource(src);
        hls.attachMedia(video);
        return;
    }

    // Safari / iOS without MSE: native HLS handles the AES-128 key itself.
    if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = src;
        video.addEventListener('loadedmetadata', play, { once: true });
    }
};

const videos = document.querySelectorAll('video[data-stream]');
const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

if (videos.length && !reducedMotion) {
    videos.forEach((video) => attachStream(video));
}
