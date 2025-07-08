<?php

namespace App\Filament\Resources\RealEstateCompanyResource\Pages;

use App\Filament\Resources\RealEstateCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRealEstateCompany extends EditRecord
{
    protected static string $resource = RealEstateCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
