<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    protected $table = 'property_types';
    protected $fillable = ['type_name'];

    public function properties()
    {
        return $this->belongsToMany(
            Property::class,
            'property_type_property',
            'property_type_id',
            'property_id'
        )->withTimestamps();
    }
}
