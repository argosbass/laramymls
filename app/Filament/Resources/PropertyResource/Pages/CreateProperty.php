<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function afterCreate(): void
    {
        $tempImages = $this->form->getState()['temp_images'] ?? [];

        if (! empty($tempImages)) {
            foreach ($tempImages as $path) {
                $this->record
                    ->addMedia(storage_path("app/public/{$path}"))
                    ->preservingOriginal()
                    ->toMediaCollection('images');
            }
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Save'),
            $this->getCreateAnotherFormAction()
                ->label('Save and add New'),
            $this->getCancelFormAction(),
        ];
    }
}
