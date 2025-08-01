<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PropertiesStatsWidget;
use App\Filament\Widgets\PropertiesByTypeChart;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\QuickSearchWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            PropertiesStatsWidget::class,
            PropertiesByTypeChart::class,
            QuickSearchWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }

    public function getWidgetData(): array
    {
        return [
            'AccountWidget' => [
                'columnSpan' => 'full', // Hacer que el Welcome ocupe todo el ancho
            ],
        ];
    }
}
