<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingCompetitor extends Model
{
    protected $fillable = [
        'property_id',
        'real_estate_company_id',
        'listing_agent',
        'property_link',
        'list_price',
        'notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function realEstateCompany()
    {
        return $this->belongsTo(RealEstateCompany::class);
    }
}
