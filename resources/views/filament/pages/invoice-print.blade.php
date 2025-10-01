<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_no }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, Noto Sans; color:#1a202c; }
        @page { size: A4; margin: 10mm; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .container { padding: 24px; }
        .card { background:#fff; border-radius:8px; padding: 12px 16px 24px; }
        .header { display:flex; justify-content:space-between; align-items:center; padding: 12px 8px 8px; }
        .brand { display:flex; gap:12px; align-items:center; font-size:22px; font-weight:600; }
        .brand img { width:30px; height:30px; object-fit:contain; }
        .badge { display:inline-block; padding:8px 19px; font-size:12px; font-weight:700; border-radius:10px; color:#fff; }
        .badge-unpaid { background: linear-gradient(135deg, #ff9800, #ffb74d); }
        .badge-paid { background: linear-gradient(135deg, #4caf50, #81c784); }
        hr { border:0; border-top:1px solid #e5e7eb; margin: 6px 0 12px; }
        .cols { display:flex; justify-content:space-between; }
        .col { padding: 18px 24px; }
        .label { font-size:12px; color:#374151; font-weight:500; }
        .muted { color:#4b5563; font-size:13px; }
        table { width:100%; border-collapse: collapse; }
        thead th { text-align:left; font-size:11px; color:#6b7280; text-transform:uppercase; padding:8px 12px; border-bottom:1px solid #e5e7eb; }
        tbody td { font-size:13px; padding:10px 12px; border-bottom:1px solid #f1f5f9; }
        .summary { display:flex; justify-content:space-between; padding: 16px 20px; margin-top:24px; }
        .summary-table { border-collapse: collapse; }
        .summary-table td { padding: 6px 16px; text-align:right; font-size:14px; }
        .summary-table .label { color:#374151; font-weight:500; text-align:left; }
        .pill-input { background:#F5F5F5; width:100%; font-size:13px; border:1px solid #e5e7eb; border-radius:6px; padding:8px 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <div class="brand">
                @if($company && $company->company_logo)
                    <img src="{{ Storage::url($company->company_logo) }}" alt="logo">
                @endif
                <span>{{ $company->name ?? 'Company' }}</span>
            </div>
            @if ($invoice->balance == 0)
                <span class="badge badge-paid">PAID</span>
            @else
                <span class="badge badge-unpaid">UNPAID</span>
            @endif
        </div>
        <hr>

        <div class="cols">
            <div class="col">
                <div class="label">From</div>
                <div class="muted">{{ $company->name ?? '' }}</div>
                <div class="muted">6 Ebony Link</div>
                <br>
                <div class="muted">+351 2323 123</div>
                <div class="muted">info@empoweringss.com.au</div>
                <div class="muted">+65 6521 959</div>
            </div>
            <div class="col">
                <div class="label">To</div>
                @if ($additional_name)
                    <div class="muted">{{ $additional_name }}</div>
                    <div class="muted">{{ $additional_address }}</div>
                    <br>
                    <div class="muted">📞 {{ $additional_phone }}</div>
                    <div class="muted">✉ {{ $additional_email }}</div>
                @else
                    <div class="muted">{{ $client_name }}</div>
                    <div class="muted">{{ $client_address }}</div>
                    <br>
                    <div class="muted">📞 {{ $client_phone }}</div>
                    <div class="muted">✉ {{ $client_email }}</div>
                @endif
            </div>
            <div class="col">
                <div><strong style="font-size:14px;">Tax Invoice:</strong> <span class="muted">{{ $invoice->invoice_no }}</span></div>
                <div><strong style="font-size:14px;">Issue Date:</strong> <span class="muted">{{ $invoice->issue_date }}</span></div>
                <div><strong style="font-size:14px;">Payment Due:</strong> <span class="muted">{{ $invoice->payment_due }}</span></div>
                <div><strong style="font-size:14px;">NDIS:</strong> <span class="muted">{{ $invoice->NDIS }}</span></div>
                <div><strong style="font-size:14px;">Ref No:</strong> <span class="muted">{{ $invoice->ref_no }}</span></div>
            </div>
        </div>

        <div style="margin-top:12px;">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Tax</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @php $remainingTax = round($invoice->tax, 2); @endphp
                    @foreach($billingReports as $report)
                        @php
                            $shift = \App\Models\Shift::find($report->shift_id);
                            $clientName = \App\Models\Client::find($invoice->client_id)->display_name ?? 'Unknown Client';
                            if ($shift) {
                                $clientSection = is_string($shift->client_section) ? json_decode($shift->client_section, true) : ($shift->client_section ?? []);
                                $timeAndLocation = is_string($shift->time_and_location) ? json_decode($shift->time_and_location, true) : ($shift->time_and_location ?? []);
                                $priceBookName = 'Unknown Price Book';
                                if (!$shift->is_advanced_shift) {
                                    $priceBookName = \App\Models\PriceBook::find($clientSection['price_book_id'] ?? null)->name ?? 'Unknown Price Book';
                                } else {
                                    $clientDetails = $clientSection['client_details'][0] ?? null;
                                    if ($clientDetails) {
                                        $priceBookName = \App\Models\PriceBook::find($clientDetails['price_book_id'] ?? null)->name ?? 'Unknown Price Book';
                                    }
                                }
                                $start = !empty($timeAndLocation['start_time']) ? \Carbon\Carbon::parse($timeAndLocation['start_time'])->format('h:i a') : '';
                                $end = !empty($timeAndLocation['end_time']) ? \Carbon\Carbon::parse($timeAndLocation['end_time'])->format('h:i a') : '';
                                $start_date = !empty($timeAndLocation['start_date']) ? \Carbon\Carbon::parse($timeAndLocation['start_date'])->format('d/m/Y') : '';
                                $refHour = $report->matched_price_book_detail->ref_hour ?? '-';
                                $refKm   = $report->matched_price_book_detail->ref_km ?? '-';
                                $shiftTextHour = "$clientName ($start_date $start - $end) [$priceBookName] [$refHour]";
                                $shiftTextKm   = "$clientName ($start_date $start - $end) [$priceBookName] [$refKm]";
                            } else {
                                $shiftTextHour = 'N/A';
                                $shiftTextKm = 'N/A';
                            }

                            $expectedTax = round(($report->total_cost ?? 0) * 0.10, 2);
                            $rowTax = 0.0;
                            if ($remainingTax >= $expectedTax) { $rowTax = $expectedTax; $remainingTax -= $expectedTax; }
                        @endphp
                        <tr>
                            <td style="width:100%;">{{ $shiftTextHour }}</td>
                            <td>Hours</td>
                            <td>{{ $report->hours !== null ? number_format($report->hours, 1) : '-' }}</td>
                            <td>${{ $report->rate ?? '-' }}</td>
                            <td>${{ number_format($rowTax, 2) }}</td>
                            <td>${{ number_format($report->hours_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td>{!! $shiftTextKm !!}</td>
                            <td>Kms</td>
                            <td>{{ $report->distance !== null ? number_format($report->distance, 1) : '-' }}</td>
                            <td>{{ $report->distance_rate ?? '-' }}</td>
                            <td>0.0</td>
                            <td>${{ number_format($report->distance_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary">
            <div>
                <div style="font-size:18px;color:#6b7280;">Payment Methods:</div>
                <input class="pill-input" type="text" disabled value="BSB: 062-585 ACC: 11037954">
            </div>
            <div style="font-size:18px;color:#6b7280; align-self:center;">Amount Due {{ \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') }}</div>
            <div>
                <table class="summary-table">
                    <tr><td class="label">Subtotal:</td><td>${{ $invoice->amount }}</td></tr>
                    <tr><td class="label">Tax:</td><td>${{ $invoice->tax }}</td></tr>
                    <tr><td class="label">Paid:</td><td>${{ number_format($totalPaid, 2) }} 
                                  @if($latestDate) ({{ \Carbon\Carbon::parse($latestDate)->format('d/m/Y') }}) @endif</td></tr>
                    <tr><td class="label">Balance:</td><td>${{ $invoice->balance }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function(){ window.print(); });
</script>
</body>
</html>


