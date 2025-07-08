<?php
namespace App\Filament\Resources;

use App\Models\PropertyFeatures;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\PropertyFeatureResource\Pages;

class PropertyFeatureResource extends Resource
{
    protected static ?string $model = PropertyFeatures::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $label = 'Property Feature';
    protected static ?string $pluralLabel = 'Property Features';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('feature_name')
                    ->label('Feature Name')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('feature_name')->label('Feature Name')->searchable()->sortable(),

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
            'index' => Pages\ListPropertyFeatures::route('/'),
            'create' => Pages\CreatePropertyFeature::route('/create'),
            'edit' => Pages\EditPropertyFeature::route('/{record}/edit'),
        ];
    }
}
