<?php

namespace App\Filament\Resources\PropertyStatusResource\Pages;

use App\Filament\Resources\PropertyStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyStatus extends ListRecords
{
    protected static string $resource = PropertyStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
