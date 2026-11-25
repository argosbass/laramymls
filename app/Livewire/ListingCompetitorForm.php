<?php

namespace App\Livewire;

use App\Models\PropertyListingCompetitor;
use App\Models\PropertyStatus;
use App\Models\RealEstateCompany;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListingCompetitorForm extends Component
{
    use WithPagination;

    public $companyId = '';
    public $statusId = '';
    public $referenceLink = '';

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public function search(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->companyId = '';
        $this->statusId = '';
        $this->referenceLink = '';

        $this->resetPage();
    }

    /**
     * Normaliza una URL para comparar ignorando:
     *
     * - http:// y https://
     * - www.
     * - slash final
     * - mayúsculas y minúsculas
     */
    private function normalizeUrl(?string $url): string
    {
        if (blank($url)) {
            return '';
        }

        // Elimina caracteres invisibles.
        $url = preg_replace(
            '/[\x00-\x1F\x7F\x{00A0}\x{FEFF}]/u',
            '',
            $url
        );

        // Quita espacios, comillas y espacios internos accidentales.
        $url = trim($url);
        $url = trim($url, "\"'");
        $url = preg_replace('/\s+/', '', $url);

        // Quita el protocolo.
        $url = preg_replace('#^https?://#i', '', $url);

        // Quita www.
        $url = preg_replace('#^www\.#i', '', $url);

        // Quita slash final.
        $url = rtrim($url, '/');

        return strtolower($url);
    }

    public function downloadExcel(): StreamedResponse
    {
        abort_if(auth()->user()?->hasRole('Agent'), 403);

        $filename ='does-it-exist-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // BOM para que Excel reconozca UTF-8.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Listing Company',
                'Property Title',
                'Added Date',
                'Property Status',
                'Reference Link',
            ]);

            $this->buildQuery()->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $competitor) {
                    $added = $competitor->property?->property_added_date;

                    fputcsv($handle, [
                        $competitor->company->company_name ?? '',
                        $competitor->property->property_title ?? '',
                        $added ? Carbon::parse($added)->format('Y-m-d') : '',
                        $competitor->property?->status?->status_name ?? '',
                        $competitor->competitor_property_link ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildQuery()
    {
        $normalizedReferenceLink = $this->normalizeUrl(
            $this->referenceLink
        );

        return PropertyListingCompetitor::query()
            ->with([
                'company',
                'property.status',
            ])

            ->when(
                filled($this->companyId),
                fn ($query) => $query->where(
                    'property_listing_competitors.real_estate_company_id',
                    $this->companyId
                )
            )

            ->when(
                filled($this->statusId),
                fn ($query) => $query->whereHas(
                    'property',
                    function ($propertyQuery) {
                        $propertyQuery->where(
                            'property_status_id',
                            $this->statusId
                        );
                    }
                )
            )

            ->when(
                $normalizedReferenceLink !== '',
                fn ($query) => $query->whereRaw(
                    "
                    TRIM(TRAILING '/' FROM
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    LOWER(
                                        property_listing_competitors.competitor_property_link
                                    ),
                                    'https://',
                                    ''
                                ),
                                'http://',
                                ''
                            ),
                            'www.',
                            ''
                        )
                    ) LIKE ?
                    ",
                    ['%' . $normalizedReferenceLink . '%']
                )
            )

            ->join(
                'real_estate_companies',
                'property_listing_competitors.real_estate_company_id',
                '=',
                'real_estate_companies.id'
            )

            ->orderBy('real_estate_companies.company_name')
            ->orderBy('property_listing_competitors.id')

            ->select('property_listing_competitors.*');
    }

    public function render()
    {
        $companies = RealEstateCompany::query()
            ->orderBy('company_name')
            ->get();

        $statuses = PropertyStatus::query()
            ->orderBy('status_name')
            ->get();

        $results = $this->buildQuery()->paginate(
            100,
            pageName: 'page'
        );

        return view(
            'livewire.listing-competitor-form',
            compact(
                'results',
                'companies',
                'statuses'
            )
        );
    }
}
