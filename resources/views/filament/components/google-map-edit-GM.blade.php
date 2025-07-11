@php
    $lat        = $getRecord()->property_geolocation_lat;
    $lng        = $getRecord()->property_geolocation_lng;
    $gmap_key   = env('GMAP_KEY');
@endphp

@if($lat && $lng)
    <div id="property-map"
        style="aspect-ratio: 16/9; width: 100%;"
        class="rounded overflow-hidden mt-4 border"
    ></div>

    <script>
        function initPropertyMap() {
            const latLng = { lat: parseFloat("{{ $lat }}"), lng: parseFloat("{{ $lng }}") };

            const map = new google.maps.Map(document.getElementById('property-map'), {
                center: latLng,
                zoom: 15,
            });

            const marker = new google.maps.Marker({
                position: latLng,
                map: map,
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                console.log('[Google Maps] Loading script...');
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key={{ $gmap_key }}&callback=initPropertyMap`;
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            } else {
                initPropertyMap();
            }
        });
    </script>
@else
    <p class="text-sm text-gray-500">Ubicación no disponible.</p>
@endif
