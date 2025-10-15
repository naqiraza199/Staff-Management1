<?php

namespace App\Filament\Pages;

use App\Models\BillingReport;
use App\Models\Company;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Performance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-square-3-stack-3d';
    protected static string $view = 'filament.pages.performance';
    protected static ?string $navigationGroup = 'Reports';

    public $totals = [];
    public $chartData = [];
    public $billingRecords = [];

    public $selectedStatus;
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->selectedStatus = request('status');
        $this->startDate = request('start_date');
        $this->endDate = request('end_date');

        $this->loadPerformanceData();
    }

    protected function loadPerformanceData()
    {
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');

        $this->totals = [
            'Invoiced' => $this->getTotalByStatus('Invoiced', $companyId),
            'Booked'   => $this->getTotalByStatus('Booked', $companyId),
            'Cancelled'=> $this->getTotalByStatus('Cancelled', $companyId),
            'Pending'  => $this->getTotalByStatus('Pending', $companyId),
        ];

        $this->chartData = $this->getChartData($companyId);
        $this->billingRecords = $this->getBillingRecords($companyId);
    }

    protected function getTotalByStatus($status, $companyId)
    {
        $query = BillingReport::whereHas('shift', function ($q) use ($status, $companyId) {
            $q->where('status', $status)
              ->where('company_id', $companyId);
        });

        $this->applyFilters($query);

        return $query->sum('total_cost');
    }

    protected function getChartData($companyId)
    {
        $query = DB::table('billing_reports')
            ->join('shifts', 'billing_reports.shift_id', '=', 'shifts.id')
            ->selectRaw('DATE(billing_reports.date) as date, shifts.status, SUM(billing_reports.total_cost) as total')
            ->where('shifts.company_id', $companyId)
            ->groupByRaw('DATE(billing_reports.date), shifts.status')
            ->orderByRaw('DATE(billing_reports.date) asc');

        $this->applyFilters($query);

        $records = $query->get();

        $dates = $records->pluck('date')->unique()->values();
        $statuses = ['Invoiced', 'Booked', 'Cancelled', 'Pending'];
        $data = [];

        foreach ($statuses as $status) {
            $data[$status] = [];
            foreach ($dates as $date) {
                $record = $records->first(fn($r) => $r->date === $date && $r->status === $status);
                $data[$status][] = $record ? (float) $record->total : 0;
            }
        }

        return [
            'labels' => $dates,
            'datasets' => $data,
        ];
    }

    protected function getBillingRecords($companyId)
    {
        $query = DB::table('billing_reports')
            ->join('shifts', 'billing_reports.shift_id', '=', 'shifts.id')
            ->leftJoin('clients', 'billing_reports.client_id', '=', 'clients.id')
            ->select(
                'billing_reports.date',
                'billing_reports.staff as shift_name',
                'clients.display_name as client_name',
                'shifts.status'
            )
            ->where('shifts.company_id', $companyId)
            ->orderBy('billing_reports.date', 'desc');

        $this->applyFilters($query);

        return $query->get();
    }

protected function applyFilters($query)
{
    $isJoined = method_exists($query, 'joins') || str_contains(strtolower((string) $query->toSql()), 'join');

    if ($this->selectedStatus && $this->selectedStatus !== 'Filter by Status...') {
        if ($isJoined) {
            $query->where('shifts.status', $this->selectedStatus);
        } else {
            $query->whereHas('shift', function ($q) {
                $q->where('status', $this->selectedStatus);
            });
        }
    }

    if ($this->startDate && $this->endDate) {
        $query->whereBetween('billing_reports.date', [$this->startDate, $this->endDate]);
    }

    return $query;
}

}
