<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ListingCompetitorDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static string $view = 'filament.pages.listing-competitor-dashboard';

    protected static ?string $title = 'Does it exists';
    protected static ?string $navigationGroup = 'Search Tools';
}
