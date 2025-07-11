<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<div 
    x-data="leafletMap()" 
    x-init="init()" 
    wire:ignore
    style="width: 100%; height: 400px; margin-top: 1rem; border: 1px solid #ddd;"
>
    <div id="map" style="width: 100%; height: 100%;"></div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
function leafletMap() {
    return {
        map: null,
        marker: null,

        init() {
            console.log('[Leaflet] init()');

            const latInput = document.getElementById('latitude-input');
            const lngInput = document.getElementById('longitude-input');

            const initialLat = parseFloat(latInput.value) || 9.7489;
            const initialLng = parseFloat(lngInput.value) || -83.7534;

            console.log(`[Leaflet] Initial coordinates: ${initialLat}, ${initialLng}`);

            this.map = L.map('map', {
                zoomAnimation: true,
                fadeAnimation: true
            }).setView([initialLat, initialLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);

            this.marker = L.marker([initialLat, initialLng], { draggable: true, autoPan: true }).addTo(this.map);

            this.marker.on('dragend', () => {
                const position = this.marker.getLatLng();
                console.log(`[Leaflet] Marker dragged to: ${position.lat}, ${position.lng}`);
                this.updateInputs(position.lat, position.lng);
            });

            this.map.on('click', (e) => {
                console.log(`[Leaflet] Map clicked at: ${e.latlng.lat}, ${e.latlng.lng}`);
                this.marker.setLatLng(e.latlng);
                this.updateInputs(e.latlng.lat, e.latlng.lng);
            });

            setTimeout(() => {
                console.log('[Leaflet] invalidating size after init');
                this.map.invalidateSize();
            }, 300);
        },

        updateInputs(lat, lng) {
            const latInput = document.getElementById('latitude-input');
            const lngInput = document.getElementById('longitude-input');

            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);

            latInput.dispatchEvent(new Event('input'));
            lngInput.dispatchEvent(new Event('input'));

            console.log(`[Leaflet] Inputs updated: ${latInput.value}, ${lngInput.value}`);
        }
    }
}
</script>
