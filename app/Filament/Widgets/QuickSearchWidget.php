<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

class QuickSearchWidget extends Widget implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $view = 'filament.widgets.quick-search-widget';
    protected static ?int $sort = 3;

    public ?string $search = '';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Property::query()->when($this->search, function (Builder $query) {
                    $query->where('property_title', 'like', '%' . $this->search . '%');
                })->limit(5)
            )
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('property_title')->label('Title')->limit(30),
                TextColumn::make('type.type_name')->label('Type'),
                TextColumn::make('property_price')
                    ->label('Price')
                    ->money('USD'),
            ])
            ->actions([
                Action::make('view')
                    ->url(fn (Property $record): string => route('filament.admin.resources.properties.view', $record))
                    ->icon('heroicon-o-eye'),
                Action::make('edit')
                    ->url(fn (Property $record): string => route('filament.admin.resources.properties.edit', $record))
                    ->icon('heroicon-o-pencil'),
            ])
            ->headerActions([
                Action::make('create_property')
                    ->label('Add New Property')
                    ->url(route('filament.admin.resources.properties.create'))
                    ->icon('heroicon-o-plus')
                    ->color('success'),
            ]);
    }

    public function updatedSearch(): void
    {
        $this->resetTable();
    }
}
