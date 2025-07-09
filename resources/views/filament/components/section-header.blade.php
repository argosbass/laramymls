@php
    $icons = [
        'information-circle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m0-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />',
        'home-modern' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-7 9 7v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8z" />',
        'arrows-expand' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l6 6m0 0L4 16m6-6h6m-6 0v6m0-6l6-6m0 12l-6-6" />',
        'map-pin' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.05 20.95l5-5L20 6m-1-1a7 7 0 00-9.9 0l-5 5a7 7 0 009.9 9.9l5-5" />',
        'clipboard-document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 2h8a2 2 0 012 2v16a2 2 0 01-2 2H8a2 2 0 01-2-2V4a2 2 0 012-2z" />',
        'video-camera' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14V10z" />',
        'map' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.618V7.382a1 1 0 01.553-.894L9 4v16zM15 4v16l5.447-2.724A1 1 0 0021 16.618V7.382a1 1 0 00-.553-.894L15 4z" />',
    ];
@endphp

<div class="flex items-center gap-2 mb-2 mt-6">
    <svg xmlns="http://www.w3.org/2000/svg" class="{{ $color }} w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        {!! $icons[$icon] ?? '' !!}
    </svg>
    <h2 class="text-lg font-semibold {{ $color }}">{{ $title }}</h2>
</div>
