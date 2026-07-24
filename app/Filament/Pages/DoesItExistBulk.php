<?php

namespace App\Filament\Pages;

use App\Models\PropertyListingCompetitor;
use Carbon\Carbon;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DoesItExistBulk extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = 'Does It Exist Bulk';

    protected static ?string $title = 'Does It Exist Bulk';

    protected static string $view = 'filament.pages.does-it-exist-bulk';

    protected static ?string $navigationGroup = 'Search Tools';

    public ?string $urls = '';

    public array $results = [];

    public function resetSearch(): void
    {
        $this->urls = '';
        $this->results = [];
    }

    /**
     * Limpia una URL pegada por el usuario.
     */
    private function cleanInputUrl(?string $url): string
    {
        if (blank($url)) {
            return '';
        }

        // Elimina caracteres invisibles, BOM, saltos de línea, etc.
        $url = preg_replace(
            '/[\x00-\x1F\x7F\x{00A0}\x{FEFF}]/u',
            '',
            $url
        );

        // Quita espacios al inicio y al final.
        $url = trim($url);

        // Quita comillas simples y dobles.
        $url = trim($url, "\"'");

        // Elimina espacios internos accidentales.
        $url = preg_replace('/\s+/', '', $url);

        // Quita slash final solamente para mostrar una URL más limpia.
        $url = rtrim($url, '/');

        return $url;
    }

    /**
     * Normaliza una URL para compararla.
     *
     * Ignora:
     * - http o https
     * - www
     * - slash final
     * - mayúsculas en el dominio
     */
    private function normalizeUrl(?string $url): string
    {
        $url = $this->cleanInputUrl($url);

        if ($url === '') {
            return '';
        }

        /*
         * Agregamos un protocolo temporal en caso de que la URL
         * almacenada no tenga http:// o https://.
         */
        $urlForParsing = preg_match('#^https?://#i', $url)
            ? $url
            : 'https://' . ltrim($url, '/');

        $parts = parse_url($urlForParsing);

        /*
         * Si parse_url no puede analizarla, hacemos una
         * normalización sencilla.
         */
        if ($parts === false || empty($parts['host'])) {
            $normalized = preg_replace('#^https?://#i', '', $url);
            $normalized = preg_replace('#^www\.#i', '', $normalized);

            return strtolower(rtrim($normalized, '/'));
        }

        $host = strtolower($parts['host']);

        // Considera www.ejemplo.com igual a ejemplo.com.
        $host = preg_replace('/^www\./i', '', $host);

        // Conserva el puerto en caso de existir.
        $port = isset($parts['port'])
            ? ':' . $parts['port']
            : '';

        $path = $parts['path'] ?? '';

        // Reemplaza múltiples slashes dentro de la ruta.
        $path = preg_replace('#/+#', '/', $path);

        // Ignora el slash final.
        $path = rtrim($path, '/');

        /*
         * Conservamos los parámetros de la URL porque podrían
         * identificar propiedades diferentes.
         */
        $query = isset($parts['query']) && $parts['query'] !== ''
            ? '?' . $parts['query']
            : '';

        return $host . $port . $path . $query;
    }

    public function search(): void
    {
        /*
         * Conservamos la URL original limpia y también generamos
         * su versión normalizada para realizar la comparación.
         */
        $urlList = collect(
            preg_split('/\r\n|\r|\n/', $this->urls ?? '')
        )
            ->map(function ($url) {
                $cleanUrl = $this->cleanInputUrl($url);

                return [
                    'original' => $cleanUrl,
                    'normalized' => $this->normalizeUrl($cleanUrl),
                ];
            })
            ->filter(function (array $urlData) {
                if ($urlData['original'] === '') {
                    return false;
                }

                /*
                 * Si no contiene protocolo, agregamos uno únicamente
                 * para validar la estructura de la URL.
                 */
                $urlForValidation = preg_match(
                    '#^https?://#i',
                    $urlData['original']
                )
                    ? $urlData['original']
                    : 'https://' . ltrim($urlData['original'], '/');

                return filter_var(
                        $urlForValidation,
                        FILTER_VALIDATE_URL
                    ) !== false;
            })
            /*
             * Evita repetir la misma URL cuando una viene con HTTP,
             * otra con HTTPS o una tiene slash final.
             */
            ->unique('normalized')
            ->values();

        /*
         * No podemos usar whereIn directamente porque la URL almacenada
         * podría tener un protocolo o slash diferente.
         *
         * Cargamos los registros y los indexamos por la URL normalizada.
         */
        $competitors = PropertyListingCompetitor::query()
            ->with([
                'company',
                'property.status',
            ])
            ->whereNotNull('competitor_property_link')
            ->where('competitor_property_link', '!=', '')
            ->get()
            ->filter(function (PropertyListingCompetitor $competitor) {
                return $this->normalizeUrl(
                        $competitor->competitor_property_link
                    ) !== '';
            })
            ->keyBy(function (PropertyListingCompetitor $competitor) {
                return $this->normalizeUrl(
                    $competitor->competitor_property_link
                );
            });

        $this->results = $urlList
            ->map(function (array $urlData) use ($competitors) {
                $item = $competitors->get($urlData['normalized']);

                return [
                    'exists' => $item !== null,

                    'company' => $item?->company?->company_name,

                    'property_title' => $item?->property?->property_title,

                    'added_date' => $item?->property?->property_added_date
                        ? Carbon::parse(
                            $item->property->property_added_date
                        )->format('Y-m-d')
                        : null,

                    'status' => $item?->property?->status?->status_name,

                    /*
                     * Mostramos la URL que el usuario ingresó.
                     */
                    'reference_link' => $urlData['original'],

                    /*
                     * También guardamos la URL encontrada en la base de datos.
                     * Esto puede servir para verificar qué variante coincidió.
                     */
                    'database_link' => $item?->competitor_property_link,

                    'view_url' => $item?->property
                        ? route(
                            'filament.admin.resources.properties.view',
                            [
                                'record' => $item->property->id,
                            ]
                        )
                        : null,

                    'edit_url' => $item?->property
                        ? route(
                            'filament.admin.resources.properties.edit',
                            [
                                'record' => $item->property->id,
                            ]
                        )
                        : null,
                ];
            })
            // Los encontrados primero y los Not Found al final.
            ->sortByDesc('exists')
            ->values()
            ->toArray();
    }

    public function downloadCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            /*
             * Agrega BOM para que Excel reconozca correctamente UTF-8.
             */
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Listing Company',
                'Property Title',
                'Added Date',
                'Property Status',
                'Reference Link',
                'Database Link',
                'Exists',
            ]);

            foreach ($this->results as $row) {
                fputcsv($handle, [
                    $row['company'] ?? '',
                    $row['property_title'] ?? '',
                    $row['added_date'] ?? '',
                    $row['status'] ?? '',
                    $row['reference_link'] ?? '',
                    $row['database_link'] ?? '',
                    !empty($row['exists']) ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, 'does-it-exist-bulk-results.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
