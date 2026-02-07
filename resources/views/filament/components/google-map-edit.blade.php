<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<div
    x-data="leafletMap()"
    wire:ignore
    id="leaflet-wrapper"
    style="width: 100%; height: 400px; margin-top: 1rem; border: 1px solid #ddd;"
>
    <div id="map" style="width: 100%; height: 400px;"></div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>

    function leafletMap()
    {

        // Limpia si ya había un mapa inicializado
        if (L.DomUtil.get('map') !== null) {
            L.DomUtil.get('map')._leaflet_id = null;
        }

        const latInput = document.getElementById('latitude-input');
        const lngInput = document.getElementById('longitude-input');
        const initialLat = parseFloat(latInput?.value) || 10.29913;
        const initialLng = parseFloat(lngInput?.value) || -85.84107;

        // initialize Leaflet
        var map = L.map('map').setView([initialLat, initialLng], 14);


        // add the OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        }).addTo(map);

        const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo( map);

        marker.on('dragend', () => {
               const position = marker.getLatLng();
               updateInputs(position.lat, position.lng);
           });

        map.on('click', onMapClick);


        function onMapClick(e)
        {
            marker.setLatLng([e.latlng.lat, e.latlng.lng]);
            updateInputs(e.latlng.lat, e.latlng.lng);
        }

        function updateInputs(lat, lng)
        {
        const latInput = document.getElementById('latitude-input');
        const lngInput = document.getElementById('longitude-input');

        if (latInput && lngInput) {
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);

            latInput.dispatchEvent(new Event('input'));
            lngInput.dispatchEvent(new Event('input'));
        }
    }

        const resizeObserver = new ResizeObserver(entries => {
            // This will be called upon every element resize
            for (let entry of entries) {
                if (entry.target.id === "map") {
                    map.invalidateSize();
                }
            }
        });
        resizeObserver.observe(document.getElementById("map"));


    }

</script>
