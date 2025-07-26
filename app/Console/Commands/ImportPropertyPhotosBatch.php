<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use Illuminate\Support\Str;

class ImportPropertyPhotosBatch extends Command
{
    protected $signature = 'photos:import-batch';
    protected $description = 'Importa 10 fotos externas por lote a MediaLibrary desde property_photos';

    public function handle()
    {
        $photos = DB::table('property_photos')
            ->whereNull('photo_alt')
            ->limit(100)
            ->get();

        if ($photos->isEmpty()) {
            $this->info("✔️ No hay más fotos por procesar.");
            return;
        }

        foreach ($photos as $photo) {
            $property = Property::find($photo->property_id);

            if (!$property) {
                $this->warn("❌ Propiedad {$photo->property_id} no encontrada.");
                $this->markAsProcessed($photo->id);
                continue;
            }

            $cleanUrl = $this->cleanUrl($photo->photo_url);

            if (!$cleanUrl) {
                $this->warn("⚠️ URL inválido: {$photo->photo_url}");
                $this->markAsProcessed($photo->id);
                continue;
            }

            $fileName = basename(parse_url($cleanUrl, PHP_URL_PATH));

            // Evita duplicados
            if ($property->getMedia('gallery')->where('file_name', $fileName)->isNotEmpty()) {
                $this->info("↪️ Ya existe {$fileName} en propiedad {$property->id}");
                $this->markAsProcessed($photo->id);
                continue;
            }

            try {
                $property
                    ->addMediaFromUrl($cleanUrl)
                    ->usingFileName($fileName)
                    ->withResponsiveImages()
                    ->toMediaCollection('gallery');

                $this->info("✅ Foto importada: {$fileName} en propiedad {$property->id}");

                // Marcar como procesada
                $this->markAsProcessed($photo->id);
            } catch (\Exception $e) {
                $this->error("❌ Error con {$photo->photo_url}: " . $e->getMessage());
                // No marcar como procesada para reintentar luego
            }
        }
    }

    protected function markAsProcessed($id)
    {
        DB::table('property_photos')->where('id', $id)->update(['photo_alt' => true]);
    }

    protected function cleanUrl($url)
    {
        $parts = parse_url(trim($url));

        if (!isset($parts['scheme'], $parts['host'])) {
            return null; // URL inválido
        }

        $scheme = $parts['scheme'] . '://';
        $host = $parts['host'];
        $path = isset($parts['path'])
            ? implode('/', array_map('rawurlencode', explode('/', $parts['path'])))
            : '';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return $scheme . $host . $path . $query;
    }
}
