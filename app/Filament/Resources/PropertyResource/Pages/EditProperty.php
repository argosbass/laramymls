<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('public_view')
                ->label('Public View')
                ->icon('heroicon-o-globe-alt')
                ->url(fn () => url('/property-listing-id/' . $this->record->id))
                ->color('gray'),
            //         ->openUrlInNewTab(),

            Actions\Action::make('view')
                ->label('View Property')
                ->icon('heroicon-o-eye')
                ->url(fn () => PropertyResource::getUrl('view', ['record' => $this->record]))
                ->color('gray'),
             //   ->openUrlInNewTab(),

            Actions\DeleteAction::make()

        ];



    }

    protected function hasRelationManagersInTabs(): bool
    {
        return true;
    }

    protected function hasRelationManagers(): bool
    {
        return true;
    }

    public function mount($record): void
    {
        parent::mount($record);

        $state = $this->form->getState();

        $state['temp_images'] = $this->record->getImagePaths();

        $this->form->fill($state);
    }

    protected function afterSave(): void
    {
        $tempImages = $this->form->getState()['temp_images'] ?? [];

        // Obtener los media actuales
        $currentMedia = $this->record->getMedia('images');

        // Mapear paths actuales
        $currentPaths = $currentMedia->map(fn ($media) => str_replace('public/', '', $media->getPathRelativeToRoot()))->toArray();

        // Detectar qué archivos eliminar (están en current pero no en tempImages)
        $toDelete = array_diff($currentPaths, $tempImages);

        // Eliminar media correspondientes
        foreach ($currentMedia as $media) {
            $mediaPath = str_replace('public/', '', $media->getPathRelativeToRoot());
            if (in_array($mediaPath, $toDelete)) {
                $media->delete();
            }
        }

        // Agregar nuevas imágenes que no estén ya en la colección
        foreach ($tempImages as $path) {
            if (!in_array($path, $currentPaths)) {
                $fullPath = storage_path("app/public/{$path}");
                if (file_exists($fullPath)) {
                    $this->record
                        ->addMedia($fullPath)
                        ->preservingOriginal()
                        ->toMediaCollection('images');
                }
            }
        }
    }
}
