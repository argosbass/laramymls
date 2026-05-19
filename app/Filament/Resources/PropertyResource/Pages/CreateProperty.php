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
                    ->toMediaCollection('gallery');
            }
        }

        $this->afterCreateLotM2();
    }

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('saveTop')
                ->label('Save')
                ->color('primary')
                ->action('create'),

            Actions\Action::make('saveAndAddNew')
                ->label('Save and add New')
                ->color('gray')
                ->action(function () {
                    $this->save();

                    $this->redirect(
                        static::getResource()::getUrl('create')
                    );
                }),

            Actions\Action::make('cancelTop')
                ->label('Cancel')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),


        ];



    }

    protected function afterCreateLotM2(): void
    {
        $quantity = $this->data['property_lot_size_area_quantity'] ?? null;
        $unit = $this->data['property_lot_size_area_unit'] ?? null;

        if (! is_numeric($quantity)) {
            $this->record->update([
                'property_lot_size_m2' => null,
            ]);

            return;
        }

        $m2 = $unit === 'sqft'
            ? round((float) $quantity / 10.7639, 4)
            : round((float) $quantity, 4);

        $this->record->update([
            'property_lot_size_m2' => $m2,
        ]);
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

    protected function getRedirectUrl(): string
    {
        if( isset( $this->record->slug ) && !empty( $this->record->slug ) )
        {
            return url('/property-listing/' . $this->record->slug);

        }

        return url('/property-listing-id/' . $this->record->id);
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return null;
    }
}
