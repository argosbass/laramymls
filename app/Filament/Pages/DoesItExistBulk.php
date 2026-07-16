<?php

namespace App\Filament\Pages;
use App\Models\PropertyListingCompetitor;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Filament\Pages\Page;

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

    public function search(): void
    {
        $urlList = collect(preg_split('/\r\n|\r|\n/', $this->urls))
            ->map(function ($url) {

                // Elimina caracteres invisibles (BOM, etc.)
                $url = preg_replace('/[\x00-\x1F\x7F\x{FEFF}]/u', '', $url);

                // Quita espacios al inicio y final
                $url = trim($url);

                // Quita comillas simples y dobles
                $url = trim($url, "\"'");

                // Elimina espacios internos accidentales
                $url = preg_replace('/\s+/', '', $url);

                // Quita el slash final
                $url = rtrim($url, '/');

                return $url;
            })
            ->filter(fn ($url) => filter_var($url, FILTER_VALIDATE_URL))
            ->unique()
            ->values();

        $competitors = PropertyListingCompetitor::query()
            ->with([
                'company',
                'property.status',
            ])
            ->whereIn('competitor_property_link', $urlList)
            ->get()
            ->keyBy('competitor_property_link');

        $this->results = $urlList
            ->map(function ($url) use ($competitors) {

                $item = $competitors->get($url);

                return [
                    'exists' => $item !== null,

                    'company' => $item?->company?->company_name,

                    'property_title' => $item?->property?->property_title,

                    'added_date' => $item?->property?->property_added_date
                        ? \Carbon\Carbon::parse($item->property->property_added_date)->format('Y-m-d')
                        : null,

                    'status' => $item?->property?->status?->status_name,

                    'reference_link' => $url,

                    'view_url' => $item?->property
                        ? route('filament.admin.resources.properties.view', [
                            'record' => $item->property->id,
                        ])
                        : null,

                    'edit_url' => $item?->property
                        ? route('filament.admin.resources.properties.edit', [
                            'record' => $item->property->id,
                        ])
                        : null,
                ];
            })
            ->sortByDesc('exists') // Los encontrados primero, los Not Found al final
            ->values()
            ->toArray();
    }

    public function downloadCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Listing Company',
                'Property Title',
                'Added Date',
                'Property Status',
                'Reference Link',
                'Exists',
            ]);

            foreach ($this->results as $row) {
                fputcsv($handle, [
                    $row['company'] ?? '',
                    $row['property_title'] ?? '',
                    $row['added_date'] ?? '',
                    $row['status'] ?? '',
                    $row['reference_link'] ?? '',
                    $row['exists'] ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, 'does-it-exist-bulk-results.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }


}
