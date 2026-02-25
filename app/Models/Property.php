<?php
namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Property extends Model implements HasMedia
{
     use InteractsWithMedia;

    protected $fillable = [
        'nid',
        'user_id',
        'slug',
        'published',
        'property_title',
        'property_added_date',
        'property_bathrooms',
        'property_bathrooms_inner',
        'property_bedrooms',
        'property_body',
        'property_building_size_m2',
        'property_building_size_area_quantity',
        'property_building_size_area_unit',
        'property_geolocation_lat',
        'property_geolocation_lng',
        'property_geolocation_lat_sin',
        'property_geolocation_lat_cos',
        'property_geolocation_lng_rad',
        'property_hoa_fee',
        'property_lot_size_area_quantity',
        'property_lot_size_area_unit',
        'property_lot_size_m2',
        'property_no_of_floors',
        'property_notes_to_agents',
        'property_on_floor_no',
        'property_osnid',
        'property_price',
        'property_status_id',
        'property_type_id',
        'property_video',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')->useFallbackUrl('/default.jpg');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->format('webp')
            ->quality(80)
            ->nonQueued(); // opcional
    }

    public function getImagePaths(): array
    {
        return $this->getMedia('gallery')
            ->map(fn ($media) => str_replace('storage/', '', $media->getPathRelativeToRoot()))
            ->toArray();
    }

    protected static function booted()
    {
        static::saving(function ($property) {
            if (empty($property->slug)) {
                $baseSlug = Str::slug($property->property_title);
                $slug = $baseSlug;
                $count = 1;

                while (static::where('slug', $slug)->where('id', '!=', $property->id)->exists()) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $property->slug = $slug;
            }
        });
    }

    public function status() {
        return $this->belongsTo(PropertyStatus::class, 'property_status_id');
    }
    public function type() {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }
    public function location() {
        return $this->belongsTo(PropertyLocations::class, 'property_location_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function features() {
        return $this->belongsToMany(PropertyFeatures::class, 'property_feature_property', 'property_id', 'feature_id');
    }

    public function photos() {
        return $this->hasMany(PropertyPhotos::class);
    }
    public function soldReferences() {
        return $this->hasMany(PropertySoldReferences::class);
    }
    public function listingCompetitors() {
        return $this->hasMany(PropertyListingCompetitor::class);
    }

    public function sold_at()
    {
        // Relación con el sold reference más reciente
        return $this->hasOne(PropertySoldReferences::class)->latestOfMany('sold_reference_date');
    }
}
