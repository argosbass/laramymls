<?php

namespace App\Filament\Resources;

use App\Models\PropertyType;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\PropertyTypesResource\Pages;

class PropertyTypesResource extends Resource
{
    protected static ?string $model = PropertyType::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $label = 'Property Type';
    protected static ?string $pluralLabel = 'Property Types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type_name')
                    ->label('Nombre del tipo')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type_name')->label('Tipo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Creado')->dateTime(),
            ])
            ->actions([
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
            'index' => Pages\ListPropertyTypes::route('/'),
            'create' => Pages\CreatePropertyTypes::route('/create'),
            'edit' => Pages\EditPropertyTypes::route('/{record}/edit'),
        ];
    }
}
