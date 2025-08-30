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
use App\Models\PropertyLocations;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Navigation\NavigationItem;
use App\Console\Commands\ImportPropertyPhotosBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $activeNavigationIcon = 'heroicon-s-home-modern';

    protected static ?string $navigationGroup = 'Search Tools';

    public static function getNavigationLabel(): string
    {
        return 'Property Manager';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['Super Admin', 'Data Entry']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Property Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Basic Property Details')->schema([
                            Forms\Components\Section::make()
                                ->schema([
                                    Forms\Components\DatePicker::make('property_added_date')
                                        ->label('Date property added')
                                        ->default(today())
                                        ->displayFormat('dd/mm/yyyy'),

                                    Forms\Components\TextInput::make('property_title')->required()->maxLength(500),

                                    Forms\Components\Select::make('property_type_id')
                                        ->label('Type')
                                        ->relationship('type', 'type_name')
                                        ->searchable()
                                        ->preload()
                                        ->nullable(),

                                    Forms\Components\Select::make('property_status_id')
                                        ->label('Status')
                                        ->relationship('status', 'status_name')
                                        ->searchable()
                                        ->preload()
                                        ->nullable(),

                                    Forms\Components\TextInput::make('property_bedrooms')->numeric(),
                                    Forms\Components\TextInput::make('property_bathrooms_inner')->numeric(),
                                    Forms\Components\TextInput::make('property_bathrooms')->numeric(),
                                    Forms\Components\TextInput::make('property_price')->numeric(),
                                    Forms\Components\TextInput::make('property_hoa_fee')->numeric(),

                                    Forms\Components\TextInput::make('property_building_size_m2')->label('Building Size M2')->suffix('m²')->numeric()->helperText('For inner use. Do not edit this field manually.'),
                                    Forms\Components\TextInput::make('property_building_size_area_quantity')->label('Building Size')->numeric(),
                                    Forms\Components\Select::make('property_building_size_area_unit')->label('Building Size Unit')
                                        ->options([
                                            'sqm' => 'sqm',
                                            'sqft' => 'sqft',
                                        ]),

                                    Forms\Components\TextInput::make('property_lot_size_m2')->label('Lot Size M2')->suffix('m²')->numeric()->helperText('For inner use. Do not edit this field manually.'),
                                    Forms\Components\TextInput::make('property_lot_size_area_quantity')->label('Lot Size')->numeric(),
                                    Forms\Components\Select::make('property_lot_size_area_unit')->label('Lot Size Unit')
                                        ->options([
                                            'sqm' => 'sqm',
                                            'sqft' => 'sqft',
                                        ]),

                                    Forms\Components\TextInput::make('property_no_of_floors')->numeric(),
                                    Forms\Components\TextInput::make('property_on_floor_no')->numeric(),

                                    Forms\Components\RichEditor::make('property_body')->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'strike',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                        'blockquote',
                                        'codeBlock',
                                    ])->columnSpan('full'),

                                    Forms\Components\TextInput::make('property_video')->url(),
                                    Forms\Components\TextInput::make('property_osnid')->numeric(),
                                ])->columns(2),
                        ])->columns(3),

                        Tab::make('Standard Features')->schema([
                            Forms\Components\CheckboxList::make('features')
                                ->relationship('features', 'feature_name'),
                        ])->columns(3),

                        Tab::make('Property Location')->schema([
                            Forms\Components\Select::make('property_location_id')
                                ->label('Location')
                                ->relationship('location', 'location_name')
                                ->options(function () {
                                    return PropertyLocations::query()
                                        ->orderBy('_lft')
                                        ->get()
                                        ->mapWithKeys(function ($location) {
                                            return [
                                                $location->id => str_repeat('- ', $location->depth) . ' ' . $location->location_name,
                                            ];
                                        });
                                })
                                ->searchable()
                                ->preload()
                                ->nullable(),

                            Forms\Components\TextInput::make('property_geolocation_lat')->label('Property Latitude')->numeric()->id('latitude-input'),
                            Forms\Components\TextInput::make('property_geolocation_lng')->label('Property Longitude')->numeric()->id('longitude-input'),
                            Forms\Components\Hidden::make('property_geolocation_lat_sin'),
                            Forms\Components\Hidden::make('property_geolocation_lat_cos'),
                            Forms\Components\Hidden::make('property_geolocation_lng_rad'),
                            Forms\Components\View::make('filament.components.google-map-edit'),
                        ]),

                        Tab::make('Property Photos')->schema([
                            SpatieMediaLibraryFileUpload::make('gallery')
                                ->collection('gallery')
                                ->multiple()
                                ->image()
                                ->responsiveImages()
                                ->reorderable()
                                ->openable()
                                ->downloadable()
                                ->previewable()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                ->maxFiles(20)
                                ->maxSize(10240)
                                ->panelLayout('grid')
                                ->columns([
                                    'sm' => 2,
                                    'lg' => 3,
                                    'xl' => 4,
                                ])
                                ->columnSpanFull()
                                ->disk('public')
                                ->visibility('public')
                                ->directory('property-gallery')
                                ->conversion('thumb')
                                ->reactive()
                                ->afterStateUpdated(fn ($component) => $component->getContainer()->getLivewire()->dispatch('refreshGallery'))
                                ->helperText('Property Photos')
                        ])
                            ->columns(3),

                        Tab::make('Sold References')->schema([
                            Repeater::make('soldReferences')
                                ->label('Sold References List')
                                ->relationship('soldReferences')
                                ->schema([
                                    Forms\Components\hidden::make('nid')
                                        ->default(fn ($state) => $state ?? 0),

                                    Forms\Components\DatePicker::make('sold_reference_date')
                                        ->label('Date')
                                        ->required(),

                                    Forms\Components\TextInput::make('sold_reference_price')
                                        ->label('Price')->numeric(),

                                    Forms\Components\RichEditor::make('sold_reference_notes')
                                        ->label('Notes')->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'strike',
                                            'link',
                                            'bulletList',
                                            'orderedList',
                                            'blockquote',
                                            'codeBlock',
                                        ])->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->itemLabel(fn ($state) => $state['sold_reference_date'] ?? 'New Sold Reference')
                                ->addActionLabel('+ Add Sold Reference')
                                ->collapsible()
                                ->columnSpanFull(),
                        ])->columns(3),

                        Tab::make('Notes to Agent')->schema([
                            Forms\Components\RichEditor::make('property_notes_to_agents')
                                ->label('Notes to Agents')->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'strike',
                                    'link',
                                    'bulletList',
                                    'orderedList',
                                    'blockquote',
                                    'codeBlock',
                                ])->columnSpanFull(),
                        ])->columns(3),

                        Tab::make('Where Listed')->schema([
                            Repeater::make('listingCompetitors')
                                ->label('Listing Competitors List')
                                ->relationship('listingCompetitors')
                                ->schema([
                                    Forms\Components\Hidden::make('nid')
                                        ->default(fn ($state) => $state ?? 0),

                                    Forms\Components\Select::make('real_estate_company_id')
                                        ->label('Company Name')
                                        ->options(function () {
                                            return \App\Models\RealEstateCompany::query()
                                                ->orderBy('company_name')
                                                ->pluck('company_name', 'id');
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->required(),

                                    Forms\Components\TextInput::make('competitor_listing_agent')
                                        ->label('Listing Agent'),

                                    Forms\Components\TextInput::make('competitor_property_link')
                                        ->label('Property Link'),

                                    Forms\Components\TextInput::make('competitor_list_price')
                                        ->label('List Price'),

                                    Forms\Components\RichEditor::make('competitor_notes')
                                        ->label('Notes')
                                        ->toolbarButtons([
                                            'bold', 'italic', 'strike', 'link',
                                            'bulletList', 'orderedList', 'blockquote', 'codeBlock',
                                        ])
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->itemLabel(fn ($state) => $state['competitor_real_estate_company_id'] ?? 'New Listing Competitor')
                                ->addActionLabel('+ Add Listing Competitor')
                                ->collapsible()
                                ->columnSpanFull()
                        ])->columns(3),

                        Tab::make('General')->schema([
                            Forms\Components\TextInput::make('nid')->hidden(),
                            Forms\Components\Toggle::make('published'),

                            Forms\Components\Select::make('user_id')
                                ->label('Author')
                                ->relationship('author', 'name')
                                ->searchable()
                                ->preload(),

                            Forms\Components\TextInput::make('slug')
                                ->label('URL')
                                ->default(fn ($record) => $record ? url('/property-listing/' . $record->slug) : null)
                                ->visible(fn ($record) => filled($record?->slug)),
                        ]),
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
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BooleanColumn::make('published'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property_status_id')
                    ->relationship('status', 'status_name'),
                Tables\Filters\SelectFilter::make('property_type_id')
                    ->relationship('type', 'type_name'),
                Tables\Filters\SelectFilter::make('author.name')
                    ->relationship('author', 'name'),
            ], layout: Tables\Enums\FiltersLayout::AboveContent) // 👈 Esto hace que se vean como inputs arriba
            ->filtersFormColumns(2)
            ->filtersFormWidth('full')
            ->actions([
                Action::make('migratePhotos')
                    ->label('Migrate Photos')
                    ->icon('heroicon-o-photo')
                    ->visible(fn($record) => DB::table('property_photos')
                        ->where('property_id', $record->id)
                        ->whereNull('photo_alt')
                        ->exists()
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        Artisan::call(ImportPropertyPhotosBatch::class, [
                            '--property-id' => $record->id,
                        ]);

                        Notification::make()
                            ->title('Migrate Photos Complete')
                            ->body("Photos of property #{$record->id} was finished.")
                            ->success()
                            ->send();
                    }),

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
            'view' => Pages\ViewProperty::route('/{record}'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            parent::getNavigationItems()[0],
            NavigationItem::make('Add Property')
                ->url(static::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->group('Search Tools')
                ->sort(1),
        ];
    }
}
