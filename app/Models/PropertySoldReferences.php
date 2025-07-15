<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PropertySoldReferences extends Model {
    protected $fillable = [
        'property_id',
        'nid',
        'sold_reference_date',
        'sold_reference_price',
        'sold_reference_notes',
    ];

    public function property() {
        return $this->belongsTo(\App\Models\Property::class);
    }
}
