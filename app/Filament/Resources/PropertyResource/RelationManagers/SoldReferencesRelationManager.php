<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SoldReferencesRelationManager extends RelationManager
{
    protected static string $relationship = 'SoldReferences';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('sold_reference_date')
                    ->required(),
                Forms\Components\TextInput::make('sold_reference_price')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('sold_reference_notes')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sold_references')
            ->columns([
                Tables\Columns\TextColumn::make('sold_reference_date'),
                Tables\Columns\TextColumn::make('sold_reference_price'),
                Tables\Columns\TextColumn::make('sold_reference_notes'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
