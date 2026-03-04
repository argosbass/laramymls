<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Property;

class SyncMediaOrderFromDelta extends Command
{
    // Ejemplos:
    // php artisan media:sync-order-from-delta
    // php artisan media:sync-order-from-delta --property-id=11512
    // php artisan media:sync-order-from-delta --limit=100

    protected $signature = 'media:sync-order-from-delta {--limit=100} {--property-id=0}';
    protected $description = 'Sincroniza media.order_column usando property_photos.delta. Procesa solo photo_alt=1 y marca photo_alt=2 al finalizar OK.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit') ?: 100;
        $propertyIdFilter = (int) $this->option('property-id');

        $q = DB::table('property_photos')
            ->select(['id','property_id','photo_url','delta','photo_alt'])
            ->whereNotNull('delta')
            ->where('photo_alt', 1)              // ✅ solo pendientes
            ->orderBy('property_id')
            ->orderBy('delta')
            ->limit($limit);                     // ✅ lote de 100

        if ($propertyIdFilter > 0) {
            $q->where('property_id', $propertyIdFilter);
        }

        $rows = $q->get();

        if ($rows->isEmpty()) {
            $this->info('✔️ No hay rows (photo_alt=1) pendientes.');
            return self::SUCCESS;
        }

        $updated = 0;
        $markedOk = 0;
        $notFound = 0;

        foreach ($rows as $r) {

            $targetOrder = ((int) $r->delta) + 1;
            $expectedFileName = $this->generateFileName($r->photo_url);

            $media = Media::query()
                ->where('model_type', Property::class)
                ->where('model_id', $r->property_id)
                ->where(function ($qq) use ($expectedFileName, $r) {
                    $qq->where('file_name', $expectedFileName)
                        ->orWhereRaw('? LIKE CONCAT("%/", file_name)', [$r->photo_url]);
                })
                ->first();

            if (!$media) {
                $notFound++;
                // ✅ lo dejamos en 1 para reintentar luego
                // (si quieres marcar “no encontrado”, abajo te dejo la opción)
                continue;
            }

            // Actualiza orden si hace falta
            if ((int) $media->order_column !== $targetOrder) {
                Media::query()->whereKey($media->id)->update(['order_column' => $targetOrder]);
                $updated++;
            }

            // ✅ Marcar como procesado OK
            DB::table('property_photos')
                ->where('id', $r->id)
                ->update(['photo_alt' => 2]);

            $markedOk++;
        }

        $this->info("✅ Lote procesado: " . $rows->count());
        $this->info("✅ order_column actualizados: {$updated}");
        $this->info("✅ marcados photo_alt=2: {$markedOk}");
        $this->warn("⚠️ no encontrados (se quedan en photo_alt=1): {$notFound}");

        return self::SUCCESS;
    }

    protected function generateFileName(string $url): string
    {
        $fileName = basename((string) parse_url($url, PHP_URL_PATH));

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

        if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
            $fileName .= '.jpg';
        }

        return $fileName;
    }
}
