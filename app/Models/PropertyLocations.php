<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Kalnoy\Nestedset\NodeTrait;

class PropertyLocations extends Model
{
    use NodeTrait;

    protected $fillable = [
        'location_name',
        'parent_id',
    ];


    public function parent()
    {
        return $this->belongsTo(PropertyLocations::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PropertyLocations::class, 'parent_id');
    }


    /**
     * Propiedades que usan esta location
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'property_location_id');
    }


    /**
     * Accesor para obtener la ruta completa de la location.
     *
     * Ej: "Costa Rica > San José > Escazú"
     */
    public function getFullPathAttribute(): string
    {
        $names = [];
        $location = $this;

        while ($location) {
            $names[] = $location->location_name;
            $location = $location->parent;
        }

        return implode(' > ', array_reverse($names));
    }

    public function ancestorsAndSelf()
    {
        return $this->ancestors()->addSelf();
    }
}
