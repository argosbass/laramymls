<meta charset="UTF-8">
<title>{{ $property->property_title }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Tailwind CDN for quick styling -->
<script src="https://cdn.tailwindcss.com"></script>

{{-- Incluye estilos de Slick y Magnific Popup --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/magnific-popup.css"/>


<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
    .slick-slide img {
        width: 100%;
        height: auto;
        max-height: 400px;
        object-fit: contain;
    }

    .slider-nav img {
        height: 80px;
        width: auto;
        object-fit: cover;
        border-radius: 0.375rem;
        cursor: pointer;
    }
    .slick-thumb {
        margin-top: 10px;
    }
    .info-label {
        font-weight: 600;
        color: #4B5563;
    }
    .slick-slide
    {
        height: auto;
    }
</style>

<div class="section" style="background-color: #ccc; text-align: center">
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
        <div class="flex justify-center">
            <img src="https://franravi.mymls-cr.com/images/remax-header-pdf.png" style="width: 50%;">
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto p-6 bg-white shadow-md mt-6 rounded-lg grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left Column --}}
    <div class="lg:col-span-2 space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $property->property_title }}</h1>

        @php


            $photos = $property->getMedia('gallery');

        @endphp

        @if ($photos->count())
            <div class="slider mb-4">
                @foreach($photos as $media)
                    <div>
                        <a href="{{ $media->getFullUrl() }}">
                            <img src="{{ $media->getFullUrl() }}"
                                 alt="Foto"
                                 class="rounded-md w-full object-cover max-h-[450px] mx-auto" />
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="slider-nav">
                @foreach($photos as $media)
                    <div class="px-1">
                        <img src="{{ $media->getFullUrl('thumb') ?? $media->getFullUrl() }}"
                             alt="Miniatura"
                             class="h-20 w-28 object-cover rounded border" />
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Description --}}
        <div>
            <h2 class="text-xl font-semibold mb-2">Description</h2>
            <div class="prose max-w-none">{!! $property->property_body !!}</div>
        </div>

        {{-- Features --}}
        @if ($property->features->count())
            <div>
                <h2 class="text-xl font-semibold mb-2">Features</h2>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($property->features as $feature)
                        <li>{{ $feature->feature_name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Right Column --}}
    <div class="space-y-6">

        <h2 class="text-xl font-semibold border-b pb-1">Options</h2>


        {{-- Export to PDF --}}
        <a target="_blank" href="{{ route('property.export', $property) }}"
           class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            <x-heroicon-o-printer class="w-5 h-5" />
            Print to PDF
        </a>

        @if (auth()->check())
            {{-- Edit in Filament --}}
            <a href="{{ route('filament.admin.resources.properties.edit', ['record' => $property->id]) }}"
               class="inline-flex items-center gap-2 bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                <x-heroicon-o-pencil class="w-5 h-5" />
                Edit Property
            </a>

            {{-- Back to Property Search Dashboard --}}
            <a href="{{ route('filament.admin.pages.property-search-dashboard') }}"
               class="inline-flex items-center gap-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <x-heroicon-o-arrow-uturn-left class="w-5 h-5" />
                Back to Properties List
            </a>
        @endif



        @if (auth()->check() && isset($signedUrl))
            <div class="mb-4">
                <button
                    id="showLinkBtn"
                    onclick="toggleLink()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow">
                    Show Temporary Link (15 days)
                </button>

                <div id="linkContainer" class="mt-3 hidden">
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            id="signedLinkInput"
                            value="{{ $signedUrl }}"
                            readonly
                            class="w-full px-3 py-2 border rounded text-sm text-gray-800" />

                        <button
                            onclick="copyToClipboard()"
                            class="bg-gray-300 hover:bg-gray-400 text-sm font-medium px-3 py-2 rounded">
                            Copy
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Anyone with this link can view the property for 15 days.</p>
                </div>
            </div>
        @endif

        {{-- Details --}}
        <div class="space-y-2">
            <h2 class="text-xl font-semibold border-b pb-1">Details</h2>
            <div><span class="info-label">Property ID:</span> {{ $property->id }}</div>
            <div><span class="info-label">Property Type:</span> {{ $property->type?->type_name }}</div>
            <div><span class="info-label">Building Size:</span> {{ $property->property_building_size_area_quantity }} {{ $property->property_building_size_area_unit }}</div>
            <div><span class="info-label">Lot Size:</span> {{ $property->property_lot_size_area_quantity }} {{ $property->property_lot_size_area_unit }}</div>
            <div><span class="info-label">On Floor No.:</span> {{ $property->property_on_floor_no }}</div>
            <div><span class="info-label">No. of Floors:</span> {{ $property->property_no_of_floors }}</div>
            <div><span class="info-label">Price:</span> ${{ number_format($property->property_price, 2) }}</div>
            <div><span class="info-label">Status:</span> {{ $property->status?->status_name }}</div>
            <div><span class="info-label">Location:</span> {{ $property->location?->full_path  }}</div>
        </div>

        {{-- Sold References --}}
        @if ($property->soldReferences->count())
            <div>
                <h2 class="text-xl font-semibold border-b pb-1">Sold References</h2>
                <ul class="space-y-2">
                    @foreach($property->soldReferences as $ref)
                        <li>

                            <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($ref->sold_reference_date)->format('M d, Y') }}</div>
                            <div><strong>Price:</strong> ${{ number_format($ref->sold_reference_price, 2) }}</div>

                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6">
            <button
                onclick="toggleCollapse('extraDetailsCollapse')"
                class="w-full text-left bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded flex justify-between items-center">
                <span class="font-semibold text-gray-800">Agent Access</span>
                <svg id="icon-extraDetailsCollapse" class="w-5 h-5 transition-transform transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div id="extraDetailsCollapse" class="mt-3 hidden">
                <div class="bg-white border rounded p-4 text-sm text-gray-700 space-y-4">

                    {{-- Added Date --}}
                    <div>
                        <strong>Added Date:</strong>
                        {{ $property->created_at ? \Carbon\Carbon::parse($property->created_at)->format('M d, Y') : '—' }}
                    </div>

                    {{-- Added Date --}}
                    <div>
                        <strong>Notes To Agents:</strong>
                        {!! $property->property_notes_to_agents !!}
                    </div>

                    {{-- Listing Competitors --}}
                    <div>
                        <h3 class="text-base font-semibold mb-2">Listing Competitors</h3>

                        @if ($property->listingCompetitors->count())
                            <ul class="space-y-3">
                                @foreach($property->listingCompetitors as $comp)
                                    <li class="border-b pb-2">
                                        @if ($comp->added_date)
                                            <div><strong>Added:</strong> {{ \Carbon\Carbon::parse($comp->added_date)->format('M d, Y') }}</div>
                                        @endif


                                        <div><strong>Company:</strong> {{ $comp->competitor_company_name ?? '—' }}</div>
                                        <div class="flex items-center gap-2">
                                            <strong>Link:</strong>
                                            <a href="{{ $comp->competitor_property_link }}"
                                               class="text-blue-600 underline whitespace-nowrap overflow-hidden text-ellipsis max-w-[300px]"
                                               target="_blank"
                                               title="{{ $comp->competitor_property_link }}">
                                                {{ $comp->competitor_property_link }}
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm italic">No competitor listings available.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>


        {{-- Map --}}
        @if ($property->property_geolocation_lat && $property->property_geolocation_lng)
            <div>
                <h2 class="text-xl font-semibold border-b pb-1">Map</h2>
                <div id="map" class="w-full h-60 rounded-md"></div>
            </div>
        @endif
    </div>
