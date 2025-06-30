<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstateCompany extends Model {
    protected $fillable = [
        'nid',
        'published',
        'company_title',
        'company_city_town',
        'company_name',
        'company_main_contact',
        'company_main_telephone',
        'company_notes_to_agents',
        'company_post_code',
        'company_province',
        'company_street_address_1',
        'company_street_address_2',
        'company_website_url',
        'company_website_text',
    ];

    public function competitors() {
        return $this->hasMany(PropertyListingCompetitor::class);
    }
}
