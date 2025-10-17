@php
    use App\Models\Team;

    $client = $getRecord();
    $teams = Team::whereJsonContains('clients', (string) $client->id)->get();
@endphp

@if ($teams->isEmpty())
    <div class="p-4 text-gray-500 text-sm">
        No teams are assigned to this client.
    </div>
@else
    <div class="overflow-x-auto">
        <table class="data-table w-full text-sm border-collapse">
            <thead>
                <tr>
                    <th class="p-2 text-left">Team Name</th>
                    <th class="p-2 text-left">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teams as $team)
                    <tr class="border-b hover:bg-gray-50 cursor-pointer"
                        onclick="window.location.href='{{ route('filament.admin.resources.teams.edit', ['record' => $team->id]) }}'">
                        <td class="p-2 text-blue-600 hover:underline">{{ $team->name }}</td>
                        <td class="p-2">{{ $team->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