</div>

{{-- Scripts necesarios --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/jquery.magnific-popup.min.js"></script>

<script>
    $(document).ready(function(){
        $('.slider').slick({
            slidesToShow: 1,
            arrows: true,
            fade: true,
            asNavFor: '.slider-nav',
        });

        $('.slider-nav').slick({
            slidesToShow: 4,
            asNavFor: '.slider',
            focusOnSelect: true,
            arrows: false,
            centerMode: true,
        });

        // Popup al hacer clic
        $('.slider').magnificPopup({
            delegate: 'a',
            type: 'image',
            gallery: {
                enabled: true
            },
            mainClass: 'mfp-fade',
            removalDelay: 200,
        });
    });
</script>

{{-- Leaflet Map --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    @if ($property->property_geolocation_lat && $property->property_geolocation_lng)
    var map = L.map('map').setView([{{ $property->property_geolocation_lat }}, {{ $property->property_geolocation_lng }}], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([{{ $property->property_geolocation_lat }}, {{ $property->property_geolocation_lng }}]).addTo(map)
        .bindPopup("{{ $property->property_title }}").openPopup();
    @endif
</script>

<script>
    function toggleLink() {
        const linkContainer = document.getElementById('linkContainer');
        linkContainer.classList.toggle('hidden');
    }

    function copyToClipboard() {
        const input = document.getElementById('signedLinkInput');
        input.select();
        input.setSelectionRange(0, 99999); // para móviles

        try {
            document.execCommand('copy');
            alert('Link copied to clipboard!');
        } catch (err) {
            alert('Failed to copy the link.');
        }
    }
    function toggleCollapse(id) {
        const content = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
</script>
