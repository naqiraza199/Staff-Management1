<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\BillingReport;
use Carbon\Carbon;

class BillingReportsStats extends Widget
{
    protected static string $view = 'filament.widgets.billing-reports-stats';

      protected static ?int $sort = 1; 

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

     protected int|string|array $columnSpan = 'full';

    public function getData(): array
    {
        $clientId = request()->query('client_id');

        $reports = BillingReport::query()
            ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
            ->get();

        $totalCost = $reports->sum('total_cost');

        $totalHours = $reports->sum(function ($report) {
            if (!$report->start_time || !$report->end_time || !$report->date) {
                return 0;
            }

            $start = Carbon::parse($report->date . ' ' . $report->start_time);
            $end   = Carbon::parse($report->date . ' ' . $report->end_time);

            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            return abs($end->diffInMinutes($start) / 60);
        });

        return [
            'totalCost' => $totalCost,
            'totalHours' => $totalHours,
        ];
    }
}
