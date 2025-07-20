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

    public $companyId;
    public $statusId;
    public $referenceLink;

    public int $page = 1;

    protected $queryString = ['page'];

    public function updated($property)
    {
        $this->resetPage();
    }

    public function updating($name)
    {
        $this->resetPage();
    }

    public function getPageName()
    {
        return 'page';
    }

    public function render()
    {
//        $companies = RealEstateCompany::all();
        $companies = RealEstateCompany::orderBy('company_name')->get();
        $statuses = PropertyStatus::all();

        $results = PropertyListingCompetitor::query()
            ->when($this->companyId, fn($q) => $q->where('real_estate_company_id', $this->companyId))
            ->when($this->referenceLink, fn($q) => $q->where('competitor_property_link', 'like', '%' . $this->referenceLink . '%'))
            ->when($this->statusId, function ($q) {
                $q->whereHas('property', fn($q) =>
                $q->where('property_status_id', $this->statusId)
                );
            })
            ->join('real_estate_companies', 'property_listing_competitors.real_estate_company_id', '=', 'real_estate_companies.id')
            ->orderBy('real_estate_companies.company_name') // Ordena por nombre de compañía
            ->with(['company', 'property.status'])
            ->select('property_listing_competitors.*') // Necesario para evitar conflictos de columnas
            ->paginate(100, pageName: $this->getPageName());




        return view('livewire.listing-competitor-form', compact('results', 'companies', 'statuses'));
    }

    public function search()
    {
        $this->resetPage();
    }
}
