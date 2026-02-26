<?php

namespace App\Filament\Resources;

use App\Models\Property;
use App\Models\PropertyFeatures;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;

use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Tables\Actions\Action as TableAction;

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

use Filament\Forms\Components\TextInput;




use Filament\Forms\Get;
use Filament\Forms\Set;

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
                    ->columnSpanFull() // Ocupa todo el ancho del formulario
                    ->tabs([

                        Tab::make('Basic Property Details')->schema([
                            Forms\Components\Section::make()
                                ->schema([

                                    Forms\Components\DatePicker::make('property_added_date'),
                                    Forms\Components\TextInput::make('property_title')->required()->maxLength(500),

                                    Forms\Components\Select::make('property_type_id')
                                        ->label('Type')
                                        ->relationship('type', 'type_name')
                                        //->searchable()
                                        //->preload()
                                        ->nullable(),

                                    Forms\Components\Select::make('property_status_id')
                                        ->label('Status')
                                        ->relationship('status', 'status_name')
                                        //->searchable()
                                        //->preload()
                                        ->nullable(),

                                    Forms\Components\TextInput::make('property_bedrooms')->numeric(),
                                    Forms\Components\TextInput::make('property_bathrooms_inner')->hidden(),
                                    Forms\Components\TextInput::make('property_bathrooms')->numeric(),
                                    Forms\Components\TextInput::make('property_price')->numeric(),
                                    Forms\Components\TextInput::make('property_hoa_fee')->numeric(),

                                  //  Forms\Components\TextInput::make('property_building_size_m2')->numeric(),
                                  //  Forms\Components\TextInput::make('property_building_size_area_quantity')->numeric(),
                                  //  Forms\Components\Select::make('property_building_size_area_unit')
                                  //      ->options([
                                  //          'sqm' => 'sqm',
                                  //          'sqft' => 'sqft',
                                  //      ]),


                                    Forms\Components\TextInput::make('property_building_size_area_quantity')
                                        ->numeric()
                                        ->lazy() // importante
                                        ->afterStateUpdated(function ($state, Get $get, Set $set) {

                                            $unit = $get('property_building_size_area_unit');

                                            if (!is_numeric($state)) {
                                                $set('property_building_size_m2', null);
                                                return;
                                            }

                                            if ($unit === 'sqft') {
                                                // convertir sqft a m2
                                                $m2 = $state * 10.7639;
                                            } else {
                                                // ya está en m2
                                                $m2 = $state;
                                            }

                                            $set('property_building_size_m2', round($m2, 4));
                                        }),

                                    Forms\Components\Select::make('property_building_size_area_unit')
                                        ->options([
                                            'sqm' => 'sqm',
                                            'sqft' => 'sqft',
                                        ])
                                        ->lazy()
                                        ->afterStateUpdated(function ($state, Get $get, Set $set) {

                                            $quantity = $get('property_building_size_area_quantity');

                                            if (!is_numeric($quantity)) {
                                                return;
                                            }

                                            if ($state === 'sqft') {
                                                $m2 = $quantity * 10.7639;
                                            } else {
                                                $m2 = $quantity;
                                            }

                                            $set('property_building_size_m2', round($m2, 4));
                                        }),

                                    Forms\Components\TextInput::make('property_building_size_m2')
                                        ->numeric()
                                        ->hidden()
                                        ->afterStateHydrated(function (Get $get, Set $set) {

                                            $quantity = $get('property_building_size_area_quantity');
                                            $unit = $get('property_building_size_area_unit');

                                            if (!is_numeric($quantity)) return;

                                            $m2 = $unit === 'sqft'
                                                ? $quantity * 10.7639
                                                : $quantity;

                                            $set('property_building_size_m2', round($m2, 4));
                                        }),

                                    Forms\Components\TextInput::make('property_lot_size_area_quantity')
                                        ->numeric()
                                        ->lazy()
                                        ->afterStateUpdated(function ($state, Get $get, Set $set) {

                                            $unit = $get('property_lot_size_area_unit');

                                            if (!is_numeric($state)) {
                                                $set('property_lot_size_m2', null);
                                                return;
                                            }

                                            if ($unit === 'sqft') {
                                                $m2 = $state * 10.7639;
                                            } else {
                                                $m2 = $state;
                                            }

                                            $set('property_lot_size_m2', round($m2, 4));
                                        }),

                                    Forms\Components\Select::make('property_lot_size_area_unit')
                                        ->options([
                                            'sqm' => 'sqm',
                                            'sqft' => 'sqft',
                                        ])
                                        ->lazy()
                                        ->afterStateUpdated(function ($state, Get $get, Set $set) {

                                            $quantity = $get('property_lot_size_area_quantity');

                                            if (!is_numeric($quantity)) {
                                                return;
                                            }

                                            if ($state === 'sqft') {
                                                $m2 = $quantity * 10.7639;
                                            } else {
                                                $m2 = $quantity;
                                            }

                                            $set('property_lot_size_m2', round($m2, 4));
                                        }),

                                    Forms\Components\TextInput::make('property_lot_size_m2')
                                        ->numeric()
                                        ->hidden()
                                        ->afterStateHydrated(function (Get $get, Set $set) {

                                            $quantity = $get('property_lot_size_area_quantity');
                                            $unit = $get('property_lot_size_area_unit');

                                            if (!is_numeric($quantity)) return;

                                            $m2 = $unit === 'sqft'
                                                ? $quantity * 10.7639
                                                : $quantity;

                                            $set('property_lot_size_m2', round($m2, 4));
                                        }),

                                    Forms\Components\TextInput::make('property_no_of_floors')->numeric(),
                                    Forms\Components\TextInput::make('property_on_floor_no')->numeric(),




                                    Forms\Components\Hidden::make('property_body_html_mode')
                                        ->default(false)
                                        ->dehydrated(false)
                                        ->reactive(),

                                    Forms\Components\Placeholder::make('property_body_source_toggle')
                                        ->key('property-body-source-toggle')
                                        ->label('Property Body')
                                        ->content('') // requerido
                                        ->dehydrated(false)
                                        ->hintAction(
                                            FormAction::make('togglePropertyBodySource')
                                                ->label(fn (Get $get) => $get('property_body_html_mode') ? 'Visual' : 'Source')
                                                ->icon(fn (Get $get) => $get('property_body_html_mode')
                                                    ? 'heroicon-m-pencil'
                                                    : 'heroicon-m-code-bracket'
                                                )
                                                ->color('gray')
                                                ->action(fn (Get $get, Set $set) =>
                                                $set('property_body_html_mode', ! (bool) $get('property_body_html_mode'))
                                                )
                                        )
                                        ->columnSpanFull(),

                                    Forms\Components\RichEditor::make('property_body')
                                        ->label(false) // ya lo pone el Placeholder
                                        ->toolbarButtons([
                                            'bold','italic','strike','link',
                                            'bulletList','orderedList','blockquote','codeBlock',
                                        ])
                                        ->columnSpanFull()
                                        ->hidden(fn (Get $get) => (bool) $get('property_body_html_mode')),

                                    Forms\Components\Textarea::make('property_body')
                                        ->label(false) // ya lo pone el Placeholder
                                        ->rows(12)
                                        ->columnSpanFull()
                                        ->hidden(fn (Get $get) => ! (bool) $get('property_body_html_mode')),

                                  //  Forms\Components\RichEditor::make('property_body')->toolbarButtons([
                                  //      'bold',
                                  //      'italic',
                                  //      'strike',
                                  //      'link',
                                  //      'bulletList',
                                  //      'orderedList',
                                  //      'blockquote',
                                  //      'codeBlock',
                                  //  ])->columnSpan('full'),







                                    Forms\Components\TextInput::make('property_video')->url(),
                                    Forms\Components\Hidden::make('property_osnid'),
                                ])->columns(2),
                        ])->columns(3)->lazy(),

                        Tab::make('Standard Features')->schema([

                            Forms\Components\CheckboxList::make('features')
                                ->relationship(
                                     'features',
                                     'feature_name'
                                )
                                ->options(function () {
                                    return PropertyFeatures::query()
                                        ->orderBy('weight')
                                        ->orderBy('feature_name')
                                        ->get()
                                        ->mapWithKeys(function ($feature) {
                                            return [
                                                $feature->id =>  $feature->feature_name,
                                            ];
                                        });
                                })

                        ])->columns(3)->lazy(),

                        Tab::make('Property Location')->schema([
                           // Forms\Components\Select::make('property_location_id')
                           //     ->label('Location')
                           //     ->relationship('location', 'location_name')
                           //     ->preload()
                           //     ->nullable(),


                            Forms\Components\Select::make('property_location_id')
                                ->label('Location')
                                ->relationship('location', 'location_name')
                                ->options(function () {
                                    return PropertyLocations::query()
                                        ->orderBy('_lft') // si usás NestedSet
                                        ->get()
                                        ->mapWithKeys(function ($location) {
                                            return [
                                                $location->id => str_repeat('- ', $location->depth) . ' ' . $location->location_name,
                                            ];
                                        });
                                })

                                ->preload()
                                ->nullable(),


                            Forms\Components\TextInput::make('property_geolocation_lat')->numeric()->id('latitude-input'),
                            Forms\Components\TextInput::make('property_geolocation_lng')->numeric()->id('longitude-input'),
                            Forms\Components\Hidden::make('property_geolocation_lat_sin'),
                            Forms\Components\Hidden::make('property_geolocation_lat_cos'),
                            Forms\Components\Hidden::make('property_geolocation_lng_rad'),
                            Forms\Components\View::make('filament.components.google-map-edit-GM'),
                        ])->lazy(),

                        Tab::make('Property Photos')->schema([
                           /*
                             Forms\Components\FileUpload::make('temp_images')
                                ->label('Upload Photos')
                                ->disk('public')
                                ->visibility('public')
                                ->multiple()
                                ->reorderable()
                                ->preserveFilenames()
                                ->directory('temp-property-photos')
                                ->previewable()
                                ->openable()
                                ->downloadable()
                                ->columnSpanFull(),
                            */

                            SpatieMediaLibraryFileUpload::make('gallery')
                                ->collection('gallery')
                                ->multiple()
                                ->image()
                                ->reorderable()
                                ->panelLayout('grid')
                                ->imagePreviewHeight('250')
                                ->openable(false)
                                ->previewable(true)
                                ->columnSpanFull()
                                ->maxFiles(75)

                        ])->columns(3)->lazy(),

                        Tab::make('Sold References')->schema([
                            Repeater::make('soldReferences')
                                ->label('Sold References List')
                                ->relationship('soldReferences') // clave para cargar la relación
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
                                ->columns(2) // columnas internas del repeater
                                ->defaultItems(0)
                                ->itemLabel(fn ($state) => $state['sold_reference_date'] ?? 'New Sold Reference')
                                ->addActionLabel('+ Add Sold Reference')
                                ->collapsible()
                                ->columnSpanFull(), // para que ocupe todo el ancho dentro del tab
                        ])->columns(3)->lazy(),

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
                        ])->columns(3)->lazy(),

                        Tab::make('Where Listed')->schema([

                            Repeater::make('listingCompetitors')
                                ->label('Listing Competitors List')
                                ->relationship('listingCompetitors')
                                ->schema([
                                    Forms\Components\Hidden::make('nid')
                                        ->default(fn ($state) => $state ?? 0),

                                   // Forms\Components\Select::make('real_estate_company_id')
                                   //     ->label('Company Name')
                                   //     ->options(function () {
                                   //         return \App\Models\RealEstateCompany::query()
                                   //             ->orderBy('company_name')
                                   //             ->pluck('company_name', 'id');
                                   //     })

                                   //     ->preload()
                                   //     ->required(),

                                    Forms\Components\Select::make('real_estate_company_id')
                                        ->label('Company Name')
                                        ->options(fn () =>
                                        \App\Models\RealEstateCompany::query()
                                            ->orderBy('company_name')
                                            ->pluck('company_name', 'id')
                                        )
                                        ->preload()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {

                                            $company = \App\Models\RealEstateCompany::find($state);

                                            $set('competitor_company_name', $company?->company_name);
                                        })
                                        ->required(),

                                    Forms\Components\TextInput::make('competitor_company_name'),

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
                                ->defaultItems(1)
                                ->itemLabel(fn ($state) => $state['competitor_company_name'] ?? 'New Listing Competitor')
                                ->addActionLabel('+ Add Listing Competitor')
                                ->collapsible()

                                ->collapsed(fn ($state) => ! blank(data_get($state, 'id')))
                                // 👆 colapsa los existentes (edit), deja abierto el nuevo vacío
                                ->reorderable()
                                ->orderColumn('sort_order') // ✅ guarda el orden aquí


                                ->columnSpanFull()



                        ])->columns(3)->lazy(),

                        Tab::make('General')->schema([

                            Forms\Components\TextInput::make('nid')->hidden(),
                            Forms\Components\Toggle::make('published')->default(true),

                            Forms\Components\Select::make('user_id')
                                ->label('Author')
                                ->relationship('author', 'name')
                                ->searchable()
                                ->preload()
                                ->default(fn () => auth()->id()),

                            Forms\Components\TextInput::make('slug')
                                ->label('URL')
                                ->default(fn ($record) => $record ? url('/property-listing/' . $record->slug) : null)


                                ->visible(fn ($record) => filled($record?->slug)),



                        ])->lazy(),




                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('property_price', 'desc') // 👈 orden inicial
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

                Tables\Filters\Filter::make('property_title')
                    ->form([
                        TextInput::make('value')
                            ->label('Property Title'),
                    ])
                    ->query(fn ($query, array $data) =>
                    $query->when($data['value'] ?? null, fn ($q, $value) =>
                    $q->where('property_title', 'like', "%{$value}%")
                    )
                    ),

                Tables\Filters\Filter::make('property_body')
                    ->form([
                        TextInput::make('value')
                            ->label('Property Description'),
                    ])
                    ->query(fn ($query, array $data) =>
                    $query->when($data['value'] ?? null, fn ($q, $value) =>
                    $q->where('property_body', 'like', "%{$value}%")
                    )
                    ),

                Tables\Filters\SelectFilter::make('property_status_id')->label('Property Status')->relationship('status', 'status_name'),
                Tables\Filters\SelectFilter::make('property_type_id')->label('Property Type')->relationship('type', 'type_name'),
                Tables\Filters\SelectFilter::make('author.name')->label('Author')->relationship('author', 'name'),
            ])
            ->actions([
                TableAction::make('migratePhotos')
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

                TableAction::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => url('/property-listing-id/' . $record->id))
                    ->openUrlInNewTab(),

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
            // Este es el ítem de navegación normal del recurso
            parent::getNavigationItems()[0],

            // Este es el ítem personalizado
            NavigationItem::make('Add Property')
                ->url(static::getUrl('create')) // link al formulario de creación
                ->icon('heroicon-o-plus')
                ->group('Search Tools') // mismo grupo
                ->sort(1), // posición dentro del grupo
        ];
    }
}
