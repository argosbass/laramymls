<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyLocationsResource\Pages\CreatePropertyLocations;
use App\Filament\Resources\PropertyLocationsResource\Pages\EditPropertyLocations;
use App\Filament\Resources\PropertyLocationsResource\Pages\ListPropertyLocations;
use App\Filament\Resources\PropertyLocationsResource\Pages\TreeView;
use App\Models\PropertyLocations;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class PropertyLocationsResource extends Resource
{
    protected static ?string $model = PropertyLocations::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Property Locations';
    protected static ?string $modelLabel = 'Location';
    protected static ?string $pluralModelLabel = 'Locations';

    /**
     * ✅ Fuerza el orden del nested set (Kalnoy) en TODO el Resource (List, relations, etc.)
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->defaultOrder(); // orden por _lft asc
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Ajusta campos según tu modelo (esto es mínimo)
            \Filament\Forms\Components\TextInput::make('location_name')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Por si acaso (aunque ya está en getEloquentQuery)
            ->defaultSort('_lft', 'asc')
            ->columns([
                TextColumn::make('location_name')
                    ->label('Location')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        // ✅ mostrar jerarquía con indent usando depth (Kalnoy)
                        $depth = (int) ($record->depth ?? 0);
                        return str_repeat('-', $depth) . $state;
                    }),

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('parent_id')
                    ->label('Parent')
                    ->sortable(),

                TextColumn::make('_lft')
                    ->label('LFT')
                    ->sortable(),

                TextColumn::make('_rgt')
                    ->label('RGT')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPropertyLocations::route('/'),
            'create' => CreatePropertyLocations::route('/create'),
            'edit'   => EditPropertyLocations::route('/{record}/edit'),

            // ✅ Tu página del árbol (asegúrate de que la ruta coincida con ->getUrl('tree'))
            'tree'   => TreeView::route('/tree'),
        ];
    }
}
