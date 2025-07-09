<?php

namespace App\Filament\Resources;




use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use App\Filament\Resources\PropertyResource\Pages;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $activeNavigationIcon = 'heroicon-s-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')
                    ->schema([
                        Forms\Components\TextInput::make('property_title')
                            ->required()
                            ->maxLength(500),

                        Forms\Components\DatePicker::make('property_added_date'),

                        Forms\Components\TextInput::make('nid')
                            ->hidden(),

                        Forms\Components\Toggle::make('published'),

                        Forms\Components\RichEditor::make('property_body'),

                        Forms\Components\TextInput::make('property_bathrooms')
                            ->numeric(),

                        Forms\Components\TextInput::make('property_bathrooms_inner')
                            ->numeric(),

                        Forms\Components\TextInput::make('property_bedrooms')
                            ->numeric(),
                    ]),

                Forms\Components\Section::make('Building Size')
                    ->schema([
                        Forms\Components\TextInput::make('property_building_size_m2')
                            ->numeric(),

                        Forms\Components\TextInput::make('property_building_size_area_quantity')
                            ->numeric(),

                        Forms\Components\Select::make('property_building_size_area_unit')
                            ->options([
                                'sqm' => 'sqm',
                                'sqft' => 'sqft',
                            ]),
                    ]),

                Forms\Components\Section::make('Geolocation')
                    ->schema([
                        Forms\Components\TextInput::make('property_geolocation_lat')->numeric(),
                        Forms\Components\TextInput::make('property_geolocation_lng')->numeric(),
                        Forms\Components\TextInput::make('property_geolocation_lat_sin')->numeric(),
                        Forms\Components\TextInput::make('property_geolocation_lat_cos')->numeric(),
                        Forms\Components\TextInput::make('property_geolocation_lng_rad')->numeric(),
                    ]),

                Forms\Components\Section::make('Other Details')
                    ->schema([
                        Forms\Components\TextInput::make('property_hoa_fee')->numeric(),
                        Forms\Components\TextInput::make('property_lot_size_area_quantity')->numeric(),
                        Forms\Components\Select::make('property_lot_size_area_unit')
                            ->options([
                                'sqm' => 'sqm',
                                'sqft' => 'sqft',
                            ]),
                        Forms\Components\TextInput::make('property_lot_size_m2')->numeric(),
                        Forms\Components\TextInput::make('property_no_of_floors')->numeric(),
                        Forms\Components\Textarea::make('property_notes_to_agents')->rows(3),
                        Forms\Components\TextInput::make('property_on_floor_no')->numeric(),
                        Forms\Components\TextInput::make('property_osnid')->numeric(),
                        Forms\Components\TextInput::make('property_price')->numeric(),
                        Forms\Components\TextInput::make('property_video')->url(),
                    ]),

                Forms\Components\Section::make('Relations')
                    ->schema([
                        Forms\Components\Select::make('property_status_id')
                            ->label('Status')
                            ->relationship('status', 'status_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('property_type_id')
                            ->label('Type')
                            ->relationship('type', 'type_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('property_location_id')
                            ->label('Location')
                            ->relationship('location', 'location_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\MultiSelect::make('features')
                            ->relationship('features', 'feature_name'),

                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property_title')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('status.status_name')->label('Status')->sortable(),
                Tables\Columns\TextColumn::make('type.type_name')->label('Type')->sortable(),

                Tables\Columns\TextColumn::make('property_price')->label('Price')->money('usd')->sortable(),

                Tables\Columns\TextColumn::make('property_added_date')->date()->sortable(),
                Tables\Columns\BooleanColumn::make('published'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property_status_id')->relationship('status', 'status_name'),
                Tables\Filters\SelectFilter::make('property_type_id')->relationship('type', 'type_name'),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => PropertyResource::getUrl('view', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
        'index' => Pages\ListProperties::route('/'),
        'create' => Pages\CreateProperty::route('/create'),
        'edit' => Pages\EditProperty::route('/{record}/edit'),
        'view' => Pages\ViewProperty::route('/{record}'), // ✅ ESTA ES CLAVE
    ];
    }

    public static function getRelations(): array
    {
        return [
            PropertyResource\RelationManagers\SoldReferencesRelationManager::class,

        ];
    }



}
