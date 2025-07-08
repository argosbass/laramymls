<?php

namespace App\Filament\Resources\RealEstateCompanyResource\Pages;

use App\Filament\Resources\RealEstateCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRealEstateCompanies extends ListRecords
{
    protected static string $resource = RealEstateCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
