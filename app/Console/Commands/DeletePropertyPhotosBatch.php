<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;

class DeletePropertyPhotosBatch extends Command
{
    protected $signature = 'photos:delete-all';
    protected $description = 'Borra todas las fotos en MediaLibrary en la colección gallery de todas las propiedades';

    public function handle()
    {
        $properties = Property::all();

        if ($properties->isEmpty()) {
            $this->info('No hay propiedades para procesar.');
            return;
        }

        $totalDeleted = 0;

        foreach ($properties as $property) {
            $mediaItems = $property->getMedia('gallery');

            if ($mediaItems->isEmpty()) {
                continue;
            }

            foreach ($mediaItems as $media) {
                try {
                    $media->delete();
                    $totalDeleted++;
                } catch (\Exception $e) {
                    $this->error("Error eliminando media ID {$media->id} en propiedad {$property->id}: " . $e->getMessage());
                }
            }

            $this->info("Se eliminaron fotos de la propiedad ID {$property->id}");
        }

        $this->info("Proceso finalizado. Total fotos eliminadas: {$totalDeleted}");
    }
}
