<x-filament-panels::page>
     <style>
        .mt-4 {
        margin-top: 1rem;
    }
    .flex {
        display: flex;
    }
    .justify-end {
        justify-content: flex-end;
    }
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
    .label {
        font-weight: 500;
        padding-right: 2rem;
    }
    .value {
        font-weight: 400;
    }
        .pay-img{
                width: 60px;
                 height: auto;
        }
    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0.25rem;
    }
    .form-group input {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        margin-top: 0.25rem;
    }
    .form-group input:focus {
        outline: none;
        border-color: #a0aec0;
        box-shadow: 0 0 0 2px rgba(160, 174, 192, 0.2);
    }
    .grid-cols-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .mt-4 {
        margin-top: 1rem;
    }
    .mt-6 {
        margin-top: 1.5rem;
    }
    .p-6 {
        padding: 1.5rem;
    }
    .bg-white {
        background-color: #ffffff;
    }
    .shadow-sm {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    .rounded-lg {
        border-radius: 0.5rem;
    }
    .p-4 {
        padding: 1rem;
    }
    .flex {
        display: flex;
    }
    .justify-between {
        justify-content: space-between;
    }
    .items-start {
        align-items: flex-start;
    }
    .text-right {
        text-align: right;
    }
    .text-sm {
        font-size: 0.875rem;
    }
    .text-gray-600 {
        color: #718096;
    }
    .text-gray-900 {
        color: #1a202c;
    }
    .font-medium {
        font-weight: 500;
    }
    .text-lg {
        font-size: 1.125rem;
    }
    .font-semibold {
        font-weight: 600;
    }
    .min-w-full {
        min-width: 100%;
    }
    .divide-y {
        border-top-width: 1px;
    }
    .divide-gray-200 {
        border-color: #edf2f7;
    }
    .bg-gray-50 {
        background-color: #f7fafc;
    }
    .px-6 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    .py-3 {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }
    .text-xs {
        font-size: 0.75rem;
    }
    .text-gray-500 {
        color: #a0aec0;
    }
    .uppercase {
        text-transform: uppercase;
    }
    .whitespace-nowrap {
        white-space: nowrap;
    }
    .py-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .justify-end {
        justify-content: flex-end;
    }
    .space-x-4 {
        margin-left: 1rem;
    }
    .space-x-4 > * + * {
        margin-left: 1rem;
    }
    .bg-gray-200 {
        background-color: #e2e8f0;
    }
    .text-gray-700 {
        color: #4a5568;
    }
    .px-4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .rounded {
        border-radius: 0.25rem;
    }
    .bg-blue-600 {
        background-color: #3182ce;
    }
    .text-white {
        color: #ffffff;
    }
    .h-6 {
        height: 1.5rem;
    }
    .newgri{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    }
    
    /* Custom tooltip styles */
    .tooltip-container {
        position: relative;
        display: inline-block;
    }
    
    .tooltip {
        visibility: hidden;
        opacity: 0;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px 12px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;
        font-size: 12px;
        white-space: nowrap;
        transition: opacity 0.3s, visibility 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }
    
    .tooltip-container:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }
    
    /* Enhanced title tooltip */
    input[disabled][title]:hover::after {
        content: attr(title);
        position: absolute;
        background-color: #333;
        color: #fff;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        margin-top: 25px;
        margin-left: -60px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        pointer-events: none;
    }
