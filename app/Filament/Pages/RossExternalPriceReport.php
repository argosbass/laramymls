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

    public function mount(): void
    {
        $jsonUrl = 'https://www.remax-oceansurf-cr.com/json-properties';

        $items = Http::withHeaders([
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
            ])->get($jsonUrl . '?v=' . now()->timestamp)->json() ?? [];

        foreach ($items['nodes'] as $nodes) {

            $item = $nodes['node'];

            $url = trim($item['Path'] ?? '');
            $url = "https://www.remax-oceansurf-cr.com".$url;

            $externalPrice          = (float) ($item['Price'] ?? 0);
            $externalPropertyStatus = ($item['PropertyStatus'] ?? null);

            $property = Property::with([
                'listingCompetitors' => function ($query) use ($url) {
                    $query->where('competitor_property_link', $url);
                }
            ])
            ->whereHas('listingCompetitors', function ($query) use ($url) {
                $query->where('competitor_property_link', $url);
            })
            ->first();



            if (! $property) {

                $this->rows[] = [
                    'id' => md5($url),
                    'title' => '',
                    'url' => $url,
                    'local_price' => null,
                    'external_price' => $externalPrice,
                    'status' => 'Missing',
                    'rossPropertyStatus' => $externalPropertyStatus,
                    'mlsPropertyStatus' => null
                ];


                continue;
            }


            $localPrice = (float) $property->property_price;
            $localPropertyStatus = $property->status?->status_name ?? '';



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
                'mlsPropertyStatus' => $localPropertyStatus

            ];
        }


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
