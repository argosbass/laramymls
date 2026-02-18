<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\ResponsiveImages\ResponsiveImageGenerator;

class EnsureResponsiveImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $mediaId) {}

    public function handle(): void
    {
        $media = Media::find($this->mediaId);
        if (! $media) return;

        // Si ya hay responsive, no hacer nada
        $responsive = $media->responsive_images ?? [];
        if (! empty($responsive)) return;

        /** @var ResponsiveImageGenerator $generator */
        $generator = app(ResponsiveImageGenerator::class);

        // Genera responsive para el ORIGINAL
        $generator->generateResponsiveImages($media);
    }
}
