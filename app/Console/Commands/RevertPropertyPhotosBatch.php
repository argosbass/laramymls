<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Property;

class RevertPropertyPhotosBatch extends Command
{
    protected $signature = 'photos:revert-batch';
    protected $description = 'Borra las fotos importadas en MediaLibrary y revierte el marcado en property_photos';

    public function handle()
    {
        // Obtener fotos marcadas como procesadas (photo_alt = true)
        $photos = DB::table('property_photos')
            ->where('photo_alt', true)
            ->limit(100)
            ->get();

        if ($photos->isEmpty()) {
            $this->info("✔️ No hay fotos importadas para revertir.");
            return;
        }

        foreach ($photos as $photo) {
            $property = Property::find($photo->property_id);

            if (!$property) {
                $this->warn("❌ Propiedad {$photo->property_id} no encontrada.");
                // Igual se quita la marca para no quedar bloqueado?
                $this->unmarkAsProcessed($photo->id);
                continue;
            }

            $fileName = basename(parse_url($photo->photo_url, PHP_URL_PATH));

            // Buscar el medio importado por file_name en la colección 'gallery'
            $mediaItems = $property->getMedia('gallery')->where('file_name', $fileName);

            if ($mediaItems->isEmpty()) {
                $this->warn("⚠️ Foto {$fileName} no encontrada en propiedad {$property->id}");
                $this->unmarkAsProcessed($photo->id);
                continue;
            }

            try {
                foreach ($mediaItems as $media) {
                    $media->delete();
                }

                $this->info("🗑️ Foto eliminada: {$fileName} de propiedad {$property->id}");

                // Desmarcar la foto para que quede para reimportar si se desea
                $this->unmarkAsProcessed($photo->id);
            } catch (\Exception $e) {
                $this->error("❌ Error eliminando foto {$fileName}: " . $e->getMessage());
            }
        }
    }

    protected function unmarkAsProcessed($id)
    {
        DB::table('property_photos')->where('id', $id)->update(['photo_alt' => null]);
    }
}
