<?php
// This view assumes the following variables are passed from the controller:
// $records: A collection of TimesheetReport models
// $formatClients: The closure defined in ListTimesheetReports.php to format clients/shift data
// $formatBreakTime: The closure defined in ListTimesheetReports.php to format break time
// $allowanceModel: The Allowance model class (for lookup)

use Carbon\Carbon;

// Check if necessary variables are available
if (!isset($records) || !isset($formatClients) || !isset($formatBreakTime) || !isset($allowanceModel)) {
    // Render an error or return if dependencies are missing in a real environment
    echo "<h1>Error: Missing required data for printing.</h1>";
    exit;
}

// Define Headers
$headers = ['Date', 'Shift', 'Client', 'Start Time', 'Finish Time', 'Break Time', 'Hours', 'Expense', 'Allowances'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Timesheet Print Report</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            margin: 0;
            background-color: #fff;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #333;
        }
        h3 {
            font-size: 1.1em;
            color: #555;
            margin-top: 0;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 10px;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
            line-height: 1.4;
        }
        th {
            background-color: #f5f5f5;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Print-specific styles */
        @media print {
            /* Set landscape A4 size for better table display */
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            body {
                padding: 0;
            }
            /* Ensure the table is visible and not broken across pages */
            table, tr, td, th {
                page-break-inside: avoid;
            }
            h1, h3 {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <h1>Timesheet Print Report</h1>
    <h3>Report Date Range: {{ Carbon::parse($records->min('date'))->format('d M Y') }} - {{ Carbon::parse($records->max('date'))->format('d M Y') }}</h3>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Shift</th>
                <th>Client</th>
                <th>Start Time</th>
                <th>Finish Time</th>
                <th>Break Time</th>
                <th>Hours</th>
                <th>Expense</th>
                <th>Mileage</th>
                <th>Allowances</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <?php
                    // Use the passed closures for formatting
                    $shiftOutput = $formatClients($record->clients, false);
                    $clientsOutput = $formatClients($record->clients, true);
                    $breakTimeOutput = $formatBreakTime($record->break_time);

                    // Allowances Lookup (replicated here since it's hard to pass DB dependency)
                    $allowances = is_string($record->allowances) ? @json_decode($record->allowances, true) : (is_array($record->allowances) ? $record->allowances : []);
                    $allowanceNames = [];
                    if (!empty($allowances)) {
                        $allowanceNames = $allowanceModel::whereIn('id', $allowances)->pluck('name')->filter()->toArray();
                    }
                    $allowancesOutput = implode('<br>', $allowanceNames) ?: '-';

                    // Use <br> instead of comma space for clean print view
                    $shiftPrint = str_replace(', ', '<br>', $shiftOutput);
                    $clientsPrint = str_replace(', ', '<br>', $clientsOutput);
                ?>
                <tr>
                    <td>{{ Carbon::parse($record->date)->format('D, d M Y') }}</td>
                    <td>{!! $shiftPrint !!}</td>
                    <td>{!! $clientsPrint !!}</td>
                    <td>{{ $record->start_time ? Carbon::createFromFormat('H:i:s', $record->start_time)->format('h:i a') : '-' }}</td>
                    <td>{{ $record->end_time ? Carbon::createFromFormat('H:i:s', $record->end_time)->format('h:i a') : '-' }}</td>
                    <td>{{ $breakTimeOutput }}</td>
                    <td>{{ max(0, (float) $record->hours) . ' hrs' }}</td>
                    <td>{{ '$' . number_format($record->expense, 2) }}</td>
                    <td>{{ $record->distance }}</td>
                    <td>{!! $allowancesOutput !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        // Use a slight delay to ensure the browser has rendered the DOM before printing
        window.onload = function() {
            setTimeout(function() {
                window.print();
                // Close the new tab automatically after print is initiated
                setTimeout(function() { window.close(); }, 500);
            }, 100);
        };
    </script>
</body>
</html>
