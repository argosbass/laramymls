<?php

namespace App\Media;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class CacheBustingUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $url = parent::getUrl();

        // cache buster con timestamp del media
        $v = $this->media->updated_at?->timestamp ?? time();

        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $v;
    }
}