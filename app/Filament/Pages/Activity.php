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
use Filament\Facades\Filament;

class Activity extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-chart-bar';
    protected static string $view = 'filament.pages.activity';
    protected static ?string $navigationGroup = 'Reports';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasPermissionTo('see-activities');
    }

    public $clients = [];
    public $staff = [];

    // Filter properties
    public $statusFilter = 'all';
    public $startDate = null;
    public $endDate = null;

    public function mount()
    {
        // Get filter parameters from query string
        $this->statusFilter = request()->query('status', 'all');
        $startDateParam = request()->query('start_date', null);
        $endDateParam = request()->query('end_date', null);
        
        // Set default to current week if no dates provided
        if (!$startDateParam) {
            $this->startDate = now()->startOfWeek()->format('Y-m-d');
        } else {
            $this->startDate = $startDateParam;
        }
        
        if (!$endDateParam) {
            $this->endDate = now()->endOfWeek()->format('Y-m-d');
        } else {
            $this->endDate = $endDateParam;
        }

        $authUser = Auth::user();
        if (!$authUser) return;

        $companyId = Company::where('user_id', $authUser->id)->value('id');

        // ✅ Fetch Clients with Aggregates
        $clients = Client::where('user_id', $authUser->id)
            ->select('id', 'display_name as name')
            ->get();

        $this->clients = $clients->map(function ($client) {
            $reports = BillingReport::where('client_id', $client->id);
            
            // Apply date filters
            if ($this->startDate) {
                $reports = $reports->where('date', '>=', $this->startDate);
            }
            if ($this->endDate) {
                $reports = $reports->where('date', '<=', $this->endDate);
            }
            $reports = $reports->get();
            
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
            $reports = BillingReport::whereRaw("FIND_IN_SET(?, staff)", [$user->id]);
            
            // Apply date filters
            if ($this->startDate) {
                $reports = $reports->where('date', '>=', $this->startDate);
            }
            if ($this->endDate) {
                $reports = $reports->where('date', '<=', $this->endDate);
            }
            $reports = $reports->get();
            
            return $this->calculateActivityStats($reports, $user->name);
        });
    }


    /**
     * Calculate Booked, Pending, Cancelled, Absent, and Total from BillingReports
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
    ];

    foreach ($reports as $report) {
        $hours = 0;
        if (preg_match('/([\d.]+)\s*x/i', $report->hours_x_rate, $matches)) {
            $hours = (float) $matches[1];
        }
        $mileage = $report->mileage ?? 0;
        $expense = $report->expense ?? 0;

        if ($report->is_absent) {
            $stats['absent'] += $hours;
        }

        $status = Shift::where('id', $report->shift_id)->value('status');

        // Apply status filter
        $statusPass = $this->statusFilter === 'all' || $status === $this->statusFilter;

        if ($statusPass) {
            if ($status === 'Booked') {
                $stats['booked'] += $hours;
                $stats['booked_mileage'] += $mileage;
                $stats['booked_expense'] += $expense;
            } elseif ($status === 'Pending') {
                $stats['pending'] += $hours;
                $stats['pending_mileage'] += $mileage;
                $stats['pending_expense'] += $expense;
            } elseif ($status === 'Cancelled') {
                $stats['cancelled'] += $hours;
                $stats['cancelled_mileage'] += $mileage;
                $stats['cancelled_expense'] += $expense;
            }
        }
    }

    // Totals
    $stats['total'] = $stats['booked'] + $stats['pending'] + $stats['cancelled'] + $stats['absent'];
    $stats['total_mileage'] = $stats['booked_mileage'] + $stats['pending_mileage'] + $stats['cancelled_mileage'];
    $stats['total_expense'] = $stats['booked_expense'] + $stats['pending_expense'] + $stats['cancelled_expense'];

    return (object) $stats;
}

}
