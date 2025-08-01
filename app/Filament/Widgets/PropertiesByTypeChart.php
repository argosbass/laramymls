<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class PropertiesByTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Properties by Type';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Property::with('type')
            ->selectRaw('property_type_id, count(*) as count')
            ->groupBy('property_type_id')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->type->type_name ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Properties',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                    ],
                ],
            ],
            'labels' => $data->pluck('type')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
