<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Timesheet Report</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; padding: 16px; }
        h1 { text-align: center; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        th { background: #f5f5f5; }
        .no-print { margin-bottom: 12px; text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <h1>Timesheet Report</h1>

    <div class="no-print">
        <button onclick="window.print()">🖨 Print</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Approved Status</th>
                <th>12a-6a</th>
                <th>6a-8p</th>
                <th>8p-10p</th>
                <th>10p-12a</th>
                <th>Saturday</th>
                <th>Sunday</th>
                <th>Standard Hours</th>
                <th>Break Time</th>
                <th>Public Holidays</th>
                <th>Total</th>
                <th>Mileage</th>
                <th>Expense</th>
                <th>Sleepover</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->user_name }}</td>
                    <td>{{ $row->approved_status }}</td>
                    <td>{{ $row->weekday_12a_6a }}</td>
                    <td>{{ $row->weekday_6a_8p }}</td>
                    <td>{{ $row->weekday_8p_10p }}</td>
                    <td>{{ $row->weekday_10p_12a }}</td>
                    <td>{{ $row->saturday }}</td>
                    <td>{{ $row->sunday }}</td>
                    <td>{{ $row->standard_hours }}</td>
                    <td>{{ $row->break_time }}</td>
                    <td>{{ $row->public_holidays }}</td>
                    <td>{{ $row->total }}</td>
                    <td>{{ $row->mileage }}</td>
                    <td>{{ $row->expense }}</td>
                    <td>{{ $row->sleepover }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
