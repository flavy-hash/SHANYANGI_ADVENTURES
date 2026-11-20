// Itinerary route map on package pages (resources/views/components/route-map.blade.php).
// Leaflet is only downloaded when a map is about to scroll into view.

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const tileLayer = (tiles) => {
    if (tiles.mapbox) {
        return L.tileLayer(`https://api.mapbox.com/styles/v1/${tiles.style}/tiles/512/{z}/{x}/{y}@2x?access_token=${tiles.mapbox}`, {
            tileSize: 512,
            zoomOffset: -1,
            maxZoom: 18,
            attribution: '© <a href="https://www.mapbox.com/about/maps/" target="_blank" rel="noopener">Mapbox</a> © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
        });
    }

    return L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
    });
};

const escapeHtml = (text) => String(text).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

const pin = (stop) => L.divIcon({
    className: 'tm-map-pin',
    html: `<span class="tm-map-pin-label">${escapeHtml(stop.label)}</span><span class="tm-map-pin-dot"></span>`,
    iconSize: null,
    iconAnchor: [0, 0],
});

export const renderRouteMap = (element) => {
    const stops = JSON.parse(element.dataset.stops || '[]');
    const tiles = JSON.parse(element.dataset.tiles || '{}');
    if (!stops.length) return;

    const touch = window.matchMedia('(pointer: coarse)').matches;
    const map = L.map(element, {
        scrollWheelZoom: false, // the page keeps scrolling; zoom with the buttons or a double click
        dragging: !touch, // on phones one finger scrolls the page, as people expect
        tap: false,
        zoomSnap: 0.25,
    });

    tileLayer(tiles).addTo(map);

    const points = stops.map((stop) => [stop.lat, stop.lng]);

    // Route line: a light casing under the dark line keeps it readable on any background.
    L.polyline(points, { color: '#ffffff', weight: 6, opacity: 0.7 }).addTo(map);
    L.polyline(points, { color: '#23331f', weight: 2.5, opacity: 0.9 }).addTo(map);

    const markers = stops.map((stop) => L.marker([stop.lat, stop.lng], { icon: pin(stop), title: `${stop.label}: ${stop.name}`, keyboard: true, riseOnHover: true })
        .bindTooltip(escapeHtml(stop.name), { direction: 'bottom', offset: [0, 14], className: 'tm-map-tooltip' })
        .addTo(map));

    // Stops close together (e.g. Kilimanjaro camps): flip a label below its pin so they don't overlap.
    const separateLabels = () => {
        const placed = [];
        markers.forEach((marker) => {
            const point = map.latLngToContainerPoint(marker.getLatLng());
            const clash = placed.some((other) => Math.abs(other.x - point.x) < 70 && Math.abs(other.y - point.y) < 34);
            marker.getElement()?.classList.toggle('is-below', clash);
            if (!clash) placed.push(point);
        });
    };

    if (points.length === 1) map.setView(points[0], 9);
    else map.fitBounds(L.latLngBounds(points), { padding: [70, 70], maxZoom: 13 });

    separateLabels();
    map.on('zoomend', separateLabels);

    // Re-measure once fonts/layout settle (the map may initialise before its box is final).
    window.setTimeout(() => map.invalidateSize(), 300);
};
