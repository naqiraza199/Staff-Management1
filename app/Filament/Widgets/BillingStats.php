<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\BillingReport;
use Carbon\Carbon;

class BillingStats extends BaseWidget
{
    protected static ?int $sort = 1; 

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $clientId = request()->query('client_id'); // always read from URL

        $reports = BillingReport::query()
            ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
            ->get();

        $totalCost = $reports->sum('total_cost');

        $totalHours = $reports->sum(function ($report) {
            if (!$report->start_time || !$report->end_time || !$report->date) {
                return 0;
            }

            $start = \Carbon\Carbon::parse($report->date . ' ' . $report->start_time);
            $end   = \Carbon\Carbon::parse($report->date . ' ' . $report->end_time);

            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            return abs($end->diffInMinutes($start) / 60);
        });

        return [
            Stat::make('Total Cost', '$' . number_format($totalCost, 2))
                ->description('Total billing generated so far')
                ->descriptionIcon('heroicon-s-currency-dollar')
                ->chart([1, 0, 0, 0, 0, 0, 1])
                ->color('success'),

            Stat::make('Total Hours', number_format($totalHours, 2) . ' hrs')
                ->description('Sum of worked hours across all shifts')
                ->descriptionIcon('heroicon-s-clock')
                ->chart([1, 0, 0, 0, 0, 0, 1])
                ->color('primary'),
        ];
    }

}
