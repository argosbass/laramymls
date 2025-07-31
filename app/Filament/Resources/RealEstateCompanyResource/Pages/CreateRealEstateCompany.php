<?php

namespace App\Filament\Resources\RealEstateCompanyResource\Pages;

use App\Filament\Resources\RealEstateCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRealEstateCompany extends CreateRecord
{
    protected static string $resource = RealEstateCompanyResource::class;

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
