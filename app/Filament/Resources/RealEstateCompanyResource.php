<?php

namespace App\Filament\Resources;

use App\Models\RealEstateCompany;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\RealEstateCompanyResource\Pages;

class RealEstateCompanyResource extends Resource
{
    protected static ?string $model = RealEstateCompany::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $label = 'Real Estate Company';
    protected static ?string $pluralLabel = 'Real Estate Companies';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('company_title')->label('Title'),
            Forms\Components\TextInput::make('company_name')->label('Name')->maxLength(255),
            Forms\Components\TextInput::make('company_main_contact')->label('Main Contact')->maxLength(255),
            Forms\Components\TextInput::make('company_main_telephone')->label('Phone')->maxLength(100),
            Forms\Components\RichEditor::make('company_notes_to_agents')->label('Notes to Agents'),
            Forms\Components\TextInput::make('company_website_url')->label('Website URL')->url()->maxLength(255),
            Forms\Components\TextInput::make('company_website_text')->label('Website Text')->maxLength(255),
            Forms\Components\TextInput::make('company_city_town')->label('City / Town')->maxLength(100),
            Forms\Components\TextInput::make('company_post_code')->label('Postal Code')->maxLength(50),
            Forms\Components\TextInput::make('company_province')->label('Province')->maxLength(100),
            Forms\Components\TextInput::make('company_street_address_1')->label('Address Line 1')->maxLength(255),
            Forms\Components\TextInput::make('company_street_address_2')->label('Address Line 2')->maxLength(255),
            Forms\Components\Hidden::make('nid')->label('NID'),
            Forms\Components\Toggle::make('published')->label('Published'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_title')->label('Title')->searchable(),
                Tables\Columns\TextColumn::make('company_name')->label('Name')->searchable(),
                Tables\Columns\TextColumn::make('company_main_contact')->label('Contact'),
                Tables\Columns\TextColumn::make('company_main_telephone')->label('Phone'),
                Tables\Columns\IconColumn::make('published')->label('Published')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
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
            'index' => Pages\ListRealEstateCompanies::route('/'),
            'create' => Pages\CreateRealEstateCompany::route('/create'),
            'edit' => Pages\EditRealEstateCompany::route('/{record}/edit'),
        ];
    }
}
