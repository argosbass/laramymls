<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PropertyLastUpdatedDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static string $view = 'filament.pages.property-last-updated-dashboard';

    protected static ?string $title = 'Last Update';
    protected static ?string $navigationGroup = 'Search Tools';
}
