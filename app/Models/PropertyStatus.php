<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyStatus extends Model
{
    protected $table = 'property_status';
    protected $fillable = ['status_name'];

    public function properties()
    {
        return $this->hasMany(Property::class, 'property_status_id');
    }
}
