<?php

namespace App\Filament\Resources\ListingCompetitorResource\Pages;

use App\Filament\Resources\ListingCompetitorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListingCompetitors extends ListRecords
{
    protected static string $resource = ListingCompetitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
