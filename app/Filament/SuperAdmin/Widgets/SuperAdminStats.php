<?php

namespace App\Filament\SuperAdmin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Company;

class SuperAdminStats extends BaseWidget
{
      protected static ?int $sort = 1; 

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

     protected int|string|array $columnSpan = 'full';

  protected function getStats(): array
    {
        $totalUsers = User::count();
        $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        $totalCompanies = Company::count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->icon('heroicon-s-users')
                ->description('All users in system')
                ->descriptionIcon('heroicon-s-user-group')
                ->color('primary')
                ->chart([10, 20, 15, 30, 25, 40, 35]),

            Stat::make('New Users (30d)', $recentUsers)
                ->icon('heroicon-s-user')
                ->description("Users in last 30 days")
                ->descriptionIcon('heroicon-s-arrow-trending-up')
                ->color('success')
                ->chart([2, 5, 3, 7, 4, 8, 6]),

            Stat::make('Total Companies', $totalCompanies)
                ->icon('heroicon-s-building-office-2')
                ->description('Registered companies')
                ->descriptionIcon('heroicon-s-building-office')
                ->color('warning')
                ->chart([5, 8, 6, 10, 9, 12, 11]),
        ];
    }
}
