<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyPhotos extends Model
{
    protected $fillable = [
        'property_id',
        'photo_url',
        'photo_alt',
        'photo_title',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
