<x-filament-panels::page>
    <style>

            .summary-table {
        border-collapse: collapse;
        width: auto;
    }
    .summary-table tr {
        border-bottom: 1px solid #e2e8f0;
    }
    .summary-table td {
        padding: 0.5rem 1rem;
        text-align: right;
        font-size: 0.875rem;
        color: #1a202c;
    }
    
    .invoice-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-bottom: 20px;
    }

    .invoice-actions button {
      padding: 8px 16px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
      cursor: pointer;
      background: #f8f8f8;
      transition: 0.2s ease-in-out;
    }

    .invoice-actions button:hover {
      background: #eee;
    }

    .invoice-actions .edit-btn {
      background-color: #007bff;
      color: #fff;
      border: none;
    }

    .invoice-actions .edit-btn:hover {
      background-color: #0056b3;
    }

    .invoice-actions .void {
      background-color: #dc3545;
      color: #fff;
      border: none;
    }

    .invoice-actions .void:hover {
      background-color: #a71d2a;
    }

    .payments-section {
      border-top: 1px solid #ddd;
      padding-top: 20px;
      margin-top: 20px;
      width: 60%;
    }

    .payments-section h3 {
      font-size: 16px;
      margin-bottom: 15px;
      color: #333;
    }

    .payments-form {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .payments-form input[type="text"],
    .payments-form input[type="date"],
    .payments-form input[type="number"] {
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      flex: 1;
    }

    .payments-form button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .payments-form button:hover {
      background-color: #0056b3;
    }

     .timeline {
      position: relative;
      margin: 20px auto;
      padding-left: 40px;
      border-left: 2px solid #ddd;
      margin-top: 70px;
    }

    .timeline-date {
      background: #28a745;
      color: #fff;
      display: inline-block;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 14px;
      margin-bottom: 20px;
    }

    .timeline-item {
      position: relative;
      margin-bottom: 20px;
      padding: 15px;
      background: #fff;
      border: 1px solid #eee;
      border-radius: 6px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .timeline-item:before {
      content: "";
      position: absolute;
      top: 20px;
      left: -30px;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: #007bff;
      border: 2px solid #fff;
      box-shadow: 0 0 0 2px #ddd;
    }

    .timeline-item .title {
      font-weight: bold;
      font-size: 14px;
      color: #007bff;
    }

    .timeline-item .subtitle {
      font-size: 13px;
      color: #333;
      margin-top: 4px;
    }

    .timeline-item .time {
      font-size: 12px;
      color: #999;
      position: absolute;
      right: 15px;
      top: 15px;
    }

    /* Add Notes Section */
    .notes-section {
      margin-top: 30px;
      background: #fff;
      padding: 15px;
      border: 1px solid #eee;
      border-radius: 6px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .notes-section h4 {
      font-size: 14px;
      color: #333;
      margin-bottom: 10px;
    }

    .notes-section textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
      resize: none;
    }

    .notes-section button {
      margin-top: 10px;
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .notes-section button:hover {
      background-color: #0056b3;
    }

    .badge {
        display: inline-block;
        padding: 8px 19px;
        font-size: 12px;
        font-weight: bold;
        border-radius: 10px;
        color: #fff;
        position: relative;
        top: -57px;
    }

    .badge-unpaid {
      background: linear-gradient(135deg, #ff9800, #ffb74d); /* Orange/Gold */
    }

    .badge-paid {
      background: linear-gradient(135deg, #4caf50, #81c784); /* Green */
    }
    
    /* Print styles */
    @media print {
      @page { size: A4; margin: 10mm; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .invoice-actions,
      .payments-section,
      .timeline,
      .notes-section { display: none !important; }
      .p-6 { padding: 0 !important; }
      .bg-white { box-shadow: none !important; }
    }


/* Overlay */
.modal-overlay {
    position: fixed;
    inset: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(6px);
    background: rgba(0,0,0,0.4);
    z-index: 1000;
}

/* Modal Box */
.modal-box {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    width: 400px;
    max-width: 90%;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    position: relative;
    animation: fadeIn 0.3s ease;
}

/* Close Button */
.modal-close {
    position: absolute;
    top: 12px;
    right: 14px;
    font-size: 20px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #666;
}
.modal-close:hover { color: #000; }

/* Title */
.modal-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #222;
}

/* Form */
.modal-form label {
    display: block;
    margin: 10px 0 4px;
    font-size: 14px;
    font-weight: 500;
    color: #444;
}
.modal-form input, 
.modal-form select, 
.modal-form textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    transition: border 0.3s;
}
.modal-form input:focus,
.modal-form select:focus,
.modal-form textarea:focus {
    border-color: #2563eb;
    outline: none;
}

/* Actions */
.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}
.btn {
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}
.btn.cancel {
    background: #e5e7eb;
    color: #111;
}
.btn.cancel:hover { background: #d1d5db; }
.btn.save {
    background: #2563eb;
    color: white;
}
.btn.save:hover { background: #1d4ed8; }

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
    </style>
 <div class="p-6">
           <div id="invoice-print-area" class="bg-white shadow-sm rounded-lg p-4 mt-4">
                 <div style="display: flex;justify-content: space-between;margin-top: 40px;padding: 20px;">
                            <h2 style="font-size:22px;display: flex;gap: 15px;" class="text-md font-medium">
                    <img style="width: 30px;height: 30px;" src="{{ Storage::url($this->company['company_logo']) }}" alt="">
                            {{ $this->company->name }}</h2>

                         @if ($invoice->balance == 0)
                                <span class="badge badge-paid">PAID</span>
                            @else
                                <span class="badge badge-unpaid">UNPAID</span>
                            @endif


                        </div>
                    <hr>

                 <div style="display: flex;justify-content: space-between;">
                       <div style="padding:30px">
                        <label class="block text-sm font-medium text-gray-700">From</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $this->company->name }}</p>
                        <p class="text-sm text-gray-600">6 Ebony Link</p><br>
                        <p class="text-sm text-gray-600">+351 2323 123</p>
                        <p class="text-sm text-gray-600">info@empoweringss.com.au</p>
                        <p class="text-sm text-gray-600">+65 6521 959</p>
                    </div>

                    <div style="padding:30px">
                        <label class="block text-sm font-medium text-gray-700">To</label>
                            @if ($additional_name)
                                <p class="mt-1 text-sm text-gray-900">{{ $additional_name }}</p>
                                <p class="text-sm text-gray-600">{{ $additional_address }}</p><br>
                                <p class="text-sm text-gray-600">📞 {{ $additional_phone }}</p>
                                <p class="text-sm text-gray-600">✉ {{ $additional_email }}</p>
                            @else
                                <p class="mt-1 text-sm text-gray-900">{{ $client_name }}</p>
                                <p class="text-sm text-gray-600">{{ $client_address }}</p><br>
                                <p class="text-sm text-gray-600">📞 {{ $client_phone }}</p>
                                <p class="text-sm text-gray-600">✉ {{ $client_email }}</p>
                            @endif

                    </div>

                    <div style="padding:30px">
                        <p><strong style="font-size:14px;">Tax Invoice:</strong> <span style="font-size:13px;">{{ $invoice->invoice_no }}</span> </p>
                        <p><strong style="font-size:14px;">Issue Date:</strong> <span style="font-size:13px;">{{ $invoice->issue_date }}</span> </p>
                        <p><strong style="font-size:14px;">Payment Due:</strong> <span style="font-size:13px;">{{ $invoice->payment_due }}</span> </p>
                        <p><strong style="font-size:14px;">NDIS:</strong> <span style="font-size:13px;">{{ $invoice->NDIS }}</span> </p>
                        <p><strong style="font-size:14px;">Ref No:</strong> <span style="font-size:13px;">{{ $invoice->ref_no }}</span> </p>
                    </div>
                 </div>

                       <div class="mt-6">
                    <table>
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase" >Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                               @foreach($this->billingReports as $report)

                               @php
                                    $shift = \App\Models\Shift::find($report->shift_id);

                                $clientName = \App\Models\Client::find($invoice->client_id)->display_name ?? 'Unknown Client';

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

                                        $start_date = !empty($timeAndLocation['start_date']) 
                                                ? \Carbon\Carbon::parse($timeAndLocation['start_date'])->format('d/m/Y') 
                                                  : '';

                                            $refHour = $report->matched_price_book_detail->ref_hour ?? '-';
                                            $refKm   = $report->matched_price_book_detail->ref_km ?? '-';

                                            $shiftTextHour = "{$clientName} ({$start_date} {$start} - {$end}) [{$priceBookName}] [{$refHour}]";
                                            $shiftTextKm   = "{$clientName} ({$start_date} {$start} - {$end}) [{$priceBookName}] [{$refKm}]";

                                    } else {
                                        // Advanced shift
                                        $clientDetails = $clientSection['client_details'][0] ?? null;

                                        if (! $clientDetails) {
                                            $shiftText = 'Advanced Shift';
                                        } else {
                                            $priceBookName = \App\Models\PriceBook::find($clientDetails['price_book_id'] ?? null)->name ?? 'Unknown Price Book';
                                       
                                            $start = !empty($timeAndLocation['start_time']) 
                                                    ? \Carbon\Carbon::parse($timeAndLocation['start_time'])->format('h:i a') 
                                                    : '';
                                                $end = !empty($timeAndLocation['end_time']) 
                                                    ? \Carbon\Carbon::parse($timeAndLocation['end_time'])->format('h:i a') 
                                                    : '';

                                                $start_date = !empty($timeAndLocation['start_date']) 
                                                        ? \Carbon\Carbon::parse($timeAndLocation['start_date'])->format('d/m/Y') 
                                                        : '';

                                      $refHour = $report->matched_price_book_detail->ref_hour ?? '-';
                                    $refKm   = $report->matched_price_book_detail->ref_km ?? '-';

                                    $shiftTextHour = "{$clientName} ({$start_date} {$start} - {$end}) [{$priceBookName}] [{$refHour}]";
                                    $shiftTextKm   = "{$clientName} ({$start_date} {$start} - {$end}) [{$priceBookName}] [{$refKm}]";

                                        }
                                    }
                                } else {
                                    $shiftText = 'N/A';
                                }
                               @endphp
                                <tr class="billing-row" data-amount="{{ $report->total_cost }}">
                                <td class="px-6 py-4" style="font-size:13px;     width: 100%;">
                                       {{ $shiftTextHour }}
                                    </td>
                                <td class="px-6 py-4" style="font-size:13px;">Hours</td>
                                <td class="px-6 py-4" style="font-size:13px;">{{ $report->hours !== null ? number_format($report->hours, 1) : '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">${{ $report->rate ?? '-' }}</td>
                             @php
                                    // Calculate per-report expected tax
                                    $expectedTax = round($report->total_cost * 0.10, 2);

                                    // Track how much tax is left to assign
                                    static $remainingTax = null;

                                    if ($remainingTax === null) {
                                        $remainingTax = round($invoice->tax, 2);
                                    }

                                    $rowTax = 0.0;

                                    // If remaining tax is enough to cover this row’s expectedTax → assign
                                    if ($remainingTax >= $expectedTax) {
                                        $rowTax = $expectedTax;
                                        $remainingTax -= $expectedTax;
                                    }
                                @endphp

                                <td class="px-6 py-4" style="font-size:13px;">
                                    ${{ number_format($rowTax, 2) }}
                                </td>

                                <td class="px-6 py-4" style="font-size:13px;">
                                    ${{ number_format($report->hours_total, 2) }}
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="px-6 py-4" style="font-size:13px;     width: 100%;">
                                        {!! $shiftTextKm !!}
                                    </td>
                                <td class="px-6 py-4" style="font-size:13px;">Kms</td>
                                <td class="px-6 py-4" style="font-size:13px;"> {{ $report->distance !== null ? number_format($report->distance, 1) : '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">{{ $report->distance_rate ?? '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">0.0</td>
                                <td class="px-6 py-4" style="font-size:13px;">${{ number_format($report->distance_total, 2) }}</td>
                            </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
                 <div style="display: flex;justify-content: space-between;padding:20px;margin-top:50px">
                
                <div class="mt-4">
                    <p style="font-size: 21px;color: grey;">Payment Methods:</p>
                        <input style="background: #F5F5F5;width: 100%;font-size: 13px;" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" disabled value="BSB: 062-585 ACC: 11037954">
                </div>
                    <p style="font-size: 21px; color: grey;">
                        Amount Due {{ \Carbon\Carbon::parse($invoice->payment_due)->format('d/m/Y') }}
                    </p>

                  <div class="mt-4 flex">
                        <table class="summary-table">
                            <tr>
                                <td class="label">Subtotal:</td>
                                <td class="value" id="subtotal">${{ $invoice->amount }}</td>
                            </tr>
                            <tr>
                                <td class="label">Tax:</td>
                                <td class="value" id="tax">${{ $invoice->tax }}</td>
                            </tr>
                           @php
                              $invoicePayments = \App\Models\InvoicePayment::where('invoice_id', $invoice->id);
                              $totalPaid = $invoicePayments->sum('paid_amount'); 
                              $latestDate = $invoicePayments->latest('payment_date')->value('payment_date'); 
                          @endphp

                          <tr>
                              <td class="label">Paid:</td>
                              <td class="value" id="tax">
                                  ${{ number_format($totalPaid, 2) }} 
                                  @if($latestDate) ({{ \Carbon\Carbon::parse($latestDate)->format('d/m/Y') }}) @endif
                              </td>
                          </tr>

                            <tr>
                                <td class="label">Balance:</td>
                                <td class="value" id="grandTotal">${{ $invoice->balance }}</td>
                            </tr>
                        </table>
                    </div>

                </div>
                <!-- Action Buttons -->
                <div class="invoice-actions">
                    <form action="{{ route('invoices.sendEmail', $invoice->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-email">📧 Email</button>
                        </form>

                    <a href="{{ route('invoices.print', ['invoice' => $invoice->id]) }}" target="_blank" rel="noopener" id="printBtnLink" style="text-decoration:none;">
                        <button type="button">🖨 Print</button>
                    </a>
              @if($invoice->status !== 'Paid')
             <div x-data="{ open: false }">
                  <button type="button" class="edit-btn" @click="open = true">Edit</button>

                  <div class="modal-overlay" x-show="open" x-transition>
                      <div class="modal-box" x-show="open" x-transition.scale>

                          <h2 class="modal-title">Edit Invoice</h2>

                          @php
                              $additionalContacts = \App\Models\AdditionalContact::where('client_id', $invoice->client_id)->get();
                          @endphp

                          <!-- Form -->
                          <form method="POST" action="{{ route('invoices.update', $invoice->id) }}">
                              @csrf
                              @method('PUT')

                              <div class="modal-form">
                                  <label>To</label>
                                  <select name="additional_contact_id">
                                      <option value="">Client</option>
                                      @foreach($additionalContacts as $contact)
                                          <option value="{{ $contact->id }}" 
                                              {{ $invoice->additional_contact_id == $contact->id ? 'selected' : '' }}>
                                              {{ $contact->first_name }} {{ $contact->last_name }}
                                          </option>
                                      @endforeach
                                  </select>

                                  <label>Payment Due</label>
                                  <input type="date" value="{{ $invoice->payment_due }}" name="payment_due">

                                  <label>Ref Number</label>
                                  <input type="text" value="{{ $invoice->ref_no }}" name="ref_no">

                                  <label>Purchase Order</label>
                                  <input type="text" value="{{ $invoice->purchase_order }}" name="purchase_order">
                              </div>

                              <div class="modal-actions">
                                  <button type="button" class="btn cancel" @click="open = false">Cancel</button>
                                  <button type="submit" class="btn save">Save</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>

                  <form id="void-form" action="{{ route('invoices.void', $invoice->id) }}" method="POST" style="display:none;">
                        @csrf
                    </form>

                    <button type="button" class="void" onclick="confirmVoid()">Void</button>

                    <script>
                        function confirmVoid() {
                            if (confirm('Are you sure you want to void this invoice?')) {
                                document.getElementById('void-form').submit();
                            }
                        }
                    </script>
              @endif
                </div>

                <!-- Payments Section -->
                <div class="payments-section">
                    <h3>Payments</h3>

                  @if ($invoice->balance != 0)
                      <form class="payments-form" method="POST" action="{{ route('invoices.payments.store', ['invoice' => $invoice->id]) }}">
                          @csrf
                          <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                          <input type="number" name="paid_amount" step="0.01" placeholder="Amount">
                          <input type="text" name="reference" placeholder="Payment Reference">
                          <input type="date" name="payment_date" value="{{ now()->toDateString() }}">
                          <button type="submit">Submit</button>
                      </form>
                      @endif

                    <div style="margin-top: 16px;">
                        <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 1px solid #e2e8f0;">
                                    <th style="padding: 8px;">Date</th>
                                    <th style="padding: 8px;">Reference</th>
                                    <th style="padding: 8px;">Amount</th>
                                    <th style="padding: 8px; text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($invoice->payments ?? []) as $payment)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 8px;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                        <td style="padding: 8px;">{{ $payment->reference ?? '-' }}</td>
                                        <td style="padding: 8px;">${{ number_format($payment->paid_amount, 2) }}</td>
                                        <td style="padding: 8px; text-align:right;">
                                            <form method="POST" action="{{ route('invoice-payments.destroy', ['invoicePayment' => $payment->id]) }}" onsubmit="return confirm('Delete this payment?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background:#dc3545; color:#fff; border:none; padding:6px 10px; border-radius:4px; cursor:pointer;">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="padding: 8px; color:#64748b;">No payments yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

<div class="timeline">
    @php
        $events = \App\Models\Event::where('invoice_id', $invoice->id)
                    ->latest()
                    ->get()
                    ->groupBy(function($event) {
                        return \Carbon\Carbon::parse($event->created_at)->format('d M, Y');
                    });
    @endphp

    @forelse($events as $date => $dayEvents)
        <div>
            {{-- Show date once --}}
            <div class="timeline-date">{{ $date }}</div>

            @foreach($dayEvents as $event)
                <div class="timeline-item">
                    <span class="title">{{ $event->title }}</span>
                    <span class="time">{{ $event->created_at->diffForHumans() }}</span>
                    
                    {{-- Truncate body with read more --}}
                  @php
                      $body = strip_tags($event->body);
                      $limit = 150; // chars limit
                  @endphp

                  @if(strlen($body) > $limit)
                      <div class="subtitle">
                          <span class="short-text">
                              {{ Str::limit($body, $limit) }}
                              <a href="javascript:void(0)" style="color:#0f59ff" onclick="toggleText(this)">Read more</a>
                          </span>

                          <span class="full-text" style="display:none;">
                              {{ $body }}
                              <a href="javascript:void(0)" style="color:#0f59ff" onclick="toggleText(this)">Read less</a>
                          </span>
                      </div>
                  @else
                      <div class="subtitle">{{ $body }}</div>
                  @endif
                </div>
            @endforeach
        </div>
    @empty
        <div class="timeline-item">
            <div class="subtitle">No events found for this invoice.</div>
        </div>
    @endforelse
</div>

<div class="notes-section">
    <h4>Add Notes</h4>
    <form method="POST" action="{{ route('invoices.notes.store', $invoice->id) }}">
        @csrf
        <textarea name="note" rows="3" placeholder="Write your notes here..." required></textarea>
        <br>
        <button type="submit">Add</button>
    </form>
</div>
    
<script>
function toggleText(el) {
    const container = el.closest('.subtitle');
    const shortText = container.querySelector('.short-text');
    const fullText = container.querySelector('.full-text');

    if (shortText.style.display === "none") {
        shortText.style.display = "inline";
        fullText.style.display = "none";
    } else {
        shortText.style.display = "none";
        fullText.style.display = "inline";
    }
}
</script>
</x-filament-panels::page>
