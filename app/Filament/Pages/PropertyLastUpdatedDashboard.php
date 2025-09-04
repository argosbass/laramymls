<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PropertyLastUpdatedDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static string $view = 'filament.pages.property-last-updated-dashboard';

    protected static ?string $title = 'Last Update';
    protected static ?string $navigationGroup = 'Search Tools';

    public static function canViewAny(): bool
    {
       // return auth()->user()?->hasAnyRole(['Super Admin', 'Data Entry']);
        return auth()->user()?->hasAnyRole(['Super Admin']);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('Super Admin');
    }
}
