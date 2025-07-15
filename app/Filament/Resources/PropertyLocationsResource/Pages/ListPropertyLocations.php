<?php

namespace App\Filament\Resources\PropertyLocationsResource\Pages;

use App\Filament\Resources\PropertyLocationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyLocations extends ListRecords
{
    protected static string $resource = PropertyLocationsResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\CreateAction::make('view_location_tree')
                ->label('Location Tree')
                ->icon('heroicon-o-rectangle-group')
                ->url(static::getResource()::getUrl('tree')) // ← usa el nombre definido en getPages()
                ->color('info'),

            Actions\CreateAction::make(),



        ];
    }
}
