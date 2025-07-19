<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PropertySearchDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';
    protected static string $view = 'filament.pages.property-search-dashboard';

    protected static ?string $title = 'MLS Search';
    protected static ?string $navigationGroup = 'Search Tools';
}
