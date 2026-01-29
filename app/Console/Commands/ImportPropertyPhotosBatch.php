<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use Illuminate\Support\Str;

class ImportPropertyPhotosBatch extends Command
{
    protected $signature = 'photos:import-batch {--property-id= : ID específico de propiedad para procesar}';
    protected $description = 'Importa fotos externas por lote a MediaLibrary desde property_photos';

    public function handle()
    {
        // Aumentar límites para el servidor
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $propertyId = $this->option('property-id');

        if ($propertyId) {
            return $this->processSpecificProperty($propertyId);
        }

        return $this->processBatch();
    }

    /**
     * Procesa una propiedad específica
     */
    protected function processSpecificProperty($propertyId)
    {
        $this->info("🔍 Procesando propiedad específica: {$propertyId}");

        $property = Property::find($propertyId);

        if (!$property) {
            $this->error("❌ Propiedad {$propertyId} no encontrada.");
            return 1;
        }

        $photos = DB::table('property_photos')
            ->where('property_id', $propertyId)
            ->whereNull('photo_alt')
            ->get();

        if ($photos->isEmpty()) {
            $this->info("✔️ No hay fotos pendientes para la propiedad {$propertyId}.");
            return 0;
        }

        $processed = 0;
        $errors = 0;

        foreach ($photos as $photo) {
            if ($this->processPhoto($photo, $property)) {
                $processed++;
            } else {
                $errors++;
            }
        }

        $this->info("📊 Propiedad {$propertyId}: {$processed} fotos procesadas, {$errors} errores.");
        return 0;
    }

    /**
     * Procesa por lotes (para cron)
     */
    protected function processBatch()
    {
        $photos = DB::table('property_photos')
            ->whereNull('photo_alt')
            ->limit(10)
            ->get();

        if ($photos->isEmpty()) {
            $this->info("✔️ No hay más fotos por procesar.");
            return 0;
        }

        $this->info("🔄 Procesando lote de " . $photos->count() . " fotos...");

        foreach ($photos as $photo) {
            $property = Property::find($photo->property_id);

            if (!$property) {
                $this->warn("❌ Propiedad {$photo->property_id} no encontrada.");
                $this->markAsProcessed($photo->id);
                continue;
            }

            $this->processPhoto($photo, $property);
        }

        return 0;
    }

    /**
     * Procesa una foto individual con descarga manual
     */
    protected function processPhoto($photo, $property)
    {
        $cleanUrl = $this->cleanUrl($photo->photo_url);

        if (!$cleanUrl) {
            $this->warn("⚠️ URL inválido: {$photo->photo_url}");
            $this->markAsProcessed($photo->id);
            return false;
        }

        // Validar que sea una imagen
        if (!$this->isValidImageUrl($cleanUrl)) {
            $this->warn("⚠️ No es una imagen válida: {$cleanUrl}");
            $this->markAsProcessed($photo->id);
            return false;
        }

        $fileName = $this->generateFileName($cleanUrl);

        // Evita duplicados
        if ($property->getMedia('gallery')->where('file_name', $fileName)->isNotEmpty()) {
            $this->info("↪️ Ya existe {$fileName} en propiedad {$property->id}");
            $this->markAsProcessed($photo->id);
            return true;
        }

        // Descargar imagen manualmente
        $tempFile = $this->downloadImage($cleanUrl);

        if (!$tempFile) {
            $this->warn("⚠️ No se pudo descargar: {$cleanUrl}");
            $this->markAsProcessed($photo->id);
            return false;
        }

        try {
            $property
                ->addMedia($tempFile)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('gallery');

            // Limpiar archivo temporal
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            $this->info("✅ Foto importada: {$fileName} en propiedad {$property->id}");
            $this->markAsProcessed($photo->id);
            return true;

        } catch (\Exception $e) {
            // Limpiar archivo temporal en caso de error
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            $this->error("❌ Error con {$photo->photo_url}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Descarga imagen usando cURL con configuración robusta
     */
    protected function downloadImage($url)
    {
        $this->info("📥 Descargando: {$url}");

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; PropertyImporter/1.0)',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        curl_close($ch);

        if ($error) {
            $this->error("❌ cURL Error: {$error}");
            return false;
        }

        if ($httpCode !== 200) {
            $this->error("❌ HTTP Error: {$httpCode}");
            return false;
        }

        if (!$data) {
            $this->error("❌ No se recibieron datos");
            return false;
        }

        // Verificar que sea realmente una imagen
        if (!str_contains($contentType, 'image/')) {
            $this->error("❌ Tipo de contenido no válido: {$contentType}");
            return false;
        }

        // Crear archivo temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'photo_');
        $written = file_put_contents($tempFile, $data);

        if ($written === false) {
            $this->error("❌ No se pudo escribir archivo temporal");
            return false;
        }

        $this->info("✅ Descargado: " . number_format(strlen($data)) . " bytes");
        return $tempFile;
    }

    /**
     * Valida si la URL es de una imagen
     */
    protected function isValidImageUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        return in_array($extension, $validExtensions);
    }

    /**
     * Genera un nombre de archivo limpio
     */
    protected function generateFileName($url)
    {
        $fileName = basename(parse_url($url, PHP_URL_PATH));

        // Limpiar caracteres especiales de URL
        $fileName = str_replace('%20', '', $fileName);
        $fileName = str_replace('%21', '', $fileName);
        $fileName = str_replace('%22', '', $fileName);
        $fileName = str_replace('%23', '', $fileName);
        $fileName = str_replace('%24', '', $fileName);
        $fileName = str_replace('%25', '', $fileName);
        $fileName = str_replace('%26', '', $fileName);
        $fileName = str_replace('%27', '', $fileName);
        $fileName = str_replace('%28', '', $fileName);
        $fileName = str_replace('%29', '', $fileName);
        $fileName = str_replace(' ', '', $fileName);

        // Si no tiene extensión, añadir .jpg por defecto
        if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
            $fileName .= '.jpg';
        }

        return $fileName;
    }

    protected function markAsProcessed($id)
    {
        DB::table('property_photos')->where('id', $id)->update(['photo_alt' => true]);
    }

    protected function cleanUrl($url)
    {
        $parts = parse_url(trim($url));

        if (!isset($parts['scheme'], $parts['host'])) {
            return null;
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
