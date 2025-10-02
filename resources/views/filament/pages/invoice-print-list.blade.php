<!DOCTYPE html>
<html>
<head>
    <title>Invoice List</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { margin-bottom: 20px; }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-size: 14px;
            }

            thead {
                background: #f8f9fa;
                border-bottom: 2px solid #dee2e6;
            }

            th {
                padding: 12px;
                text-align: left;
                font-weight: bold;
                color: #333;
                border: 1px solid #dee2e6;
                background-color: #f1f3f5;
            }

            td {
                padding: 10px;
                border: 1px solid #e1e5eb;
                color: #555;
            }

            tbody tr:nth-child(even) {
                background-color: #f9fafb;
            }

            tbody tr:hover {
                background-color: #f1f5f9;
            }


              .status-badge-row {
      display: flex;
      justify-content: space-around;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .status-badge {
    flex: 1;
    min-width: 143px;
    font-size: 13px;
    padding: 13px 30px;
    border-radius: 10px;
    color: #1c1c1cff;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: default;
    display: flex;
    justify-content: space-between;
    }

    .status-success { background: #E8F6F9; }  /* Green */
    .status-warning { background: #f2e8f9ff;  }  /* Yellow */
    .status-danger  { background: #E3F5ED; }  /* Red */
    .status-info    { background: #FFEFDB; }  /* Teal */
    .status-primary { background: #e2edc7; }  /* Blue */
    </style>
</head>
<body onload="window.print()">
    <h2>Invoice List</h2>

    <div class="status-badge-row">
    <div class="status-badge status-success">
        <div>Invoiced</div>
        <div>${{ number_format($grandTotal, 2) }}</div>
    </div>
    <div class="status-badge status-warning">
        <div>Tax</div>
        <div>${{ number_format($totalTax, 2) }}</div>
    </div>
    <div class="status-badge status-danger">
        <div>Paid</div>
        <div>${{ number_format($paidAmount, 2) }}</div>
    </div>
    <div class="status-badge status-info">
        <div>Unpaid</div>
        <div>${{ number_format($unpaidOverdueBalance, 2) }}</div>
    </div>
    <div class="status-badge status-primary">
        <div>Overdue</div>
        <div>${{ number_format($overdueBalance, 2) }}</div>
    </div>
</div>


    <!-- Invoice Table -->
    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Client</th>
                <th>Amount</th>
                <th>Tax</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Issued Date</th>
                <th>Payment Due</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_no }}</td>
                    <td>{{ $invoice->client->display_name ?? '-' }}</td>
                    <td>${{ number_format($invoice->amount, 2) }}</td>
                    <td>${{ number_format($invoice->tax, 2) }}</td>
                    <td>${{ number_format($invoice->balance, 2) }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->payment_due)->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">No invoices found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
