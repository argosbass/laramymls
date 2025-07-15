<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<div
    x-data="leafletMap()"
    x-init="init()"
    wire:ignore
    id="leaflet-wrapper"
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
                // Limpia si ya había un mapa inicializado
                if (L.DomUtil.get('map') !== null) {
                    L.DomUtil.get('map')._leaflet_id = null;
                }

                const latInput = document.getElementById('latitude-input');
                const lngInput = document.getElementById('longitude-input');
                const initialLat = parseFloat(latInput?.value) || 9.7489;
                const initialLng = parseFloat(lngInput?.value) || -83.7534;

                this.map = L.map('map').setView([initialLat, initialLng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a>'
                }).addTo(this.map);

                this.marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(this.map);

                this.marker.on('dragend', () => {
                    const position = this.marker.getLatLng();
                    this.updateInputs(position.lat, position.lng);
                });

                this.map.on('click', (e) => {
                    this.marker.setLatLng(e.latlng);
                    this.updateInputs(e.latlng.lat, e.latlng.lng);
                });

                this.deferMapRedraw(); // fuerza render
            },

            updateInputs(lat, lng) {
                const latInput = document.getElementById('latitude-input');
                const lngInput = document.getElementById('longitude-input');

                if (latInput && lngInput) {
                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);

                    latInput.dispatchEvent(new Event('input'));
                    lngInput.dispatchEvent(new Event('input'));
                }
            },

            deferMapRedraw() {
                // pequeño retraso para evitar glitches iniciales
                setTimeout(() => {
                    this.map.invalidateSize();
                }, 300);

                // Observa cambios en el DOM por tabs o modales
                const wrapper = document.getElementById('leaflet-wrapper');
                if (!wrapper) return;

                const observer = new MutationObserver(() => {
                    if (document.getElementById('map')?.offsetParent !== null) {
                        this.map.invalidateSize();
                    }
                });

                observer.observe(wrapper, {
                    attributes: true,
                    childList: true,
                    subtree: true,
                });
            }
        };
    }
</script>
