<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Property;

class SyncMediaOrderFromDelta extends Command
{
    // php artisan media:sync-order-from-delta --limit=500
    protected $signature = 'media:sync-order-from-delta {--limit=0} {--property-id=0}';
    protected $description = 'Sincroniza media.order_column usando property_photos.delta (orden de Drupal).';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $propertyIdFilter = (int) $this->option('property-id');

        $q = DB::table('property_photos')
            ->select(['id','property_id','photo_url','delta'])
            ->whereNotNull('delta')
            ->orderBy('property_id')
            ->orderBy('delta');

        if ($propertyIdFilter > 0) {
            $q->where('property_id', $propertyIdFilter);
        }

        if ($limit > 0) {
            $q->limit($limit);
        }

        $rows = $q->get();

        if ($rows->isEmpty()) {
            $this->info('No hay rows con delta para procesar.');
            return self::SUCCESS;
        }

        $updated = 0;
        $notFound = 0;

        foreach ($rows as $r) {

            // orden en Spatie usualmente es 1-based
            $targetOrder = ((int) $r->delta) + 1;

            // Genera el file_name esperado con TU misma lógica
            $expectedFileName = $this->generateFileName( $r->photo_url);

            $this->info($r->photo_url );
            $this->info($expectedFileName );

            // Busca media por modelo + (match por filename exacto) o (URL termina en filename)
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
                continue;
            }

            if ((int) $media->order_column !== $targetOrder) {
                $media->order_column = $targetOrder;
                $media->save();
                $updated++;
            }
        }

        $this->info("✅ Actualizados: {$updated}");
        $this->warn("⚠️ No encontrados: {$notFound}");

        return self::SUCCESS;
    }

    protected function generateFileName(string $url): string
    {
        $fileName = basename((string) parse_url($url, PHP_URL_PATH));

        // misma limpieza que ya usas
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
