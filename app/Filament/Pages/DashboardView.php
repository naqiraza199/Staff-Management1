<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\Shift;
use App\Models\ShiftCancel;
use Illuminate\Support\Carbon;
use Filament\Facades\Filament;


class DashboardView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-home';
    protected static string $view = 'filament.pages.dashboard-view';
    protected static ?string $navigationLabel = 'Dashboard';

    public array $chartData = [];
    public array $shiftVarianceData = [];
    public array $shiftCancellationData = [];
    public int $todayCancelledCount = 0;

      public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('admin-panel-access');
        }


    public function mount()
    {
        $authUser = Auth::user();

        // ✅ Get company ID
        $companyId = Cache::remember("user:{$authUser->id}:company_id", now()->addMinutes(10), function () use ($authUser) {
            return Company::where('user_id', $authUser->id)->value('id');
        });

        if (!$companyId) {
            $this->chartData = [];
            $this->shiftVarianceData = [];
            $this->shiftCancellationData = [];
            return;
        }

        // ✅ Get all staff under this company
        $staffUserIds = StaffProfile::where('company_id', $companyId)
            ->where('is_archive', 'Unarchive')
            ->pluck('user_id')
            ->toArray();

        if (!in_array($authUser->id, $staffUserIds)) {
            $staffUserIds[] = $authUser->id;
        }

        $users = User::with('staffProfile')
            ->whereIn('id', $staffUserIds)
            ->get(['id', 'name']);

        // ✅ Chart 1: Utilisation
        $this->chartData = [
            'labels' => $users->pluck('name'),
            'assigned_hours' => $users->map(fn ($u) => rand(40, 70)),
            'contractual_hours' => $users->map(fn ($u) => 100),
        ];

        // ✅ Chart 2: Shift Variance
        $this->shiftVarianceData = [
            'labels' => $users->pluck('name'),
            'variance_rate' => $users->map(fn ($u) => rand(0, 10)), // Example
        ];

        // ✅ Chart 3: Shift Cancellations (Staff vs Client Totals)
        $shiftIds = Shift::where('company_id', $companyId)->pluck('id');

        $staffCount = ShiftCancel::whereIn('shift_id', $shiftIds)
            ->where('type', 'Cancelled by us')
            ->count();

        $clientCount = ShiftCancel::whereIn('shift_id', $shiftIds)
            ->where('type', 'Cancelled by clients')
            ->count();

            // ✅ Calculate today's cancellations for this company
        $today = Carbon::today();

        $shiftIds = Shift::where('company_id', $companyId)->pluck('id');

        $todayCancelledCount = ShiftCancel::whereIn('shift_id', $shiftIds)
            ->whereDate('created_at', $today)
            ->count();

        $this->todayCancelledCount = $todayCancelledCount;


        $this->shiftCancellationData = [
            'labels' => ['Staff', 'Clients'],
            'counts' => [$staffCount, $clientCount],
        ];
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }
}
