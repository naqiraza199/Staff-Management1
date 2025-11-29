<x-filament-panels::page>
<div class="flex justify-end mb-4">
    <button id="generate-btn" class="generate-btn"
        class="hidden bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-200">
         <svg xmlns="http://www.w3.org/2000/svg" 
             class="icon" 
             viewBox="0 0 20 20" 
             fill="currentColor">
            <path d="M13 7H7v6h6V7z" />
            <path fill-rule="evenodd" 
                  d="M5 3a2 2 0 00-2 2v10a2 2 0 
                     002 2h10a2 2 0 002-2V5a2 2 0 
                     00-2-2H5zm0 2h10v10H5V5z" 
                  clip-rule="evenodd"/>
        </svg>
        Generate
    </button>
</div>

       <style>
  .badge {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 7px;
    font-size: 0.78rem;        /* ~13px */
    font-weight: 600;
    line-height: 1;
    border-radius: 7px;      /* pill shape */
    box-shadow: 0 1px 2px rgba(8, 15, 25, 0.06);
    border: 1px solid rgba(0,0,0,0.06);
    white-space: nowrap;
    vertical-align: middle;
    transform: translateZ(0);  /* better rendering */
  }

  .badge svg { width: 14px; height: 14px; flex: 0 0 14px; }

  /* Color variants */
  .badge--success {
    background: linear-gradient(180deg, #ECFDF3 0%, #D1FAE5 100%);
    color: #065F46;
    border-color: rgba(6,95,70,0.08);
  }
  .badge--warning {
    background: linear-gradient(180deg, #FFF7ED 0%, #FFEDD5 100%);
    color: #92400E;
    border-color: rgba(146,64,14,0.08);
  }
  .badge--info {
    background: linear-gradient(180deg, #EFF6FF 0%, #DBEAFE 100%);
    color: #1E40AF;
    border-color: rgba(30,64,175,0.08);
  }
  .badge--muted {
    background: linear-gradient(180deg, #F8FAFC 0%, #F1F5F9 100%);
    color: #334155;
    border-color: rgba(51,65,85,0.06);
  }

  .button-container {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 15px;
}

.generate-btn {
    display: none; /* hidden by default */
    background-color: #2563eb; /* blue */
    color: white;
    font-weight: 600;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: background-color 0.2s, transform 0.2s;
    align-items: center;
    font-size: 14px;
}

.generate-btn:hover {
    background-color: #1e40af; /* darker blue */
    transform: translateY(-2px);
}

.generate-btn .icon {
    width: 18px;
    height: 18px;
    margin-right: 6px;
}


  /* Tiny helper if you need very small badges */
</style>


<link rel="stylesheet" href="{{asset('invoice.css')}}">
  <div class="mt-6">
                           <div style="gap: 20px;padding: 30px;background: #ffffffff;border-left: 5px groove #8080806e;" class="flex items-end mb-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">GROUP BY</label>
                                        <div class="relative">
                                            <select style="width: 200px;" name="" id=""  class="block w-44 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                                <option value="Client">Client</option>
                                                <option value="Fund">Fund</option>
                                                <option value="Payment Type">Payment Type</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="display:flex;gap: 10px;">
                                        <div class="relative">
                                            <input
                                                type="checkbox"
                                                style="margin-top: -9px;"
                                            >
                                         
                                        </div>
                                        <label style="font-size:15px;margin-top: -2px;" class="block font-medium text-gray-700 mb-1">HOURS</label>
                                    </div>

                                    <div style="display:flex;gap: 10px;">
                                        <div class="relative">
                                            <input
                                                type="checkbox"
                                                style="margin-top: -9px;"
                                            >
                                         
                                        </div>
                                        <label style="font-size:15px;margin-top: -2px;" class="block font-medium text-gray-700 mb-1">MILEAGE</label>
                                    </div>

                                    <div style="display:flex;gap: 10px;">
                                        <div class="relative">
                                            <input
                                                type="checkbox"
                                                style="margin-top: -9px;"
                                            >
                                         
                                        </div>
                                        <label style="font-size:15px;margin-top: -2px;" class="block font-medium text-gray-700 mb-1">EXPENSES</label>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">START DATE</label>
                                        <div class="relative">
                                            <input
                                                id="shift-start-input"
                                                type="date"
                                                class="block w-44 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            >
                                         
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">END DATE</label>
                                        <div class="relative">
                                            <input
                                                id="shift-end-input"
                                                type="date"
                                                class="block w-44 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            >
                                           
                                        </div>
                                    </div>

                                    <div class="pt-5">
                                        <button
                                            id="clear-date-filter"
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                        >
                                            Clear
                                        </button>
                                    </div>
                                </div>

                                <hr>

                    <table style="width: 100%;">
                        <thead style="background-color: #151a2d;color: white;padding:20px">
                            <tr>
                                 <th style="padding: 20px;" class="text-left text-xs font-medium uppercase">
                                    <input type="checkbox" id="include-master-checkbox">
                                </th>
                                <th class="text-left text-xs font-medium uppercase">Client</th>
                                <th class="text-left text-xs font-medium uppercase">Total Shifts</th>
                                <th class="text-left text-xs font-medium uppercase">To</th>
                                <th class="text-left text-xs font-medium uppercase">Purchase Order</th>
                                <th class="text-left text-xs font-medium uppercase">Due At</th>
                                <th class="text-left text-xs font-medium uppercase">
                                                <input type="checkbox" id="tax-master-checkbox"> Tax
                                            </th>
                                <th class="text-left text-xs font-medium uppercase">Total Cost</th>
                                <th class="text-left text-xs font-medium uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="clients-table-body">
                             @foreach($this->clients as $client)
                                            {{-- Create JSON for JS: ensure date is string (Y-m-d) and amounts are numbers --}}
                                            @php
                                                $billingForJs = $client->billingReports->map(function ($r) {
                                                    return [
                                                        'date' => (string) $r->date,              // e.g. "2025-09-18"
                                                        'amount' => (float) $r->total_cost,
                                                        'status' => $r->status,
                                                    ];
                                                })->values();
                                            $amount = $client->unpaid_total_cost ?? 0;
                                            @endphp

                                            <tr data-client-id="{{ $client->id }}"
                                                data-billing-reports='@json($billingForJs)'>
                                                                    
                                     <td class="py-4" style="text-align: center;">
                                                <input type="checkbox" class="include-checkbox">    
                                            </td>
                                <td class=" py-4" style="font-size:13px;">{{ $client->display_name }}</td>

                                <td class=" py-4" style="font-size:13px;">
                                <a href="{{ url('/admin/billing-reports-client') }}?client_id={{ $client->id }}" target="_blank">
                                    <span class="badge badge--info" role="status" aria-label="Info">
                                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" focusable="false">
                                    <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-9-1a1 1 0 10-2 0v5a1 1 0 102 0V9zm0-4a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $client->not_paid_reports_count }} View Reports
                                </span>
                                </a>
                                </td>
                                <td class=" py-4" style="font-size:13px;">
                                    <select style="font-size: 12px;width: 150px;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                                        <option value="Client">Client</option>
                                     @if($client->additionalContacts->isNotEmpty())
                                      @foreach($client->additionalContacts as $contact)
                                        <option value="{{ $contact->id }}">{{ $contact->first_name }} {{ $contact->last_name }}</option>
                                          @endforeach
                                       @endif
                                    </select> 
                                </td>
                                <td class=" py-4" style="font-size:13px;">
                                    <input type="text" placeholder="Enter Purchase Order" name="" id="" style="font-size: 12px;width: 150px;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm"> 
                                </td>
                                <td class=" py-4" style="font-size:13px;">
                                    <input type="date" value="{{ now()->addDays(14)->format('Y-m-d') }}"  name="" id="purchase-order-date" style="font-size: 12px;width: 150px;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm"> 
                                </td>
                                 <td class="py-4">
                                        <input type="checkbox"
                                            class="tax-checkbox"
                                            data-base="{{ $amount }}">
                                    </td>

                                    <!-- Amount column -->
                                    <td class="py-4">
                                        $<span class="amount" style="font-size:13px;">{{ number_format($amount, 2) }}</span>
                                    </td>
                                
                                {{-- Include/Exclude checkbox --}}
                                <td class=" py-4" style="font-size:13px;">
                                      <span class="badge badge--warning badge--xs" role="status" aria-label="Pending">
                                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" focusable="false">
                                            <path d="M9.401 2.003a1 1 0 01.198 0l7.5 1.25A1 1 0 0118.5 4.25v9.5a2 2 0 01-1.5 1.94l-7.5 1.25a1 1 0 01-.398 0l-7.5-1.25A2 2 0 01.5 13.75v-9.5A1 1 0 011.901 3.253l7.5-1.25z"/>
                                            </svg>
                                            Ready To Invoiced
                                        </span>
                                </td>
                            </tr>
                           @endforeach
                           
                        </tbody>
                    </table>
                
                </div>

<script>
document.getElementById('generate-btn').addEventListener('click', function () {
    const rows = document.querySelectorAll("tbody tr");
    let selectedClients = [];

    rows.forEach(row => {
        let include = row.querySelector(".include-checkbox")?.checked;
        if (include) {
            selectedClients.push({
                id: row.dataset.clientId,
                additional_contact_id: row.querySelector("select")?.value !== "Client" ? row.querySelector("select").value : null,
                issue_date: row.querySelector("input[type='date']").value,
                payment_due: row.querySelector("input[type='date']").value,
                ref_no: row.querySelector("input[type='text']").value,
                tax_checked: row.querySelector(".tax-checkbox")?.checked,
            });
        }
    });

    if (selectedClients.length > 0) {
        Livewire.dispatch('generateInvoices', { selectedClients });
    }
});
</script>
          <script>
document.addEventListener("DOMContentLoaded", function () {
    const taxMaster = document.getElementById("tax-master-checkbox");
    const includeMaster = document.getElementById("include-master-checkbox");

    const taxCheckboxes = document.querySelectorAll(".tax-checkbox");
    const includeCheckboxes = document.querySelectorAll(".include-checkbox");

    const generateContainer = document.getElementById("generate-container");

    // Update amount for a tax checkbox
    function updateAmount(checkbox) {
        const base = parseFloat(checkbox.dataset.base);
        const amountSpan = checkbox.closest("tr").querySelector(".amount");

        if (checkbox.checked) {
            amountSpan.textContent = (base * 1.10).toFixed(2);
        } else {
            amountSpan.textContent = base.toFixed(2);
        }
    }

    // Show/hide generate button
   function toggleGenerateButton() {
    const anyChecked = Array.from(document.querySelectorAll(".include-checkbox")).some(cb => cb.checked);
    document.getElementById("generate-btn").style.display = anyChecked ? "inline-flex" : "none";
        }

        // Attach event listener
        document.querySelectorAll(".include-checkbox").forEach(cb => {
            cb.addEventListener("change", toggleGenerateButton);
        });


    // Master Tax checkbox
    taxMaster.addEventListener("change", function () {
        taxCheckboxes.forEach(cb => {
            cb.checked = taxMaster.checked;
            updateAmount(cb);
        });
    });

    // Master Include checkbox
    includeMaster.addEventListener("change", function () {
        includeCheckboxes.forEach(cb => {
            cb.checked = includeMaster.checked;
        });
        toggleGenerateButton();
    });

    // Individual Tax checkboxes
    taxCheckboxes.forEach(cb => {
        cb.addEventListener("change", function () {
            updateAmount(cb);
        });
        updateAmount(cb); // run once
    });

    // Individual Include checkboxes
    includeCheckboxes.forEach(cb => {
        cb.addEventListener("change", function () {
            toggleGenerateButton();
        });
    });
});
</script>
    <script>
          document.addEventListener('DOMContentLoaded', function () {
        if (!window.initCustomDatePicker) return;

        ['purchase-order-date','shift-start-input','shift-end-input'].forEach(function (id) {
            window.initCustomDatePicker(id);
        });
    });
        document.addEventListener('DOMContentLoaded', function () {
            if (window.initCustomDatePicker) {
                window.initCustomDatePicker('purchase-order-date');
            } else {
                console.warn('initCustomDatePicker is not defined yet.');
            }
        });
    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('shift-start-input');
    const endInput   = document.getElementById('shift-end-input');
    const clearBtn   = document.getElementById('clear-date-filter');
    const rows       = Array.from(document.querySelectorAll('#clients-table-body tr[data-billing-reports]'));

    // For native <input type="date">, value is already 'YYYY-MM-DD'
    function normalizeToYMD(value) {
        if (!value) return null;
        return value.trim(); // already correct
    }

    function filterRows() {
        const startYmd = normalizeToYMD(startInput.value);
        const endYmd   = normalizeToYMD(endInput.value);

        rows.forEach(row => {
            const raw = row.getAttribute('data-billing-reports');
            if (!raw) {
                row.style.display = 'none';
                return;
            }

            let reports = [];
            try {
                reports = JSON.parse(raw);
            } catch (e) {
                console.error('Invalid JSON in data-billing-reports', e);
                row.style.display = 'none';
                return;
            }

            let totalAmount = 0;
            let reportsCount = 0;

            reports.forEach(rep => {
                const repDate = rep.date; // expected 'YYYY-MM-DD'
                if (!repDate) return;

                let inRange = true;

                if (startYmd && repDate < startYmd) inRange = false;
                if (endYmd && repDate > endYmd) inRange = false;

                if (inRange) {
                    totalAmount += Number(rep.amount || 0);
                    reportsCount++;
                }
            });

            const amountSpan = row.querySelector('.amount');
            if (amountSpan) amountSpan.textContent = totalAmount.toFixed(2);

            const countSpan = row.querySelector('.reports-count');
            if (countSpan) countSpan.textContent = reportsCount;

            row.style.display = reportsCount > 0 ? '' : 'none';
        });
    }

    if (startInput) startInput.addEventListener('change', filterRows);
    if (endInput)   endInput.addEventListener('change', filterRows);
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            startInput.value = '';
            endInput.value = '';
            filterRows();
        });
    }

    // initial
    filterRows();
});
</script>



</x-filament-panels::page>

@push('styles')
    <style>
        .fi-fo-grid { /* Tighten form grid spacing */
            gap: 0.5rem;
        }
        .filament-toggle { /* Style toggles like buttons */
            display: inline-flex !important;
        }
        .filament-table-container { 
            border: 1px solid #e5e7eb; 
            margin-top: 1rem;
        }
    </style>
@endpush

