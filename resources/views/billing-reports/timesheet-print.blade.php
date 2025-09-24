<!DOCTYPE html>
<html>
<head>
    <title>Timesheet Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 14px; }
        th { background: #f4f4f4; }
        h2 { margin-bottom: 5px; }
        .summary { margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body onload="window.print()">


    <h2>Consolidated Timesheet Report</h2>

    <div class="summary">
        Total Hours: {{ number_format($totalHours, 2) }} <br>
        Total Cost: ${{ number_format($totalCost, 2) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Shift</th>
                <th>Staff</th>
                <th>Start Time</th>
                <th>Finish Time</th>
                <th>Hours × Rate</th>
                <th>Additional Cost</th>
                <th>Distance × Rate</th>
                <th>Total Cost</th>
                <th>Running Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                @php
                    $shift = \App\Models\Shift::find($report->shift_id);

                    // Shift text
                    if ($shift) {
                        $clientSection = is_string($shift->client_section) ? json_decode($shift->client_section, true) : $shift->client_section;
                        $timeAndLocation = is_string($shift->time_and_location) ? json_decode($shift->time_and_location, true) : $shift->time_and_location;
                        $shiftSection = is_string($shift->shift_section) ? json_decode($shift->shift_section, true) : $shift->shift_section;

                        if (!$shift->is_advanced_shift) {
                            $clientName = \App\Models\Client::find($clientSection['client_id'] ?? null)->display_name ?? 'Unknown Client';
                            $priceBookName = \App\Models\PriceBook::find($clientSection['price_book_id'] ?? null)->name ?? 'Unknown Price Book';
                            $start = !empty($timeAndLocation['start_time']) ? \Carbon\Carbon::parse($timeAndLocation['start_time'])->format('h:i a') : '';
                            $end = !empty($timeAndLocation['end_time']) ? \Carbon\Carbon::parse($timeAndLocation['end_time'])->format('h:i a') : '';
                            $shiftText = "{$clientName} - {$priceBookName} | {$start} - {$end}";
                        } else {
                            $clientDetails = $clientSection['client_details'][0] ?? null;
                            if (!$clientDetails) {
                                $shiftText = 'Advanced Shift';
                            } else {
                                $clientName = $clientDetails['client_name'] ?? 'Unknown Client';
                                $ratio = $clientDetails['hours'] ?? '';
                                $priceBookName = \App\Models\PriceBook::find($clientDetails['price_book_id'] ?? null)->name ?? 'Unknown Price Book';
                                $shiftText = "{$clientName} - {$ratio} - {$priceBookName}";
                            }
                        }
                    } else {
                        $shiftText = 'N/A';
                    }

                    // Staff text
                    if ($shift) {
                        $carerSection = is_string($shift->carer_section) ? json_decode($shift->carer_section, true) : $shift->carer_section;
                        if (!$shift->is_advanced_shift) {
                            $userId = $carerSection['user_id'] ?? null;
                            $staffText = \App\Models\User::find($userId)->name ?? 'Unknown Staff';
                        } else {
                            $userDetails = $carerSection['user_details'] ?? [];
                            $names = collect($userDetails)->pluck('user_name')->filter()->implode(', ');
                            $staffText = $names ?: 'Advanced Staff';
                        }
                    } else {
                        $staffText = 'N/A';
                    }

                    // Format start & end times
                    $startTime = $report->start_time && $report->date
                        ? \Carbon\Carbon::parse($report->date . ' ' . $report->start_time)->format('h:i a (d/m/Y)')
                        : null;

                    $endDate = $report->date ? \Carbon\Carbon::parse($report->date) : null;
                    if ($shift && !empty($timeAndLocation['shift_finishes_next_day'] ?? false)) {
                        $endDate?->addDay();
                    }
                    $endTime = $report->end_time && $endDate
                        ? \Carbon\Carbon::parse($endDate->format('Y-m-d') . ' ' . $report->end_time)->format('h:i a (d/m/Y)')
                        : null;
                @endphp
            
                <tr>
                    <td>{{ \Carbon\Carbon::parse($report->date)->format('D, d M Y') }}</td>
                    <td>{!! $shiftText !!}</td>
                    <td>{{ $staffText }}</td>
                    <td>{{ $startTime }}</td>
                    <td>{{ $endTime }}</td>
                    <td>{{ $report->hours_x_rate }}</td>
                    <td>${{ number_format($report->additional_cost, 2) }}</td>
                    <td>{{ $report->distance_x_rate }}</td>
                    <td>${{ number_format($report->total_cost, 2) }}</td>
                    <td>${{ number_format($report->running_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
