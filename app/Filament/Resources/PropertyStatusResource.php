<?php
namespace App\Filament\Resources;

use App\Models\PropertyStatus;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\PropertyStatusResource\Pages;

class PropertyStatusResource extends Resource
{
    protected static ?string $model = PropertyStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $label = 'Property Status';
    protected static ?string $pluralLabel = 'Property Status';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status_name')
                    ->label('Status Name')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status_name')->label('Status Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime(),
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
            'index' => Pages\ListPropertyStatus::route('/'),
            'create' => Pages\CreatePropertyStatus::route('/create'),
            'edit' => Pages\EditPropertyStatus::route('/{record}/edit'),
        ];
    }
}
