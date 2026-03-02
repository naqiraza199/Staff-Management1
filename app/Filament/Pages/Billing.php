<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Company;
use App\Models\Client;
use App\Models\User;
use App\Models\Shift;
use App\Models\BillingReport;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Filament\Facades\Filament;

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-banknotes';

    protected static string $view = 'filament.pages.billing';

    protected static ?string $navigationGroup = 'Reports';

    public $clients = [];
    public $staff = [];
    public $filterStartDate = '';
    public $filterEndDate = '';
    public $filterStatus = 'all';
    public $chartLabels = [];

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('see-billings');
    }

    public function mount()
    {
        $authUser = Auth::user();
        if (!$authUser) return;

        $companyId = Company::where('user_id', $authUser->id)->value('id');

        // Get filter parameters from request
        $this->filterStartDate = request('start_date', '');
        $this->filterEndDate = request('end_date', '');
        $this->filterStatus = request('status', 'all');

        // If no dates provided, default to current week
        if (empty($this->filterStartDate) || empty($this->filterEndDate)) {
            $now = Carbon::now();
            $this->filterStartDate = $now->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
            $this->filterEndDate = $now->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
        }

        // Generate chart labels for the date range
        $this->chartLabels = $this->generateChartLabels($this->filterStartDate, $this->filterEndDate);

        // ✅ Fetch Clients with Aggregates
        $clients = Client::where('user_id', $authUser->id)
            ->select('id', 'display_name as name')
            ->get();

        $this->clients = $clients->map(function ($client) {
            $reports = BillingReport::where('client_id', $client->id)->get();
            return $this->calculateActivityStats($reports, $client->name);
        });

        // ✅ Fetch Staff (including the logged-in user)
        $staffIds = StaffProfile::where('company_id', $companyId)
            ->where('is_archive', 'Unarchive')
            ->pluck('user_id')
            ->toArray();

        // ➕ Add current user if missing
        if (!in_array($authUser->id, $staffIds)) {
            $staffIds[] = $authUser->id;
        }

        // ✅ Fetch all staff user records (unique)
        $staffMembers = User::whereIn('id', array_unique($staffIds))
            ->select('id', 'name')
            ->get();

        $this->staff = $staffMembers->map(function ($user) {
            $reports = BillingReport::whereRaw("FIND_IN_SET(?, staff)", [$user->id])->get();
            return $this->calculateActivityStats($reports, $user->name);
        });
    }

    /**
     * Generate chart labels from date range
     */
    private function generateChartLabels($startDate, $endDate)
    {
        $labels = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dayName = $current->format('D');
            $dayNum = $current->format('j');
            $monthName = $current->format('M');
            $labels[] = "{$dayName}, {$dayNum} {$monthName}";
            $current->addDay();
        }

        return $labels;
    }

    /**
     * Calculate Booked, Pending, Cancelled, Absent, and Total from BillingReports
     * Now filters by date range and status
     */
    private function calculateActivityStats($reports, $name)
    {
        $stats = [
            'name' => $name,
            'booked' => 0,
            'pending' => 0,
            'cancelled' => 0,
            'absent' => 0,
            'total' => 0,
            'booked_mileage' => 0,
            'pending_mileage' => 0,
            'cancelled_mileage' => 0,
            'total_mileage' => 0,
            'booked_expense' => 0,
            'pending_expense' => 0,
            'cancelled_expense' => 0,
            'total_expense' => 0,
            'dates' => [], // Store dates for filtering
        ];

        // Get filter values
        $startDate = $this->filterStartDate;
        $endDate = $this->filterEndDate;
        $statusFilter = $this->filterStatus;

        foreach ($reports as $report) {
            // Get shift and its date
            $shift = Shift::where('id', $report->shift_id)->first();
            
            if (!$shift) continue;

            // Get shift date from time_and_location JSON
            $shiftDate = data_get($shift->time_and_location, 'start_date');
            
            if (!$shiftDate) continue;

            // Store the date
            $stats['dates'][] = $shiftDate;

            // Apply date filter
            if ($startDate && $endDate) {
                $shiftCarbon = Carbon::parse($shiftDate);
                $startCarbon = Carbon::parse($startDate)->startOfDay();
                $endCarbon = Carbon::parse($endDate)->endOfDay();

                if ($shiftCarbon->lt($startCarbon) || $shiftCarbon->gt($endCarbon)) {
                    continue; // Skip this report if outside date range
                }
            }

            // Use total_cost instead of hours_x_rate
            $totalCost = (float) ($report->total_cost ?? 0);
            $mileage = (float) ($report->mileage ?? 0);
            $expense = (float) ($report->expense ?? 0);

            // Mark absent (optional: depends on your business logic)
            if ($report->is_absent) {
                $stats['absent'] += $totalCost;
            }

            // Get shift status
            $status = $shift->status;

            // Apply status filter
            if ($statusFilter !== 'all' && $status !== $statusFilter) {
                // Don't skip - still count for totals but mark in a way JS can filter
            }

            if ($status === 'Booked') {
                $stats['booked'] += $totalCost;
                $stats['booked_mileage'] += $mileage;
                $stats['booked_expense'] += $expense;
            } elseif ($status === 'Pending') {
                $stats['pending'] += $totalCost;
                $stats['pending_mileage'] += $mileage;
                $stats['pending_expense'] += $expense;
            } elseif ($status === 'Cancelled') {
                $stats['cancelled'] += $totalCost;
                $stats['cancelled_mileage'] += $mileage;
                $stats['cancelled_expense'] += $expense;
            }
        }

        // Apply status filter to totals (if specific status selected)
        if ($statusFilter !== 'all') {
            // Reset and only count the filtered status
            $filteredStats = [
                'booked' => 0,
                'pending' => 0,
                'cancelled' => 0,
                'booked_mileage' => 0,
                'pending_mileage' => 0,
                'cancelled_mileage' => 0,
                'booked_expense' => 0,
                'pending_expense' => 0,
                'cancelled_expense' => 0,
            ];

            foreach ($reports as $report) {
                $shift = Shift::where('id', $report->shift_id)->first();
                if (!$shift) continue;

                $shiftDate = data_get($shift->time_and_location, 'start_date');
                if (!$shiftDate) continue;

                // Apply date filter
                if ($startDate && $endDate) {
                    $shiftCarbon = Carbon::parse($shiftDate);
                    $startCarbon = Carbon::parse($startDate)->startOfDay();
                    $endCarbon = Carbon::parse($endDate)->endOfDay();

                    if ($shiftCarbon->lt($startCarbon) || $shiftCarbon->gt($endCarbon)) {
                        continue;
                    }
                }

                $status = $shift->status;
                $totalCost = (float) ($report->total_cost ?? 0);
                $mileage = (float) ($report->mileage ?? 0);
                $expense = (float) ($report->expense ?? 0);

                if ($status === $statusFilter) {
                    if ($statusFilter === 'Booked') {
                        $filteredStats['booked'] += $totalCost;
                        $filteredStats['booked_mileage'] += $mileage;
                        $filteredStats['booked_expense'] += $expense;
                    } elseif ($statusFilter === 'Pending') {
                        $filteredStats['pending'] += $totalCost;
                        $filteredStats['pending_mileage'] += $mileage;
                        $filteredStats['pending_expense'] += $expense;
                    } elseif ($statusFilter === 'Cancelled') {
                        $filteredStats['cancelled'] += $totalCost;
                        $filteredStats['cancelled_mileage'] += $mileage;
                        $filteredStats['cancelled_expense'] += $expense;
                    }
                }
            }

            // Apply filtered values
            $stats['booked'] = $filteredStats['booked'];
            $stats['pending'] = $filteredStats['pending'];
            $stats['cancelled'] = $filteredStats['cancelled'];
            $stats['booked_mileage'] = $filteredStats['booked_mileage'];
            $stats['pending_mileage'] = $filteredStats['pending_mileage'];
            $stats['cancelled_mileage'] = $filteredStats['cancelled_mileage'];
            $stats['booked_expense'] = $filteredStats['booked_expense'];
            $stats['pending_expense'] = $filteredStats['pending_expense'];
            $stats['cancelled_expense'] = $filteredStats['cancelled_expense'];
        }

        // Totals
        $stats['total'] = $stats['booked'] + $stats['pending'] + $stats['cancelled'] + $stats['absent'];
        $stats['total_mileage'] = $stats['booked_mileage'] + $stats['pending_mileage'] + $stats['cancelled_mileage'];
        $stats['total_expense'] = $stats['booked_expense'] + $stats['pending_expense'] + $stats['cancelled_expense'];

        return (object) $stats;
    }
}
