<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'nid',
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

    public function status() {
        return $this->belongsTo(PropertyStatus::class, 'property_status_id');
    }
    public function type() {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }
    public function location() {
        return $this->belongsTo(PropertyLocations::class, 'property_location_id');
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
}
