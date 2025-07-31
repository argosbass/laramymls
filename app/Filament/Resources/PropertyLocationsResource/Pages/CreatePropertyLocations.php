<?php

namespace App\Filament\Resources\PropertyLocationsResource\Pages;

use App\Filament\Resources\PropertyLocationsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePropertyLocations extends CreateRecord
{
    protected static string $resource = PropertyLocationsResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Save'),
            $this->getCreateAnotherFormAction()
                ->label('Save and add New'),
            $this->getCancelFormAction(),
        ];
    }
}
