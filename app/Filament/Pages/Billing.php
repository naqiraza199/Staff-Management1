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

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-banknotes';

    protected static string $view = 'filament.pages.billing';

       protected static ?string $navigationGroup = 'Reports';

    public $clients = [];
    public $staff = [];

   public function mount()
{
    $authUser = Auth::user();
    if (!$authUser) return;

    $companyId = Company::where('user_id', $authUser->id)->value('id');

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
    // Use total_cost instead of hours_x_rate
    $totalCost = (float) ($report->total_cost ?? 0);
    $mileage = (float) ($report->mileage ?? 0);
    $expense = (float) ($report->expense ?? 0);

    // Mark absent (optional: depends on your business logic)
    if ($report->is_absent) {
        $stats['absent'] += $totalCost;
    }

    // Get shift status
    $status = Shift::where('id', $report->shift_id)->value('status');

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


    // Totals
    $stats['total'] = $stats['booked'] + $stats['pending'] + $stats['cancelled'] + $stats['absent'];
    $stats['total_mileage'] = $stats['booked_mileage'] + $stats['pending_mileage'] + $stats['cancelled_mileage'];
    $stats['total_expense'] = $stats['booked_expense'] + $stats['pending_expense'] + $stats['cancelled_expense'];

    return (object) $stats;
}
}
