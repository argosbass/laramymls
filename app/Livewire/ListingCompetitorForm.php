<?php

namespace App\Livewire;

use App\Models\PropertyListingCompetitor;
use App\Models\RealEstateCompany;
use App\Models\PropertyStatus;
use Livewire\Component;
use Livewire\WithPagination;

class ListingCompetitorForm extends Component
{
    use WithPagination;

    public $companyId = '';
    public $statusId = '';
    public $referenceLink = '';

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public function search()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->companyId = '';
        $this->statusId = '';
        $this->referenceLink = '';

        $this->resetPage();
    }

    public function render()
    {
        $companies = RealEstateCompany::orderBy('company_name')->get();
        $statuses = PropertyStatus::orderBy('status_name')->get();

        $results = PropertyListingCompetitor::query()
            ->with([
                'company',
                'property.status',
            ])

            ->when(
                !empty($this->companyId),
                fn ($q) => $q->where(
                    'property_listing_competitors.real_estate_company_id',
                    $this->companyId
                )
            )

            ->when(
                !empty($this->statusId),
                fn ($q) => $q->whereHas('property', function ($q) {
                    $q->where('property_status_id', $this->statusId);
                })
            )

            ->when(
                !empty(trim($this->referenceLink)),
                fn ($q) => $q->where(
                    'property_listing_competitors.competitor_property_link',
                    'like',
                    '%' . trim($this->referenceLink) . '%'
                )
            )

            ->join(
                'real_estate_companies',
                'property_listing_competitors.real_estate_company_id',
                '=',
                'real_estate_companies.id'
            )

            ->orderBy('real_estate_companies.company_name')
            ->select('property_listing_competitors.*')
            ->paginate(100, pageName: 'page');

        return view('livewire.listing-competitor-form', compact(
            'results',
            'companies',
            'statuses'
        ));
    }
}
