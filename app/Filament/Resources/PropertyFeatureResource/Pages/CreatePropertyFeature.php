<?php

namespace App\Filament\Resources\PropertyFeatureResource\Pages;

use App\Filament\Resources\PropertyFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePropertyFeature extends CreateRecord
{
    protected static string $resource = PropertyFeatureResource::class;

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
