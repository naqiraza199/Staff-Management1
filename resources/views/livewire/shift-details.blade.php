<div>
  @if($shift)
    <!-- Existing details ... -->

    <p><strong>Shift ID:</strong> {{ $shift->id }}</p>

    <div class="mt-4">
        <a href="{{ route('filament.admin.pages.advanced-shift-form', ['shiftId' => $shift->id]) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit Shift
        </a>
    </div>

                <p><strong>Staff:</strong> {{ is_array($userName) ? implode(', ', $userName) : $userName }}</p>


        @if($selectedDate)
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</p>
        @endif

        <p><strong>Time:</strong>
           {{ $timeset }}
        </p>


        <p><strong>Clients:</strong> {{ is_array($clientName) ? implode(', ', $clientName) : $clientName }}</p>



        @php
            $recurrence = $shift->time_and_location['recurrance'] ?? null;
            $weeklyDays = $shift->time_and_location['occurs_on_weekly'] ?? [];
            $monthlyDay = $shift->time_and_location['occurs_on_monthly'] ?? null;
        @endphp

        <p><strong>Recurrence:</strong> {{ $recurrence ?? 'None' }}</p>

        @if($recurrence === 'Weekly' && !empty($weeklyDays))
            <p><strong>Weekly Days:</strong>
                {{ collect($weeklyDays)->filter()->keys()->map(fn($day) => ucfirst($day))->implode(', ') }}
            </p>
        @endif

        @if($recurrence === 'Monthly' && $monthlyDay)
            <p><strong>Day of Month:</strong> {{ $monthlyDay }}</p>
        @endif

        <p><strong>Start Date:</strong> {{ $startDateFormatted }}</p>
        <p><strong>End Date:</strong>
            {{ $endDateFormatted }}
        </p>

        <p><strong>Shift ID:</strong> {{ $shift->id }}</p>
    @else
        <p>No shift selected.</p>
    @endif
</div>
