@php
    $lat = $getRecord()->property_geolocation_lat;
    $lng = $getRecord()->property_geolocation_lng;
@endphp

@if($lat && $lng)
    <div style="aspect-ratio: 16/9;" class="rounded overflow-hidden mt-4">
        <iframe 
            width="100%" 
            height="100%" 
            frameborder="0" 
            style="border:0"
            src="https://www.google.com/maps?q={{ $lat }},{{ $lng }}&hl=es&z=15&output=embed"
            allowfullscreen>
        </iframe>
    </div>
@else
    <p class="text-sm text-gray-500">Ubicación no disponible.</p>
@endif
