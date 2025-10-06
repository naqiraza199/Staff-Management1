<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DashboardView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-home';

    protected static string $view = 'filament.pages.dashboard-view';

    protected static ?string $navigationLabel = 'Dashboard';
    
    public function getTitle(): string
        {
            return 'Dashboard';
        }
}
