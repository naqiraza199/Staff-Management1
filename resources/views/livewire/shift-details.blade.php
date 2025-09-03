<div>
    @if($shift)
        <p><strong>Staff:</strong> {{ $userName }}</p>

        @if($selectedDate)
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</p>
        @endif

        <p><strong>Time:</strong>
           {{ $timeset }}
        </p>

        <p><strong>Shift Type:</strong> {{ $shiftTypeName }}</p>
        <p><strong>Client:</strong> {{ $clientName }}</p>

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
