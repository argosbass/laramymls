<?php

namespace App\Filament\Resources\PropertyLocationsResource\Pages;

use App\Filament\Resources\PropertyLocationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyLocations extends EditRecord
{
    protected static string $resource = PropertyLocationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
