@php
    $gmap_key   = env('GMAP_KEY');
@endphp

<style>
    /* Opcional: evita que Filament/Tailwind afecte el mapa */
    #map { width: 100%; height: 400px; }
</style>

<div
    x-data="googleMap()"
    x-init="init()"
    wire:ignore
    id="leaflet-wrapper"
    style="width: 100%; height: 400px; margin-top: 1rem; border: 1px solid #ddd;"
>
    <div id="map"></div>
</div>

<!-- Google Maps JS API (pon tu key) -->
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ $gmap_key }}&v=weekly"
    async
    defer
></script>

<script>
    function googleMap() {
        return {
            map: null,
            marker: null,

            init() {
                const latInput = document.getElementById('latitude-input');
                const lngInput = document.getElementById('longitude-input');

                const initialLat = parseFloat(latInput?.value) || 10.29913;
                const initialLng = parseFloat(lngInput?.value) || -85.84107;

                const mapEl = document.getElementById('map');
                if (!mapEl) return;

                // Espera a que cargue la API (por async/defer)
                const waitForGoogle = () => {
                    if (window.google && google?.maps) {
                        this.mountMap(initialLat, initialLng);
                    } else {
                        setTimeout(waitForGoogle, 50);
                    }
                };
                waitForGoogle();
            },

            mountMap(initialLat, initialLng) {
                const center = { lat: initialLat, lng: initialLng };

                this.map = new google.maps.Map(document.getElementById('map'), {
                    center,
                    zoom: 14,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                });

                this.marker = new google.maps.Marker({
                    position: center,
                    map: this.map,
                    draggable: true,
                });

                // Drag marker -> update inputs
                this.marker.addListener('dragend', (e) => {
                    this.updateInputs(e.latLng.lat(), e.latLng.lng());
                });

                // Click map -> move marker + update inputs
                this.map.addListener('click', (e) => {
                    this.marker.setPosition(e.latLng);
                    this.updateInputs(e.latLng.lat(), e.latLng.lng());
                });

                // Resize observer: cuando el div cambie tamaño (tabs/modales)
                const mapEl = document.getElementById('map');
                const resizeObserver = new ResizeObserver(() => {
                    // Truco estándar: trigger resize + re-centrar
                    const currentCenter = this.map.getCenter();
                    google.maps.event.trigger(this.map, 'resize');
                    if (currentCenter) this.map.setCenter(currentCenter);
                });
                resizeObserver.observe(mapEl);
            },

            updateInputs(lat, lng) {
                const latInput = document.getElementById('latitude-input');
                const lngInput = document.getElementById('longitude-input');

                if (latInput && lngInput) {
                    latInput.value = Number(lat).toFixed(6);
                    lngInput.value = Number(lng).toFixed(6);

                    latInput.dispatchEvent(new Event('input'));
                    lngInput.dispatchEvent(new Event('input'));
                }
            },
        };
    }
</script>
