<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyListingCompetitor extends Model {
    protected $fillable = [
        'property_id',
        'nid',
        'competitor_listing_agent',
        'competitor_company_name',
        'competitor_property_link',
        'competitor_list_price',
        'competitor_notes',
        'real_estate_company_id',
    ];

    public function property() {
        return $this->belongsTo(Property::class);
    }

    public function company() {
        return $this->belongsTo(RealEstateCompany::class, 'real_estate_company_id');
    }
}
