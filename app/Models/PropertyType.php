<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    protected $table = 'property_types';
    protected $fillable = ['type_name'];

    public function properties()
    {
        return $this->hasMany(Property::class, 'property_type_id');
    }
}
