<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Card;


class DashboardStatsWidget extends BaseWidget
{
   protected static bool $isLazy = true;

    protected function getCards(): array
    {
        return [
            Card::make('New Incidents', 0)
                ->description('')
                ->descriptionIcon('heroicon-m-information-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('gray')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'x-data' => '{}',
                    'x-on:click' => '$dispatch("open-modal", { id: "incidents-details" })',
                ]),

            Card::make('Late Clock-ins', 0)
                ->description('')
                ->descriptionIcon('heroicon-m-information-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('gray')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'x-data' => '{}',
                    'x-on:click' => '$dispatch("open-modal", { id: "late-clockins-details" })',
                ]),

            Card::make('Late Clock-outs', 0)
                ->description('')
                ->descriptionIcon('heroicon-m-information-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('gray')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'x-data' => '{}',
                    'x-on:click' => '$dispatch("open-modal", { id: "late-clockouts-details" })',
                ]),

            Card::make('Forms Awaiting Review', 0)
                ->description('')
                ->descriptionIcon('heroicon-m-information-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('gray')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'x-data' => '{}',
                    'x-on:click' => '$dispatch("open-modal", { id: "forms-details" })',
                ]),
        ];
    }
} 