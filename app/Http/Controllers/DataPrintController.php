<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillingReport;

class DataPrintController extends Controller
{
    public function printTimesheet()
{
    $reports = \App\Models\BillingReport::all();

    // Calculate totals
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

    return view('billing-reports.timesheet-print', compact('reports', 'totalCost', 'totalHours'));
}

public function printDetailed()
{
    $reports = \App\Models\BillingReport::with('shift')->get();

    $totalCost = $reports->sum('total_cost');
    $totalHours = $reports->sum(function ($report) {
        if (!$report->start_time || !$report->end_time || !$report->date) return 0;
        $start = \Carbon\Carbon::parse($report->date . ' ' . $report->start_time);
        $end   = \Carbon\Carbon::parse($report->date . ' ' . $report->end_time);
        if ($end->lessThanOrEqualTo($start)) $end->addDay();
        return abs($end->diffInMinutes($start) / 60);
    });

    return view('billing-reports.timesheet-detailed', compact('reports', 'totalCost', 'totalHours'));
}

}
