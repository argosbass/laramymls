<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
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
                Property::query()
                    ->when($this->search, function (Builder $query) {
                        // Buscar solo por título
                        $query->where('property_title', 'like', '%' . $this->search . '%');
                    })
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('property_title')
                    ->label('Title')
                    ->limit(50),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false)
            ->actions([
                Action::make('edit')
                    ->url(fn (Property $record): string => route('filament.admin.resources.properties.edit', $record))
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->color('warning'),
                Action::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Property $record) => $record->delete())
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->headerActions([
                Action::make('create_property')
                    ->label('Add New Property')
                    ->url(route('filament.admin.resources.properties.create'))
                    ->icon('heroicon-o-plus')
                    ->color('success'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('No properties found')
            ->emptyStateDescription('Try adjusting your search terms or create a new property.');
    }

    public function updatedSearch(): void
    {
        $this->resetTable();
    }
}
