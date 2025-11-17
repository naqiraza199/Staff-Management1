@php
use App\Models\User;

    $staff = $getRecord();
    $user = User::where('id',$staff->id)->first();
@endphp

<style>
    .sapace {
  display: flex;
  justify-content: space-between;
}
</style>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
    <!-- NDIS Number -->
 <div class="sapace">
       <div>
        <span class="data-label">Status</span>
    </div>
     <div class="flex items-center justify-between mt-1">
            <span style="padding: 1px 9px;font-size: 10px;" class="status-badge">{{ $user->status }}</span>
            <button class="text-gray-400 hover:text-gray-600" title="Copy">
                <i class="lucide-copy"></i>
            </button>
        </div>
 </div>

 <div class="sapace">
       <div>
        <span class="data-label">Role</span>
    </div>
     <div class="flex items-center justify-between mt-1">
            <span style="padding: 1px 9px;font-size: 10px;" class="status-badge-yellow">
                {{ $user->roles->first()?->name ?? '-' }}
            </span>
            <button class="text-gray-400 hover:text-gray-600" title="Copy">
                <i class="lucide-copy"></i>
            </button>
        </div>
 </div>

  <div class="sapace">
       <div>
        <span class="data-label">Job Title</span>
    </div>
     <div class="flex items-center justify-between mt-1">
            <span style="padding: 1px 9px;font-size: 10px;" class="status-badge-green">
                {{ $user->jobTitle->name ?? '-' }}
            </span>
            <button class="text-gray-400 hover:text-gray-600" title="Copy">
                <i class="lucide-copy"></i>
            </button>
        </div>
 </div>


            @php
            use Carbon\Carbon;

            $user = \App\Models\User::with('teams')->find($user->id);
            $teams = $user?->teams ?? collect();
            @endphp
        <span class="data-label">Teams</span>

            <div class="overflow-x-auto">
                <table class="data-table w-full text-sm border-collapse">
                    <thead>
                        <tr>
                            <th>Teams Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $team)
                            <tr 
                                onclick="window.location.href='{{ route('filament.admin.resources.teams.edit', ['record' => $team->id]) }}'" 
                                class="border-b hover:bg-gray-50 cursor-pointer transition"
                                style="font-size: 13px;"
                            >
                                <td data-label="Team Name">{{ $team->name }}</td>
                            </tr>
                        @empty
                            <tr 
                                onclick="window.location.href='{{ route('filament.admin.resources.teams.create') }}'" 
                                class="text-center text-gray-500 py-3 cursor-pointer hover:bg-gray-50 transition"
                            >
                                <td colspan="2">No teams found — Click here to create one</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


</div>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
