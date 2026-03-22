<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use App\Jobs\EnsureResponsiveImages;
use App\Jobs\EnsureThumbConversion;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [


            Actions\Action::make('saveTop')
                ->label('Save Changes')

                ->color('primary')
                ->action('save'),

            Actions\Action::make('cancelTop')
                ->label('Cancel')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),


            Actions\Action::make('public_view')
                ->label('View Property')
                ->icon('heroicon-o-eye')
                ->url(fn () => url('/property-listing-id/' . $this->record->id))
                ->color('success'),
            //         ->openUrlInNewTab(),




            Actions\DeleteAction::make()->color('warning')


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

        $this->dispatchThumbJobs();

        $state['temp_images'] = $this->record->getImagePaths();

        $this->form->fill($state);


    }

    protected function afterFill(): void
    {
            $this->dispatchThumbJobs();
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

    protected function dispatchResponsiveJobs(): void
    {
        $mediaItems = $this->record->getMedia('gallery');

        foreach ($mediaItems as $media) {
            $responsive = $media->responsive_images ?? [];
            if (empty($responsive)) {
                EnsureResponsiveImages::dispatch($media->id);
            }
        }
    }

    protected function dispatchThumbJobs(): void
    {
        $mediaItems = $this->record->getMedia('gallery');

        foreach ($mediaItems as $media) {
            $generated = $media->generated_conversions ?? [];

            if (empty($generated['thumb'])) {
                EnsureThumbConversion::dispatch($media->id);
            }
        }
    }
}
