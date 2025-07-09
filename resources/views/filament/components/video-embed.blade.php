@php
    use Illuminate\Support\Str;

    $url = $getRecord()->property_video ?? null;
    $embedUrl = null;

    if ($url && Str::contains($url, 'youtube.com')) {
        preg_match('/v=([^\&]+)/', $url, $matches);
        $videoId = $matches[1] ?? null;
        $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
    } elseif ($url && Str::contains($url, 'youtu.be')) {
        $videoId = Str::afterLast($url, '/');
        $embedUrl = "https://www.youtube.com/embed/{$videoId}";
    } elseif ($url && Str::contains($url, 'vimeo.com')) {
        $videoId = Str::afterLast($url, '/');
        $embedUrl = "https://player.vimeo.com/video/{$videoId}";
    }
@endphp

@if($embedUrl)
    <div style="aspect-ratio: 16/9;" class="rounded overflow-hidden">
        <iframe 
            src="{{ $embedUrl }}" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
            class="w-full h-full">
        </iframe>
    </div>
@else
    <p class="text-sm text-gray-500">There is no valid video.</p>
@endif
