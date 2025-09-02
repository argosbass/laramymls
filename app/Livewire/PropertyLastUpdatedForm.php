<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyStatus;
use App\Models\PropertyLocations;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyLastUpdatedForm extends Component
{
    use WithPagination;

    public $title;
    public $propertyId;
    public $typeId;
    public $statusId;
    public $locationId;
    public $authorId;

    public int $page = 1;

    protected $queryString = ['page'];

    public function updated($property)
    {
        $this->resetPage(); // Cuando cambia cualquier filtro, vuelve a la página 1
    }

    public function render()
    {
        $authors    = User::all();
        $types      = PropertyType::all();
        $statuses   = PropertyStatus::all();
        $locations  = PropertyLocations::orderBy('_lft')->get();

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

            ->when(
                $this->authorId &&
                isset($this->authorId['value']) &&
                $this->authorId['value'] !== '' &&
                $this->authorId['value'] !== 'all',
                fn($q) => $q->where('user_id', $this->authorId['value'])
            )

            ->when($this->getLocationIdsToSearch(), function ($q, $ids) {
                $q->whereIn('property_location_id', $ids);
            })

            ->with(['type', 'status', 'location', 'author'])

            ->orderBy('updated_at', 'desc') // ordena de la más reciente a la más antigua
            ->paginate(100, pageName: $this->getPageName());

        return view('livewire.property-last-updated-form', compact(
            'types',
            'statuses',
            'locations',
            'authors',
            'results'
        ));
    }

    public function resetFilters()
    {
        $this->reset([
            'title', 'propertyId',
            'typeId', 'statusId', 'locationId', 'authorId',
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
