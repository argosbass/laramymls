<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PropertyFeatures;
use App\Models\PropertyType;
use App\Models\PropertyStatus;
use App\Models\PropertyLocations;
use Livewire\Component;
use Livewire\WithPagination;

class PropertySearchForm extends Component
{
    use WithPagination;

    public $title;
    public $propertyId;
    public $typeId;
    public $statusId;
    public $locationId;
    public $priceFrom;
    public $priceTo;
    public $bedroomsFrom;
    public $bedroomsTo;
    public $bathroomsFrom;
    public $bathroomsTo;
    public $buildingFrom;
    public $buildingTo;
    public $lotFrom;
    public $lotTo;
    public $year;
    public $features = [];

    public int $page = 1;

    public string $sortBy = 'property_price';
    public string $sortDir = 'asc';

    protected $queryString = ['page'];

    public function updated($property)
    {
        $this->resetPage(); // Cuando cambia cualquier filtro, vuelve a la página 1
    }

    public function sortByColumn(string $column): void
    {
        $allowed = [
            'id',
            'created_at',
            'property_title',
            'property_price',
            'property_bedrooms',
            'property_bathrooms',
            'property_building_size_m2',
            'property_lot_size_m2',
            'property_no_of_floors',
        ];

        if (! in_array($column, $allowed, true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }

        $this->resetPage();
    }

    public function render()
    {


        $types = PropertyType::all();
        $statuses = PropertyStatus::all();
        $locations = PropertyLocations::orderBy('_lft')->get();
        $years = Property::selectRaw('YEAR(property_added_date) as year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $featuresList = PropertyFeatures::whereIn('id', [ 18,24,28,33,34,42,50,172,174,173 ])->get();

        /*
            18	Condominium Community
            24	Oceanfront
            28	Elevator
            33	Gated Community
            34	Golf Front
            42	Ocean Views
            50	Swimming Pool
            172	Owner Financing
            174	Guest House
            173	Sold Furnished

         */


        $results = Property::query()
            ->when($this->title, fn($q) => $q->where('property_title', 'like', '%' . $this->title . '%'))
            ->when($this->propertyId, fn($q) => $q->where('id', $this->propertyId))

            ->when(
                $this->typeId &&
                isset($this->typeId['value']) &&
                $this->typeId['value'] !== '' &&
                $this->typeId['value'] !== 'all',
                fn($q) => $q->where('property_type_id', $this->typeId['value'])
            )

            ->when(
                $this->statusId &&
                isset($this->statusId['value']) &&
                $this->statusId['value'] !== '' &&
                $this->statusId['value'] !== 'all',
                fn($q) => $q->where('property_status_id', $this->statusId['value'])
            )



            ->when($this->getLocationIdsToSearch(), function ($q, $ids) {
                $q->whereIn('property_location_id', $ids);
            })
            ->when($this->priceFrom, fn($q) => $q->where('property_price', '>=', $this->priceFrom))
            ->when($this->priceTo, fn($q) => $q->where('property_price', '<=', $this->priceTo))
            ->when($this->bedroomsFrom, fn($q) => $q->where('property_bedrooms', '>=', $this->bedroomsFrom))
            ->when($this->bedroomsTo, fn($q) => $q->where('property_bedrooms', '<=', $this->bedroomsTo))
            ->when($this->bathroomsFrom, fn($q) => $q->where('property_bathrooms', '>=', $this->bathroomsFrom))
            ->when($this->bathroomsTo, fn($q) => $q->where('property_bathrooms', '<=', $this->bathroomsTo))
            ->when($this->buildingFrom, fn($q) => $q->where('property_building_size_m2', '>=', $this->buildingFrom))
            ->when($this->buildingTo, fn($q) => $q->where('property_building_size_m2', '<=', $this->buildingTo))
            ->when($this->lotFrom, fn($q) => $q->where('property_lot_size_m2', '>=', $this->lotFrom))
            ->when($this->lotTo, fn($q) => $q->where('property_lot_size_m2', '<=', $this->lotTo))
            //->when($this->year, fn($q) => $q->whereYear('property_added_date', $this->year))

            ->when(
                $this->year &&
                isset($this->year['value']) &&
                $this->year['value'] !== '' &&
                $this->year['value'] !== 'all',
                fn($q) => $q->whereYear('property_added_date', $this->year['value'])
            )

            ->when(count($this->features), function ($q) {
                foreach ($this->features as $fid) {
                    $q->whereHas('features', fn($q) => $q->where('property_features.id', $fid));
                }
            })
            ->with(['type', 'status', 'location', 'features'])
//            ->paginate(100);

            ->orderBy($this->sortBy, $this->sortDir)

        ->paginate(100, pageName: $this->getPageName());

        return view('livewire.property-search-form', compact(
            'types',
            'statuses',
            'locations',
            'years',
            'featuresList',
            'results'
        ));
    }

    public function resetFilters()
    {
        $this->reset([
            'title', 'propertyId',
            'typeId', 'statusId', 'locationId', 'year',
            'priceFrom', 'priceTo',
            'bedroomsFrom', 'bedroomsTo',
            'bathroomsFrom', 'bathroomsTo',
            'buildingFrom', 'buildingTo',
            'lotFrom', 'lotTo',
            'features',
        ]);

        $this->search(); // para refrescar resultados vacíos
    }

    public function search()
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

    public function getLocationIdsToSearch()
    {

        if (!$this->locationId) {
            return null;
        }

        $location = PropertyLocations::where('id', $this->locationId)->first();

        if (!$location) {
            return null;
        }

        return PropertyLocations::descendantsAndSelf($this->locationId)->pluck('id')->toArray();

    }
}
