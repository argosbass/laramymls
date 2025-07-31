<?php

namespace App\Filament\Resources\PropertyTypesResource\Pages;

use App\Filament\Resources\PropertyTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePropertyTypes extends CreateRecord
{
    protected static string $resource = PropertyTypesResource::class;

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
