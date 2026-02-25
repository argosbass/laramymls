<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EnsureThumbConversion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $mediaId) {}

    public function handle(): void
    {
        $media = Media::find($this->mediaId);
        if (! $media) return;

        // Genera SOLO conversiones (incluye 'thumb')
        $media->generateConversions();
    }
}
