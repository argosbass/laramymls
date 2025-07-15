<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PropertyLocationsResource\Pages;
use App\Models\PropertyLocations;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class PropertyLocationsResource extends Resource
{
    protected static ?string $model = PropertyLocations::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $label = 'Location';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('location_name')->required(),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Location')
                    ->relationship('parent', 'location_name')
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location_name')->label('Location Name'),
                Tables\Columns\TextColumn::make('parent.location_name')->label('Parent Location'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyLocations::route('/'),
            'create' => Pages\CreatePropertyLocations::route('/create'),
            'edit' => Pages\EditPropertyLocations::route('/{record}/edit'),
            'tree' => Pages\TreeView::route('/tree'),
        ];
    }
}
