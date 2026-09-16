<?php

namespace App\Filament\Pages;

use App\Models\Property;
use Filament\Pages\Page;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RossExternalPriceReport extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'ROSS Price Report';
    protected static ?string $title = 'ROSS Price Report';

    protected static string $view = 'filament.pages.ross-external-price-report';

    public array    $rows = [];

    public ?string  $resultFilter = null;

    public ?string  $sortColumn = "title";
    public string   $sortDirection = 'asc';

    public bool $isLoaded = false;

    public function loadRows(): void
    {
        $jsonUrl = 'https://www.remax-oceansurf-cr.com/wp-json/remax/v1/property-urls';

        $items = Http::withHeaders([
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
        ])
            ->get($jsonUrl . '?v=' . now()->timestamp)
            ->json() ?? [];

        foreach ($items as $item) {

            // ---------------------------------------------------------
            // URL
            // ---------------------------------------------------------
            $url = trim($item['Url'] ?? '');

            // Por seguridad, si Url no viene, usamos Path
            if (empty($url)) {
                $path = trim($item['Path'] ?? '');

                if (! empty($path)) {
                    $url = 'https://www.remax-oceansurf-cr.com' . $path;
                }
            }

            // Si no tenemos URL, no podemos comparar
            if (empty($url)) {
                continue;
            }

            // ---------------------------------------------------------
            // Datos externos ROSS
            // ---------------------------------------------------------
            $externalPrice = isset($item['Price'])
                ? (float) $item['Price']
                : null;

            $externalPropertyStatus = $item['PropertyStatus'] ?? null;

            // Campos adicionales disponibles en el nuevo endpoint
            $externalPropertyId = $item['PropertyId'] ?? null;
            $externalTitle      = $item['Title'] ?? '';
            $externalHidden     = $item['Hidden'] ?? null;
            $externalWpStatus   = $item['WpStatus'] ?? null;
            $externalUpdated    = $item['Updated'] ?? null;


            // ---------------------------------------------------------
            // Buscar propiedad local por competitor_property_link
            // ---------------------------------------------------------
            $property = Property::with([
                'listingCompetitors' => function ($query) use ($url) {
                    $query->where('competitor_property_link', $url);
                }
            ])
                ->whereHas('listingCompetitors', function ($query) use ($url) {
                    $query->where('competitor_property_link', $url);
                })
                ->first();


            // ---------------------------------------------------------
            // No existe en MLS
            // ---------------------------------------------------------
            if (! $property) {

                $this->rows[] = [
                    'id' => md5($url),

                    'title' => $externalTitle,

                    'url' => $url,

                    'local_price' => null,
                    'external_price' => $externalPrice,

                    'status' => 'Missing',

                    'rossPropertyStatus' => $externalPropertyStatus,
                    'mlsPropertyStatus' => null,

                    // Nuevos campos disponibles
                    'rossPropertyId' => $externalPropertyId,
                    'rossHidden' => $externalHidden,
                    'rossWpStatus' => $externalWpStatus,
                    'rossUpdated' => $externalUpdated,
                ];

                continue;
            }


            // ---------------------------------------------------------
            // Existe en MLS
            // ---------------------------------------------------------
            $localPrice = $property->property_price !== null
                ? (float) $property->property_price
                : null;

            $localPropertyStatus = $property->status?->status_name ?? '';


            // ---------------------------------------------------------
            // Resultado
            // ---------------------------------------------------------
            $this->rows[] = [
                'id' => $property->id,

                'title' => $property->property_title,

                'url' => $url,

                'local_price' => $localPrice,
                'external_price' => $externalPrice,

                'status' => $localPrice != $externalPrice
                    ? 'Price Different'
                    : 'OK',

                'rossPropertyStatus' => $externalPropertyStatus,
                'mlsPropertyStatus' => $localPropertyStatus,

                // Nuevos campos disponibles
                'rossPropertyId' => $externalPropertyId,
                'rossHidden' => $externalHidden,
                'rossWpStatus' => $externalWpStatus,
                'rossUpdated' => $externalUpdated,
            ];
        }

        $this->isLoaded = true;
    }
    public function mount(): void
    {

    }

    public function _getFilteredRowsProperty(): array
    {
        return collect($this->rows)
            ->when($this->resultFilter, function ($rows) {
                return $rows->where('status', $this->resultFilter);
            })
            ->values()
            ->toArray();
    }

    public function getFilteredRowsProperty()
    {
        $rows = collect($this->rows);

        if ($this->resultFilter) {
            $rows = $rows->where('status', $this->resultFilter);
        }

        if ($this->sortColumn) {
            $rows = $rows->sortBy(
                fn ($row) => strtolower($row[$this->sortColumn] ?? ''),
                SORT_REGULAR,
                $this->sortDirection === 'desc'
            );
        }

        return $rows->values();
    }


    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getStatsProperty(): array
    {
        return [
            'missing' => collect($this->rows)->where('status', 'Missing')->count(),
            'different' => collect($this->rows)->where('status', 'Price Different')->count(),
            'ok' => collect($this->rows)->where('status', 'OK')->count(),
        ];
    }

    public function downloadExcel()
    {
        $filename = 'ross-price-report-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {

            $handle = fopen('php://output', 'w');

            // UTF8 para Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [

                'Status',
                'MLS Property',
                'Reference Link',
                'MLS Property Status',
                'ROSS Property Status',
                'MLS Price',
                'ROSS Price',

            ]);

            foreach ($this->filteredRows as $row) {

                fputcsv($handle, [
                    $row['status'],
                    $row['title'],
                    $row['url'],
                    $row['mlsPropertyStatus'],
                    $row['rossPropertyStatus'],
                    $row['local_price'],
                    $row['external_price'],
                ]);
            }

            fclose($handle);

        }, $filename);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['Super Admin', 'Data Entry']);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['Super Admin', 'Data Entry']);
    }

}
