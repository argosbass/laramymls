<?php

namespace App\Filament\Resources\PropertyStatusResource\Pages;

use App\Filament\Resources\PropertyStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePropertyStatus extends CreateRecord
{
    protected static string $resource = PropertyStatusResource::class;

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
