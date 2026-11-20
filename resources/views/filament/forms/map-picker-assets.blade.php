{{-- Script + styles for the itinerary map picker (filament/forms/map-picker.blade.php), loaded with every admin page
     so pickers added later (e.g. "Add day") work too. Registered in AdminPanelProvider. --}}
    <style>
    .sa-map-picker-map { height: 17rem; border-radius: 0.75rem; overflow: hidden; z-index: 0; position: relative; outline: 1px solid rgb(3 7 18 / 0.1); background: #e5e7eb; }
    .dark .sa-map-picker-map { outline-color: rgb(255 255 255 / 0.12); }
    .sa-map-picker-hint { margin-top: 0.4rem; font-size: 0.8rem; color: var(--gray-500); }
    .sa-map-picker-hint strong { color: var(--gray-700); font-weight: 600; }
    .dark .sa-map-picker-hint strong { color: var(--gray-300); }
    .sa-map-picker-other { display: grid; place-items: center; width: 1.5rem !important; height: 1.5rem !important; margin: -0.75rem 0 0 -0.75rem !important; border-radius: 9999px; background: #7b8a72; color: #fff; font: 700 0.72rem/1 system-ui, sans-serif; border: 2px solid #fff; box-shadow: 0 2px 6px rgb(0 0 0 / 0.3); }
    .sa-map-picker-pin { display: grid; place-items: center; width: 2rem !important; height: 2rem !important; margin: -2rem 0 0 -1rem !important; }
    .sa-map-picker-pin span { display: grid; place-items: center; width: 1.9rem; height: 1.9rem; border-radius: 9999px 9999px 9999px 0; transform: rotate(-45deg); background: #23331f; border: 2px solid #fff; box-shadow: 0 4px 10px rgb(0 0 0 / 0.35); }
    .sa-map-picker-pin b { transform: rotate(45deg); color: #fff; font: 700 0.7rem/1 system-ui, sans-serif; }
</style>
<script>
    window.shanyangiMapPicker = (config) => ({
        map: null,
        marker: null,

        async init() {
            await this.loadLeaflet(config.leafletJs, config.leafletCss);
            const L = window.L;
            const views = (window.__shanyangiMapViews ||= {});
            const saved = views[config.base];

            this.map = L.map(this.$refs.map, { scrollWheelZoom: false, zoomSnap: 0.5 });
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>',
            }).addTo(this.map);

            // The rest of the route, for context.
            const routePoints = [];
            config.others.forEach((other) => {
                routePoints.push([other.lat, other.lng]);
                L.marker([other.lat, other.lng], {
                    icon: L.divIcon({ className: 'sa-map-picker-other', html: String(other.day), iconSize: null }),
                    interactive: true,
                    keyboard: false,
                }).bindTooltip(`Day ${other.day}: ${other.name}`).addTo(this.map);
            });

            if (config.point) this.placeMarker([config.point.lat, config.point.lng]);

            // Keep the zoom the admin chose when the form refreshes; otherwise fit what we have.
            if (saved) this.map.setView(config.point ? [config.point.lat, config.point.lng] : saved.center, saved.zoom);
            else if (config.point) this.map.setView([config.point.lat, config.point.lng], routePoints.length ? 8 : 9);
            else if (routePoints.length) this.map.fitBounds(L.latLngBounds(routePoints), { padding: [30, 30], maxZoom: 9 });
            else this.map.setView([-3.6, 36.2], 7); // northern Tanzania

            this.map.on('moveend', () => { views[config.base] = { center: this.map.getCenter(), zoom: this.map.getZoom() }; });
            this.map.on('click', (event) => this.choose(event.latlng));

            // Days start collapsed: redraw (and re-centre) when the map becomes visible or resizes.
            const element = this.$refs.map;
            const focus = config.point ? [config.point.lat, config.point.lng] : null;
            let wasHidden = element.offsetWidth === 0;
            this.observer = new ResizeObserver(() => {
                if (!element.isConnected || !this.map) return;
                if (element.offsetWidth === 0) { wasHidden = true; return; }
                this.map.invalidateSize();
                if (wasHidden) {
                    wasHidden = false;
                    if (focus) this.map.setView(focus, this.map.getZoom());
                    else if (routePoints.length) this.map.fitBounds(L.latLngBounds(routePoints), { padding: [30, 30], maxZoom: 9 });
                }
            });
            this.observer.observe(element);
        },

        // Called by Alpine when the form refreshes and replaces this picker.
        destroy() {
            this.observer?.disconnect();
            this.map?.remove();
            this.map = null;
        },

        placeMarker(latlng) {
            const L = window.L;
            if (this.marker) {
                this.marker.setLatLng(latlng);
                return;
            }
            this.marker = L.marker(latlng, {
                draggable: true,
                icon: L.divIcon({ className: 'sa-map-picker-pin', html: `<span><b>${config.dayNumber ?? ''}</b></span>`, iconSize: null }),
            }).addTo(this.map);
            this.marker.on('dragend', () => this.choose(this.marker.getLatLng()));
        },

        choose(latlng) {
            const lat = Math.round(latlng.lat * 1e5) / 1e5;
            const lng = Math.round(latlng.lng * 1e5) / 1e5;
            this.placeMarker([lat, lng]);
            this.$wire.$set(`${config.base}.map_lat`, String(lat), false);
            this.$wire.$set(`${config.base}.map_lng`, String(lng), false);
            this.$wire.$set(`${config.base}.map_place`, 'custom'); // refreshes the form once
        },

        loadLeaflet(js, css) {
            if (window.L) return Promise.resolve();
            if (!document.querySelector(`link[href="${css}"]`)) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = css;
                document.head.appendChild(link);
            }
            return (window.__shanyangiLeaflet ||= new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = js;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            }));
        },
    });
</script>
