@php
    $lat = $getRecord()->property_geolocation_lat;
    $lng = $getRecord()->property_geolocation_lng;
@endphp

@if($lat && $lng)
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <div id="property-map" 
        style="aspect-ratio: 16/9; width: 100%; z-index: 0;" 
        class="rounded overflow-hidden mt-4 border"
    ></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('property-map').setView([{{ $lat }}, {{ $lng }}], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([{{ $lat }}, {{ $lng }}]).addTo(map);
            
            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        });
    </script>
@else
    <p class="text-sm text-gray-500">Ubicación no disponible.</p>
@endif
