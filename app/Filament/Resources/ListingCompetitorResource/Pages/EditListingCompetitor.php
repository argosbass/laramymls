<?php

namespace App\Filament\Resources\ListingCompetitorResource\Pages;

use App\Filament\Resources\ListingCompetitorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListingCompetitor extends EditRecord
{
    protected static string $resource = ListingCompetitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