</style>
    <div>
        <div class="p-6">
            <div class="bg-white shadow-sm rounded-lg p-4 mt-4">
                 <div style="display: flex;justify-content: space-between;margin-top: 40px;padding: 20px;">
                            <h2 style="font-size:22px;display: flex;gap: 15px;" class="text-md font-medium">
                    <img style="width: 30px;height: 30px;" src="{{ Storage::url($this->company['company_logo']) }}" alt="">
                            {{ $this->company->name }}</h2>
                         <p class="text-sm mb-4">{{ now()->format('F d, Y') }}</p>

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
                    
                <div class="mt-4 grid newgri gap-4">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700">To</label>
                        <select wire:model="additional_contact_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                            @foreach($this->contactOptions as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700">Issued at</label>
                        <input type="date" wire:model="issue_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700">Payment Due</label>
                      <input type="date" wire:model="payment_due" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="{{ now()->addDays(14)->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700">PO</label>
                        <input type="text" wire:model="purchase_order" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700">Ref No</label>
                        @php
                            $randomNumber = str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
                            $formattedNumber = substr($randomNumber, 0, 3) . ' ' . substr($randomNumber, 3, 3) . ' ' . substr($randomNumber, 6, 3);
                        @endphp

                       <input 
                            type="text" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" 
                            wire:model="ref_no"
                            value="{{ $formattedNumber }}"
                        >

                    </div>
                </div>
                 </div>


                <div class="mt-6">
                    <table>
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="font-size: 11px;">NDIA Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                               @foreach($this->billingReports as $report)

                               @php
                                    $shift = \App\Models\Shift::find($report->shift_id);

                                $clientName = \App\Models\Client::find($this->client_id)->display_name ?? 'Unknown Client';

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


                                        $shiftTextHour = "{$clientName} ({$start_date}  {$start} - {$end}) [{$priceBookName}]  ";
                                        $shiftTextKm = "{$clientName} ({$start_date}  {$start} - {$end}) [{$priceBookName}]  ";
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

                                            $shiftTextHour = "{$clientName} ({$start_date}  {$start} - {$end}) [{$priceBookName}] ";
                                            $shiftTextKm = "{$clientName} ({$start_date}  {$start} - {$end}) [{$priceBookName}] ";

                                        }
                                    }
                                } else {
                                    $shiftText = 'N/A';
                                }
                               @endphp
                                <tr class="billing-row" data-amount="{{ $report->total_cost }}">
                                <td class="px-6 py-4" style="font-size:13px;     width: 100%;">
                                        <input 
                                            style="padding: 10px;"
                                            type="text" 
                                            value="{{ $shiftTextHour }}" 
                                            readonly
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm truncate"
                                            title="{{ $shiftTextHour }}"  
                                        >
                                    </td>
                                <td class="px-6 py-4" style="font-size:13px;">Hours</td>
                                <td class="px-6 py-4" style="font-size:13px;">{{ $report->matched_price_book_detail->ref_hour ?? '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">{{ $report->hours !== null ? number_format($report->hours, 1) : '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">{{ $report->rate ?? '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                    <div class="tooltip-container">
                                        <input type="checkbox" class="tax-checkbox"
                                                {{ $report->status === 'Paid' ? 'disabled' : 'checked' }}
                                                title="{{ $report->status === 'Paid' ? '' : 'Mark as paid' }}">
                                            @if($report->status === 'Paid')
                                                <div class="tooltip">Already invoiced.</div>
                                            @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4" style="font-size:13px;">
                                    ${{ number_format($report->hours_total, 2) }}
                                </td>
                                
                                {{-- Include/Exclude checkbox --}}
                                <td class="px-6 py-4" style="font-size:13px;">
                                    <div class="tooltip-container">
                                        <input type="checkbox" class="include-checkbox"
                                                {{ $report->status === 'Paid' ? 'disabled' : 'checked' }}
                                                title="{{ $report->status === 'Paid' ? '' : 'Mark as paid' }}">
                                            @if($report->status === 'Paid')
                                                <div class="tooltip">Already invoiced.</div>
                                            @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4" style="font-size:13px;     width: 100%;">
                                        <input 
                                            style="padding: 10px;"
                                            type="text" 
                                            value="{!! $shiftTextKm !!}" 
                                            readonly
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm truncate"
                                            title="{!! $shiftTextKm !!}"  
                                        >
                                    </td>
                                <td class="px-6 py-4" style="font-size:13px;">Kms</td>
                                <td class="px-6 py-4" style="font-size:13px;">{{ $report->matched_price_book_detail->ref_km ?? '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;"> {{ $report->distance !== null ? number_format($report->distance, 1) : '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">{{ $report->distance_rate ?? '-' }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                    <div class="tooltip-container">
                                        <input type="checkbox" name="tax" 
                                               {{ $report->status === 'Paid' ? 'disabled' : 'checked' }}
                                               title="">
                                        @if($report->status === 'Paid')
                                            <div class="tooltip">Already invoiced.</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4" style="font-size:13px;">${{ number_format($report->distance_total, 2) }}</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                    <div class="tooltip-container">
                                        <input type="checkbox" name="tax" 
                                               {{ $report->status === 'Paid' ? 'disabled' : '' }}
                                               title="">
                                        @if($report->status === 'Paid')
                                            <div class="tooltip">Already invoiced.</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4" style="font-size:13px;     width: 100%;">
                                        <input 
                                            style="padding: 10px;"
                                            type="text" 
                                            value="{{ $shiftTextHour }}" 
                                            readonly
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm truncate"
                                            title="{{ $shiftTextHour }}"  
                                        >
                                    </td>
                                <td class="px-6 py-4" style="font-size:13px;">Program</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                 <select style="font-size: 12px;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                                    @foreach($this->invoiceCategories as $category)
                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                </td>
                                <td class="px-6 py-4" style="font-size:13px;">1.0</td>
                                <td class="px-6 py-4" style="font-size:13px;">$0.00</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                    <div class="tooltip-container">
                                        <input type="checkbox" name="tax" 
                                               {{ $report->status === 'Paid' ? 'disabled' : 'checked' }}
                                               title="">
                                        @if($report->status === 'Paid')
                                            <div class="tooltip">Already invoiced.</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4" style="font-size:13px;">$0.00</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                    <div class="tooltip-container">
                                        <input type="checkbox" name="tax" 
                                               {{ $report->status === 'Paid' ? 'disabled' : '' }}
                                               title="">
                                        @if($report->status === 'Paid')
                                            <div class="tooltip">Already invoiced.</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4" style="font-size:13px;     width: 100%;">
                                        <input 
                                            style="padding: 10px;"
                                            type="text" 
                                            value="{{ $shiftTextHour }}" 
                                            readonly
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm truncate"
                                            title="{{ $shiftTextHour }}"  
                                        >
                                    </td>
                                <td class="px-6 py-4" style="font-size:13px;">Additional</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                 <select style="font-size: 12px;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                                    @foreach($this->invoiceCategories as $category)
                                       <option value="{{ $category->name }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                </td>
                                <td class="px-6 py-4" style="font-size:13px;">1.0</td>
                                <td class="px-6 py-4" style="font-size:13px;">$0.00</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                    <div class="tooltip-container">
                                        <input type="checkbox" name="tax" 
                                               {{ $report->status === 'Paid' ? 'disabled' : 'checked' }}
                                               title="">
                                        @if($report->status === 'Paid')
                                            <div class="tooltip">Already invoiced.</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4" style="font-size:13px;">$0.00</td>
                                <td class="px-6 py-4" style="font-size:13px;">
                                    <div class="tooltip-container">
                                        <input type="checkbox" name="tax" 
                                               {{ $report->status === 'Paid' ? 'disabled' : '' }}
                                               title="">
                                        @if($report->status === 'Paid')
                                            <div class="tooltip">Already invoiced.</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>   
                                @endforeach
                           
                        </tbody>
                    </table>
                </div>
                <div style="display: flex;justify-content: space-between;padding:20px">
               
                
                <div class="mt-4">
                    <p style="font-size: 21px;color: grey;">Payment Methods:</p>
                    <div style="gap: 5px;margin-bottom: 15px;margin-top: 30px;" class="flex space-x-2">
                        <img class="pay-img" src="https://appassets02.shiftcare.com/assets/visa-fbb0b3025bad98baadefdbe82fd8db4d835921fe3d843d15c3a0d2efec69a8ac.png" alt="Visa" class="h-6">
                        <img class="pay-img" src="https://appassets02.shiftcare.com/assets/mastercard-9dd72d3d59cca8f3f3d5392d81e92cfc1f144e933aafa95c3a1812d1e4f989b3.png" alt="MasterCard" class="h-6">
                        <img class="pay-img" src="https://appassets02.shiftcare.com/assets/american-express-6080b0ab7050c8133f4ce6fcc95b9cdce1f28fa53dfe81c9b9dd3ec4dc5a1c96.png" alt="American Express" class="h-6">
                        <img class="pay-img" src="https://appassets02.shiftcare.com/assets/paypal2-ad0bdd2a8cf5817e75572c50a4c2f83b7385bdd9395588e80ad5f77edb441d16.png" alt="PayPal" class="h-6">
                    </div>
                        <input style="background: #F5F5F5;width: 100%;font-size: 13px;" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" disabled value="BSB: 062-585 ACC: 11037954">
                </div>

                  <div class="mt-4 flex">
                        <table class="summary-table">
                            <tr>
                                <td class="label">Subtotal:</td>
                                <td class="value" id="subtotal">${{ number_format($this->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="label">Tax:</td>
                                <td class="value" id="tax">${{ number_format($this->tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="label">Total:</td>
                                <td class="value" id="grandTotal">${{ number_format($this->grandTotal, 2) }}</td>
                            </tr>
                        </table>
                    </div>



                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <button onclick="window.history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
                    <button onclick="createInvoiceWithValues()" class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
                </div>
            </div>
        </div>
    </div>
   
<script>
document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll(".billing-row");

    function calculateTotals() {
        let subtotal = 0;
        let tax = 0;

        rows.forEach(row => {
            const amount = parseFloat(row.dataset.amount) || 0;
            const includeCheckbox = row.querySelector(".include-checkbox");
            const taxCheckbox = row.querySelector(".tax-checkbox");

            // Skip if the row is already paid (disabled checkboxes indicate paid status)
            if (includeCheckbox.disabled || taxCheckbox.disabled) {
                return; // Skip this row - it's already paid
            }

            if (includeCheckbox.checked) {
                taxCheckbox.disabled = false; // editable
                subtotal += amount;

                if (taxCheckbox.checked) {
                    tax += amount * 0.10; // 10% tax
                }
            } else {
                taxCheckbox.checked = false;   // reset
                taxCheckbox.disabled = true;   // lock it
            }
        });

        const grandTotal = subtotal + tax;

        // Update UI with calculated values
        document.getElementById("subtotal").innerText = "$" + subtotal.toFixed(2);
        document.getElementById("tax").innerText = "$" + tax.toFixed(2);
        document.getElementById("grandTotal").innerText = "$" + grandTotal.toFixed(2);
    }

    // Bind events
    rows.forEach(row => {
        const include = row.querySelector(".include-checkbox");
        const tax = row.querySelector(".tax-checkbox");

        include.addEventListener("change", calculateTotals);
        tax.addEventListener("change", calculateTotals);
    });

    // Initialize with PHP values - don't override on first load
    // The PHP values are already displayed in the HTML
});

// Function to create invoice with HTML values
function createInvoiceWithValues() {
    // Get current values from HTML elements
    const subtotalText = document.getElementById("subtotal").innerText;
    const taxText = document.getElementById("tax").innerText;
    const grandTotalText = document.getElementById("grandTotal").innerText;
    
    // Send values to PHP method
    @this.call('updateHtmlValues', subtotalText, taxText, grandTotalText)
        .then(() => {
            // After updating values, create the invoice
            @this.call('createInvoice');
        });
}
</script>


</x-filament-panels::page>

