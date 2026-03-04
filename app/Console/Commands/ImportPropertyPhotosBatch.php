<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
    //  protected function processBatch()
    //  {
    //      $photos = DB::table('property_photos')
    //        ->whereNull('photo_alt')
    //        ->limit(10)
    //        ->get();
    //
    //      if ($photos->isEmpty()) {
    //            $this->info("✔️ No hay más fotos por procesar.");
    //          return 0;
    //      }
    //
    //      $this->info("🔄 Procesando lote de " . $photos->count() . " fotos...");
    //
    //      foreach ($photos as $photo) {
    //          $property = Property::find($photo->property_id);
    //
    //          if (!$property) {
    //              $this->warn("❌ Propiedad {$photo->property_id} no encontrada.");
    //              $this->markAsErrorAndProcessed($photo->id, "Propiedad {$photo->property_id} no encontrada");
    //              continue;
    //          }
    //
    //          $this->processPhoto($photo, $property);
    //      }
    //
    //      return 0;
    //  }

    protected function processBatch()
    {
        $photos = DB::table('property_photos as pp')
            ->join('properties as p', 'p.id', '=', 'pp.property_id')
            ->whereNull('pp.photo_alt')
            ->orderBy('p.property_status_id', 'asc')
            ->limit(10)
            ->select('pp.*') // importante
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
                $this->markAsErrorAndProcessed(
                    $photo->id,
                    "Propiedad {$photo->property_id} no encontrada"
                );
                continue;
            }

            $this->processPhoto($photo, $property);
        }

        return 0;
    }

    /**
     * Procesa una foto individual con descarga manual
     */
    protected function processPhoto($photo, $property): bool
    {
        $cleanUrl = $this->cleanUrl($photo->photo_url);

        if (!$cleanUrl) {
            $msg = "URL inválida";
            $this->warn("⚠️ {$msg}: {$photo->photo_url}");
            $this->markAsErrorAndProcessed($photo->id, $msg);
            return false;
        }

        // Validar que sea una imagen por extensión (filtro rápido)
        if (!$this->isValidImageUrl($cleanUrl)) {
            $msg = "No es una imagen válida (extensión)";
            $this->warn("⚠️ {$msg}: {$cleanUrl}");
            $this->markAsErrorAndProcessed($photo->id, $msg);
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
        try {
            $tempFile = $this->downloadImage($cleanUrl);
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            $this->warn("⚠️ No se pudo descargar: {$cleanUrl} | {$msg}");
            $this->markAsErrorAndProcessed($photo->id, $msg);
            return false;
        }

        if (!$tempFile) {
            $msg = "No se pudo descargar (sin detalle)";
            $this->warn("⚠️ {$msg}: {$cleanUrl}");
            $this->markAsErrorAndProcessed($photo->id, $msg);
            return false;
        }

        try {
            $media = $property
                ->addMedia($tempFile)
                ->usingFileName($fileName)
                ->toMediaCollection('gallery');

            // ✅ setear orden desde delta (si existe)
            if (isset($photo->delta) && $photo->delta !== null)
            {
                $order = ((int) $photo->delta) + 1;

                // método rápido: update directo (evita eventos/overhead)
                Media::query()
                    ->whereKey($media->id)
                    ->update(['order_column' => $order]);
            }

            // Limpiar archivo temporal
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }

            $this->info("✅ Foto importada: {$fileName} en propiedad {$property->id}");
            $this->markAsProcessed($photo->id);
            return true;

        } catch (\Throwable $e) {
            // Limpiar archivo temporal en caso de error
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }

            $msg = "MediaLibrary: " . $e->getMessage();
            $this->error("❌ Error con {$photo->photo_url}: {$msg}");
            $this->markAsErrorAndProcessed($photo->id, $msg);
            return false;
        }
    }

    /**
     * Descarga imagen usando cURL con BASIC AUTH
     */
    protected function downloadImage(string $url): string
    {
        $this->info("📥 Descargando: {$url}");

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_MAXREDIRS => 5,

            // ✅ BASIC AUTH
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD  => 'mls:mls',

            // ✅ headers tipo navegador (evita bloqueos)
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                'Accept-Language: es-CR,es;q=0.9,en;q=0.8',
                'Referer: https://franravi.mymls-cr.com/',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
            ],

            CURLOPT_ENCODING => '',

            // Nota: en prod idealmente true. Lo dejo como venías para evitar fallos por SSL.
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $data = curl_exec($ch);

        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error       = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \RuntimeException("cURL Error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("HTTP Error: {$httpCode}");
        }

        if (!$data) {
            throw new \RuntimeException("No se recibieron datos");
        }

        $contentTypeLower = strtolower((string) $contentType);
        $isImageByHeader = $contentTypeLower !== '' && str_contains($contentTypeLower, 'image/');

        // A veces el server manda mal el content-type (text/html) aunque devuelva imagen.
        $isImageByBytes = @getimagesizefromstring($data) !== false;

        if (!$isImageByHeader && !$isImageByBytes) {
            throw new \RuntimeException("Tipo de contenido no válido: " . ($contentType ?: 'null'));
        }

        // Crear archivo temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'photo_');
        if (!$tempFile) {
            throw new \RuntimeException("No se pudo crear archivo temporal");
        }

        $written = file_put_contents($tempFile, $data);
        if ($written === false) {
            throw new \RuntimeException("No se pudo escribir archivo temporal");
        }

        $this->info("✅ Descargado: " . number_format(strlen($data)) . " bytes");
        return $tempFile;
    }

    /**
     * Valida si la URL es de una imagen por extensión
     */
    protected function isValidImageUrl(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        return in_array($extension, $validExtensions, true);
    }

    /**
     * Genera un nombre de archivo limpio
     */
    protected function generateFileName(string $url): string
    {
        $fileName = basename((string) parse_url($url, PHP_URL_PATH));

        // Limpia algunos escapes típicos
        $fileName = str_replace(
            [
                '%20','%21','%22','%23','%24','%25','%26','%27',
                '%28','%29','%2B','%2C','%3A','%3B','%3D',
                '%3F','%40','%5B','%5D',
                '%E2%80%8B','%E2%80%AF','%C2%A0',
                ' '
            ],
            '',
            $fileName
        );


        // Si no tiene extensión, añade .jpg por defecto
        if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
            $fileName .= '.jpg';
        }

        return $fileName;
    }

    protected function markAsProcessed($id): void
    {
        DB::table('property_photos')
            ->where('id', $id)
            ->update(['photo_alt' => true]);
    }

    /**
     * Guarda error en photo_title y marca como procesado (photo_alt=true)
     */
    protected function markAsErrorAndProcessed($id, string $message): void
    {
        $message = trim(preg_replace('/\s+/', ' ', $message));
        $message = mb_substr($message, 0, 240);

        DB::table('property_photos')
            ->where('id', $id)
            ->update([
                'photo_title' => 'error: ' . $message,
                'photo_alt'   => true, // ✅ igual lo marca como procesado
            ]);
    }

    protected function cleanUrl(string $url): ?string
    {
        $url = trim($url);

        // Separa base y resto
        if (!preg_match('#^(https?://[^/]+)(/.*)$#i', $url, $m)) {
            return null;
        }

        $base = $m[1];
        $rest = $m[2];

        // 1) Si hay "#", SIEMPRE es fragment: debe ser parte del filename => encodearlo
        // (porque si no, parse_url lo corta)
        $rest = str_replace('#', '%23', $rest);

        // 2) Caso especial: "?" dentro del filename (sin query real)
        // Si después de "?" no hay "=" ni "&", casi seguro no es query string
        if (str_contains($rest, '?')) {
            $pos = strpos($rest, '?');
            $after = substr($rest, $pos + 1);

            // Heurística: no parece query (no tiene = ni &)
            if (!str_contains($after, '=') && !str_contains($after, '&')) {
                // Encode solo el primer "?" que está rompiendo el path
                $rest = substr($rest, 0, $pos) . '%3F' . $after;
            }
        }

        // 3) Ahora sí: separar query REAL (si existiera)
        $path = $rest;
        $query = '';

        if (str_contains($rest, '?')) {
            [$path, $q] = explode('?', $rest, 2);
            $query = '?' . $q;
        }

        // 4) Encodear cada segmento del path (maneja espacios, (), etc.)
        $segments = explode('/', $path);
        $segments = array_map(function ($seg) {
            return rawurlencode(rawurldecode($seg));
        }, $segments);

        return $base . implode('/', $segments) . $query;
    }
}
