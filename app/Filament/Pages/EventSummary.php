<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Company;
use App\Models\Client;
use App\Models\StaffProfile;
use App\Models\User;
use App\Models\Shift;
use App\Models\ShiftNote;

class EventSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static ?string $title = 'Reports';
    protected static string $view = 'filament.pages.event-summary';
    protected static ?string $navigationLabel = 'Event Summary';
    protected static ?string $navigationGroup = 'Reports';



    public $clients = [];
    public $staff = [];

    public function mount(): void
    {

            $this->clients = $this->getClients();
            $this->staff = $this->getStaff();
        $authUser = Auth::user();

        // 🔹 Cache company_id for performance
        $companyId = Cache::remember(
            "user:{$authUser->id}:company_id",
            now()->addMinutes(10),
            fn () => Company::where('user_id', $authUser->id)->value('id')
        );

        // --- CLIENTS ---
        $this->clients = Client::query()
            ->where('user_id', $authUser->id)
            ->select('id', 'display_name')
            ->get()
            ->map(function ($client) {
                $client->note_counts = $this->getNoteCountsForClient($client->id);
                return $client;
            });

        // --- STAFF ---
        $staffUserIds = StaffProfile::query()
            ->where('company_id', $companyId)
            ->where('is_archive', 'Unarchive')
            ->pluck('user_id')
            ->toArray();

        // Include logged-in user also
        $staffUserIds[] = $authUser->id;

        $this->staff = User::query()
            ->whereIn('id', $staffUserIds)
            ->select('id', 'name')
            ->get()
            ->map(function ($user) {
                $user->note_counts = $this->getNoteCountsForStaff($user->id);
                return $user;
            });
    }

    /**
     * 🔍 Get all Shift IDs where this client appears in client_section
     */
    protected function getShiftIdsForClient($clientId): array
    {
        $shiftIds = [];

        foreach (Shift::select('id', 'client_section')->get() as $shift) {
            $data = $shift->client_section;
            if (!is_array($data)) {
                continue;
            }

            $ids = [];

            if (isset($data['client_id'])) {
                $ids = is_array($data['client_id']) ? $data['client_id'] : [$data['client_id']];
            }

            if (!empty($data['client_details']) && is_array($data['client_details'])) {
                $ids = array_merge($ids, collect($data['client_details'])->pluck('client_id')->toArray());
            }

            if (in_array($clientId, array_map('intval', $ids))) {
                $shiftIds[] = $shift->id;
            }
        }

        return $shiftIds;
    }

    /**
     * 🔍 Get all Shift IDs where this staff user appears in carer_section
     */
    protected function getShiftIdsForStaff($userId): array
    {
        $shiftIds = [];

        foreach (Shift::select('id', 'carer_section')->get() as $shift) {
            $data = $shift->carer_section;
            if (!is_array($data)) {
                continue;
            }

            $ids = [];

            if (isset($data['user_id'])) {
                $ids = is_array($data['user_id']) ? $data['user_id'] : [$data['user_id']];
            }

            if (!empty($data['user_details']) && is_array($data['user_details'])) {
                $ids = array_merge($ids, collect($data['user_details'])->pluck('user_id')->toArray());
            }

            if (in_array($userId, array_map('intval', $ids))) {
                $shiftIds[] = $shift->id;
            }
        }

        return $shiftIds;
    }

    /**
     * 🧮 Count notes for a client
     */
    protected function getNoteCountsForClient($clientId): array
    {
        $shiftIds = $this->getShiftIdsForClient($clientId);

        if (empty($shiftIds)) {
            return $this->emptyCounts();
        }

        $counts = ShiftNote::query()
            ->whereIn('shift_id', $shiftIds)
            ->selectRaw('note_type, COUNT(*) as total')
            ->groupBy('note_type')
            ->pluck('total', 'note_type')
            ->toArray();

        return $this->buildCountsArray($counts);
    }

    /**
     * 🧮 Count notes for a staff user
     */
    protected function getNoteCountsForStaff($userId): array
    {
        $shiftIds = $this->getShiftIdsForStaff($userId);

        if (empty($shiftIds)) {
            return $this->emptyCounts();
        }

        $counts = ShiftNote::query()
            ->whereIn('shift_id', $shiftIds)
            ->selectRaw('note_type, COUNT(*) as total')
            ->groupBy('note_type')
            ->pluck('total', 'note_type')
            ->toArray();

        return $this->buildCountsArray($counts);
    }

    /**
     * Helpers
     */
    protected function emptyCounts(): array
    {
        return [
            'Injury' => 0,
            'Feedback' => 0,
            'Enquiry' => 0,
            'Incident' => 0,
            'Progress Notes' => 0,
            'Total' => 0,
        ];
    }

    protected function buildCountsArray(array $counts): array
    {
        $types = ['Injury', 'Feedback', 'Enquiry', 'Incident', 'Progress Notes'];
        $result = [];

        foreach ($types as $type) {
            $result[$type] = $counts[$type] ?? 0;
        }

        $result['Total'] = array_sum($result);
        return $result;
    }

    protected function getClients()
{
    $authUser = auth()->user();

    $companyId = \App\Models\Company::where('user_id', $authUser->id)->value('id');

    $clients = \App\Models\Client::where('user_id', $authUser->id)
        ->get();

    foreach ($clients as $client) {
        $noteCounts = \App\Models\ShiftNote::whereHas('shift', function ($q) use ($client) {
            $q->whereJsonContains('client_section->client_id', (string) $client->id)
              ->orWhereJsonContains('client_section->client_id', $client->id);
        })
        ->select('note_type', \DB::raw('count(*) as total'))
        ->groupBy('note_type')
        ->pluck('total', 'note_type');

        $client->note_counts = $noteCounts->toArray();
    }

    return $clients;
}

protected function getStaff()
{
    $authUser = auth()->user();

    $companyId = \App\Models\Company::where('user_id', $authUser->id)->value('id');

    $staffIds = \App\Models\StaffProfile::where('company_id', $companyId)
        ->where('is_archive', 'Unarchive')
        ->pluck('user_id')
        ->toArray();

    $staff = \App\Models\User::whereIn('id', $staffIds)->get();

    foreach ($staff as $user) {
        $noteCounts = \App\Models\ShiftNote::whereHas('shift', function ($q) use ($user) {
            $q->whereJsonContains('carer_section->user_id', (string) $user->id)
              ->orWhereJsonContains('carer_section->user_id', $user->id);
        })
        ->select('note_type', \DB::raw('count(*) as total'))
        ->groupBy('note_type')
        ->pluck('total', 'note_type');

        $user->note_counts = $noteCounts->toArray();
    }

    return $staff;
}
}
