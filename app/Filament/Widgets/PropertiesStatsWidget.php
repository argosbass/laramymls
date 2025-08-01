<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertiesStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Properties', Property::count())
                ->description('All properties in system')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),

            Stat::make('Available Properties', Property::where('property_status_id', 13)->count())
                ->description('Currently Available')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),

            Stat::make('Sold Properties', Property::where('property_status_id', 15)->count())
                ->description('Properties sold')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning'),

            Stat::make('Avg Price', '$' . number_format(Property::avg('property_price')))
                ->description('Average property price')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}
