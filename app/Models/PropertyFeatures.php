<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFeatures extends Model
{
    protected $table = 'property_features';
    protected $fillable = ['feature_name'];


    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_feature_property', 'feature_id', 'property_id');
    }

}
