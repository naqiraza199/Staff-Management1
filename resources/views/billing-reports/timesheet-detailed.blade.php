<!DOCTYPE html>
<html>
<head>
    <title>Detailed Billing Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f4f4f4; }
        h1 { text-align: center; margin-bottom: 20px; }
        .attachment img {
            max-width: 100px;
            border: 1px #dfdfdf groove;
            border-radius: 10px;
            margin: 5px 0;
            height: 69px;
        }
        ul { margin: 0; padding-left: 18px; }
        .flwx{
            display:flex;
        }
    </style>
</head>
<body onload="window.print()">
     

    <h2>{{ $clientCheck->display_name }} Timesheet Report</h2>


    {{-- Totals --}}
    <p><strong>Total Hours:</strong> {{ $totalHours }}</p>
    <p><strong>Total Cost:</strong> ${{ number_format($totalCost, 2) }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Shift</th>
                <th>Staff</th>
                <th>Start Time</th>
                <th>Finish Time</th>
                <th>Hours x Rate</th>
                <th>Additional Cost</th>
                <th>Distance x Rate</th>
                <th>Total Cost</th>
                <th>Running Cost</th>
                <th>Notes</th>
                <th>Tasks</th>
                <th>Client Signature</th>
            </tr>
        </thead>
        <tbody>
             @foreach($reports as $report)
                @php
                    $shift = \App\Models\Shift::find($report->shift_id);
                    $note = \App\Models\ShiftNote::where('shift_id', $report->shift_id)->first();

                    // Tasks
                    $tasks = $shift && !empty($shift->task_section)
                        ? (is_string($shift->task_section) ? json_decode($shift->task_section, true) : $shift->task_section)
                        : [];

                    // Get clientId from route param
                                $clientId = request()->route('clientId');
                                $clientName = \App\Models\Client::find($clientId)->display_name ?? 'Unknown Client';

                                if ($shift) {
                                    $clientSection = is_string($shift->client_section) ? json_decode($shift->client_section, true) : ($shift->client_section ?? []);
                                    $timeAndLocation = is_string($shift->time_and_location) ? json_decode($shift->time_and_location, true) : ($shift->time_and_location ?? []);

                                    if (! $shift->is_advanced_shift) {
                                        // Simple shift
                                        $priceBookName = \App\Models\PriceBook::find($clientSection['price_book_id'] ?? null)->name ?? 'Unknown Price Book';

                                        $start = !empty($timeAndLocation['start_time']) 
                                            ? \Carbon\Carbon::parse($timeAndLocation['start_time'])->format('h:i a') 
                                            : '';
                                        $end = !empty($timeAndLocation['end_time']) 
                                            ? \Carbon\Carbon::parse($timeAndLocation['end_time'])->format('h:i a') 
                                            : '';

                                        $shiftText = "{$clientName} - {$priceBookName} | {$start} - {$end}";
                                    } else {
                                        // Advanced shift
                                        $clientDetails = $clientSection['client_details'][0] ?? null;

                                        if (! $clientDetails) {
                                            $shiftText = 'Advanced Shift';
                                        } else {
                                            $ratio = $clientDetails['hours'] ?? '';
                                            $priceBookName = \App\Models\PriceBook::find($clientDetails['price_book_id'] ?? null)->name ?? 'Unknown Price Book';

                                            $shiftText = "{$clientName} - {$ratio} - {$priceBookName}";
                                        }
                                    }
                                } else {
                                    $shiftText = 'N/A';
                                }




                                    if ($shift) {
                            $carerSection = is_string($shift->carer_section) 
                                ? json_decode($shift->carer_section, true) 
                                : ($shift->carer_section ?? []);

                            if (! $shift->is_advanced_shift) {
                                // Simple shift → one staff
                                $userId = $carerSection['user_id'] ?? null;
                                $staffText = \App\Models\User::find($userId)->name ?? 'Unknown Staff';
                            } else {
                                // Advanced shift
                                $staffIds = [];

                                // 1️⃣ Try to get from staff column
                                if (!empty($shift->staff)) {
                                    if (is_string($shift->staff)) {
                                        $staffIds = array_filter(explode(',', $shift->staff));
                                    } elseif (is_array($shift->staff)) {
                                        $staffIds = $shift->staff;
                                    }
                                }

                                // 2️⃣ If no IDs, try carer_section user_details
                                if (empty($staffIds) && !empty($carerSection['user_details'])) {
                                    $staffIds = collect($carerSection['user_details'])
                                        ->pluck('user_id')
                                        ->filter()
                                        ->toArray();
                                }

                                // 3️⃣ Get names
                                $staffNames = \App\Models\User::whereIn('id', $staffIds)->pluck('name')->toArray();

                                $staffText = !empty($staffNames) 
                                    ? implode(', ', $staffNames) 
                                    : 'Advanced Staff';
                            }
                        } else {
                            $staffText = 'N/A';
                        }

                @endphp
          
                <tr>
                    <td>{{ \Carbon\Carbon::parse($report->date)->format('D, d M Y') }}</td>
                    <td>{!! $shiftText !!}</td>
                    <td>{{ $staffText }}</td>
                    <td>{{ $report->start_time ? \Carbon\Carbon::parse($report->date.' '.$report->start_time)->format('h:i a (d/m/Y)') : '' }}</td>
                    <td>{{ $report->end_time ? \Carbon\Carbon::parse($report->date.' '.$report->end_time)->format('h:i a (d/m/Y)') : '' }}</td>
                    <td>{{ $report->hours_x_rate }}</td>
                    <td>{{ $report->additional_cost !== null ? '$' . number_format($report->additional_cost, 2) : '' }}</td>
                    <td>{{ $report->distance_x_rate }}</td>
                    <td>{{ $report->total_cost !== null ? '$' . number_format($report->total_cost, 2) : '' }}</td>
                    <td>{{ $report->running_total !== null ? '$' . number_format($report->running_total, 2) : '' }}</td>
                    <td>
                        @if($note)
                            <p>{{ $note->note_body }}</p>
                            @if(!empty($note->attachments) && is_array($note->attachments))
                                <div class="flwx">
                                    @foreach($note->attachments as $attachment)
                                    <div class="attachment">
                                        <img src="{{ Storage::url($attachment['file_path']) }}" alt="Attachment">
                                    </div>
                                @endforeach
                                </div>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(!empty($tasks))
                            <ul>
                                @foreach($tasks as $task)
                                    <li>{{ $task['task_name'] ?? '' }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                    <td>Signature: _____________</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
