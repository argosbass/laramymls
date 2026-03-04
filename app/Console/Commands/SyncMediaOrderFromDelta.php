<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Property;

class SyncMediaOrderFromDelta extends Command
{
    protected $signature = 'media:sync-order-from-delta {--limit=100} {--property-id=0}';
    protected $description = 'Sincroniza media.order_column usando property_photos.delta. Procesa solo photo_alt=1 y marca photo_alt=2 al finalizar (OK o error).';

    public function handle(): int
    {
        $limit = (int) $this->option('limit') ?: 100;
        $propertyIdFilter = (int) $this->option('property-id');

        $q = DB::table('property_photos')
            ->select(['id','property_id','photo_url','delta','photo_alt'])
            ->whereNotNull('delta')
            ->where('photo_alt', 1)
            ->orderBy('property_id')
            ->orderBy('delta')
            ->limit($limit);

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
        $markedError = 0;

        foreach ($rows as $r) {

            try {
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
                    // ❌ No encontrado: marcar como procesado pero con error
                    $this->markAsProcessedWithError($r->id, 'Error:Delta media_not_found');
                    $markedError++;
                    continue;
                }

                if ((int) $media->order_column !== $targetOrder) {
                    Media::query()->whereKey($media->id)->update(['order_column' => $targetOrder]);
                    $updated++;
                }

                // ✅ OK: marcar procesado
                DB::table('property_photos')
                    ->where('id', $r->id)
                    ->update(['photo_alt' => 2]);

                $markedOk++;

            } catch (\Throwable $e) {
                // ❌ Cualquier excepción: marcar procesado pero con error
                $msg = 'Error:Delta ' . $e->getMessage();
                $this->markAsProcessedWithError($r->id, $msg);
                $markedError++;
            }
        }

        $this->info("✅ Lote procesado: " . $rows->count());
        $this->info("✅ order_column actualizados: {$updated}");
        $this->info("✅ marcados OK (photo_alt=2): {$markedOk}");
        $this->warn("⚠️ marcados con error (photo_alt=2 + photo_title): {$markedError}");

        return self::SUCCESS;
    }

    protected function markAsProcessedWithError(int $id, string $message): void
    {
        $message = trim(preg_replace('/\s+/', ' ', $message));
        $message = mb_substr($message, 0, 240);

        DB::table('property_photos')
            ->where('id', $id)
            ->update([
                'photo_alt'   => 2,
                'photo_title' => $message, // ej: "Error:Delta ..."
            ]);
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
