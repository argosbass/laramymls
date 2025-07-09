<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\View;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([

            // 🔷 SECCIONES EN 2 COLUMNAS CON ALTURA IGUALADA
            Grid::make(2)
                ->extraAttributes(['class' => 'items-stretch'])
                ->schema([

                Section::make()
                    ->schema([
                        View::make('filament.components.section-header')->viewData([
                            'title' => 'General Information',
                            'icon' => 'information-circle',
                            'color' => 'text-blue-600',
                        ]),
                        Grid::make(2)->schema([
                            TextEntry::make('nid')->label('External ID'),
                            TextEntry::make('property_title')->label('Title'),
                            TextEntry::make('property_price')->label('Price'),
                            TextEntry::make('property_status_id')->label('Status ID'),
                            TextEntry::make('property_type_id')->label('Type ID'),
                            TextEntry::make('published')->label('Published'),
                            TextEntry::make('property_added_date')->label('Added Date'),
                        ]),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'h-full']),

                Section::make()
                    ->schema([
                        View::make('filament.components.section-header')->viewData([
                            'title' => 'Property Features',
                            'icon' => 'home-modern',
                            'color' => 'text-green-600',
                        ]),
                        Grid::make(3)->schema([
                            TextEntry::make('property_bedrooms')->label('Bedrooms'),
                            TextEntry::make('property_bathrooms')->label('Bathrooms'),
                            TextEntry::make('property_bathrooms_inner')->label('Internal Bathrooms'),
                            TextEntry::make('property_no_of_floors')->label('Floors'),
                            TextEntry::make('property_on_floor_no')->label('Floor No.'),
                        ]),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'h-full']),

                Section::make()
                    ->schema([
                        View::make('filament.components.section-header')->viewData([
                            'title' => 'Size and Area',
                            'icon' => 'arrows-expand',
                            'color' => 'text-purple-600',
                        ]),
                        Grid::make(2)->schema([
                            TextEntry::make('property_building_size_m2')->label('Building Area (m²)'),
                            TextEntry::make('property_building_size_area_quantity')->label('Building Size'),
                            TextEntry::make('property_building_size_area_unit')->label('Building Unit'),
                            TextEntry::make('property_lot_size_m2')->label('Lot Area (m²)'),
                            TextEntry::make('property_lot_size_area_quantity')->label('Lot Size'),
                            TextEntry::make('property_lot_size_area_unit')->label('Lot Unit'),
                        ]),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'h-full']),

                Section::make()
                    ->schema([
                        View::make('filament.components.section-header')->viewData([
                            'title' => 'Geographical Location',
                            'icon' => 'map-pin',
                            'color' => 'text-orange-600',
                        ]),
                        Grid::make(2)->schema([
                            TextEntry::make('property_geolocation_lat')->label('Latitude'),
                            TextEntry::make('property_geolocation_lng')->label('Longitude'),
                            TextEntry::make('property_geolocation_lat_sin')->label('Latitude Sin'),
                            TextEntry::make('property_geolocation_lat_cos')->label('Latitude Cos'),
                            TextEntry::make('property_geolocation_lng_rad')->label('Longitude Radians'),
                        ]),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'h-full']),
            ]),

            // 🔷 SECCIONES GRANDES A PANTALLA COMPLETA
            Section::make()
                ->schema([
                    View::make('filament.components.section-header')->viewData([
                        'title' => 'Extras and Notes',
                        'icon' => 'clipboard-document',
                        'color' => 'text-gray-700',
                    ]),
                    Grid::make(1)->schema([
                        TextEntry::make('property_notes_to_agents')->label('Notes to Agents'),
                        TextEntry::make('property_body')->label('Description')->html(),
                        TextEntry::make('property_video')->label('Video (URL)'),
                        TextEntry::make('property_hoa_fee')->label('HOA Fee'),
                        TextEntry::make('property_osnid')->label('OSN ID'),
                    ]),
                ])
                ->columnSpanFull(),

            Section::make()
                ->schema([
                    View::make('filament.components.section-header')->viewData([
                        'title' => 'Property Video',
                        'icon' => 'video-camera',
                        'color' => 'text-red-600',
                    ]),
                    View::make('filament.components.video-embed'),
                ])
                ->columnSpanFull(),

            Section::make()
                ->schema([
                    View::make('filament.components.section-header')->viewData([
                        'title' => 'Location Map',
                        'icon' => 'map',
                        'color' => 'text-blue-600',
                    ]),
                    View::make('filament.components.property-map'),
                ])
                ->columnSpanFull(),

        ]);
}






}

