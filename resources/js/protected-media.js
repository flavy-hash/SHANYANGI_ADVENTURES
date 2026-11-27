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

// Safari (iPhone, iPad, Mac) plays encrypted HLS by itself, so it never needs hls.js.
const isSafari = /^((?!chrome|chromium|crios|android|edg|fxios).)*safari/i.test(navigator.userAgent);

// hls.js "light" build: everything we use (incl. AES-128 decryption), ~40% smaller.
// On a slow server the download can fail or be cut off: retry once, then give up quietly
// and leave the poster photo showing instead of throwing an error.
const loadHls = async () => {
    for (let attempt = 0; attempt < 2; attempt++) {
        try {
            return (await import('hls.js/light')).default;
        } catch {
            await new Promise((resolve) => setTimeout(resolve, 3000));
        }
    }
    return null;
};

const attachStream = async (video) => {
    const src = video.dataset.stream;
    const play = () => video.play()?.catch(() => {});

    if (isSafari && video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = src;
        video.addEventListener('loadedmetadata', play, { once: true });
        return;
    }

    const Hls = await loadHls();
    if (!Hls) return;

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

    // Other browsers without Media Source support but with native HLS.
    if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = src;
        video.addEventListener('loadedmetadata', play, { once: true });
    }
};

// A plain video file behind a signed URL (<video data-src="...">): used when the server
// couldn't convert an uploaded video into an encrypted stream.
const attachFile = (video) => {
    video.src = video.dataset.src;
    video.addEventListener('loadeddata', () => video.play()?.catch(() => {}), { once: true });
    video.load();
};

const attach = (video) => (video.dataset.stream ? attachStream(video) : attachFile(video));

const videos = document.querySelectorAll('video[data-stream], video[data-src]');
const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

// Data saver or a slow connection (up to 3G): keep the poster photo, skip the video.
const connection = navigator.connection;
const slowConnection = Boolean(connection?.saveData) || ['slow-2g', '2g', '3g'].includes(connection?.effectiveType);

// Start the video only after the page (text, poster, images) has loaded, so it never
// competes with the content people came for.
const whenPageIsReady = (callback) => {
    const idle = () => (window.requestIdleCallback ? window.requestIdleCallback(callback, { timeout: 2000 }) : window.setTimeout(callback, 300));
    if (document.readyState === 'complete') idle();
    else window.addEventListener('load', idle, { once: true });
};

if (videos.length && !reducedMotion && !slowConnection) {
    whenPageIsReady(() => videos.forEach((video) => attach(video)));
}
