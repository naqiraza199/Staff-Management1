<x-filament-panels::page>

<!-- Invoice Settings UI (pure HTML/CSS/JS) -->
<div class="uiinv-root">
<meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
  /* ---------- Core layout ---------- */
  .uiinv-root { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; color: #0f172a; }

  .uiinv-grid { display: grid; grid-template-columns: 1fr 430px; gap: 22px; align-items: start; }

  /* ---------- Card ---------- */
  .uiinv-card, .uiinv-card--small {
    background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(250,250,255,0.9));
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15,23,42,0.04);
  }
.uiinv-card {
    width: 100%;
}
  .uiinv-card h3 { display:flex; align-items:center; justify-content:space-between; gap:12px; margin:0 0 14px 0; font-size:1.15rem; font-weight:600; color:#0b1220; }

  .uiinv-actions { display:flex; gap:10px; align-items:center; }

  .uiinv-btn {
    display:inline-flex; align-items:center; gap:8px; padding:4px 12px; border-radius:10px; border:0; cursor:pointer; font-weight:600;
    background:linear-gradient(135deg,#4f46e5,#2563eb); color:white; box-shadow:0 6px 18px rgba(37,99,235,0.14);
  }
  .uiinv-btn.secondary {
    background: #0eabbb9c;
    color: #ffffff;
    border: 1px solid rgba(11, 17, 32, 0.06);
    box-shadow: none;
    font-size: 16px;
    border-radius: 6px;
   }
  .uiinv-btn.ghost { background:transparent; color:#2563eb; border:0; box-shadow:none; font-weight:600; }

  /* ---------- Info list ---------- */
  .uiinv-info { display:grid; grid-template-columns: 1fr 1fr; gap:12px 24px; align-items:center; }
  .uiinv-key {
    font-weight: 600;
    color: #334155;
    margin-top: 12px;
    font-size: 15px;
  }
  .uiinv-value { 
    text-align: right;
    font-size: 13px;
    color: #475569;
    opacity: 0.95;
   }

  /* ---------- Small right-card (Taxes) ---------- */
  .uiinv-card--small h3 { font-size:1rem; margin-bottom:12px; }
  .uiinv-table { width:100%; border-collapse:collapse; font-size:0.92rem; }
  .uiinv-table th { text-align:left; color:#64748b; font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; padding-bottom:8px; }
  .uiinv-table td { padding:10px 6px; border-bottom:1px solid rgba(15,23,42,0.04); color:#0f172a; }

  /* ---------- Modal common ---------- */
  .uiinv-modal-backdrop { position:fixed; inset:0; display:flex; align-items:flex-start; justify-content:center; padding:40px 18px; z-index:1200; backdrop-filter: blur(6px); transition:opacity .22s ease; }
  .uiinv-modal-backdrop.hidden { display:none; opacity:0; pointer-events:none; }
  .uiinv-modal-panel { background:linear-gradient(180deg,#fff,#fbfdff); border-radius:12px; width:100%; max-width:920px; box-shadow:0 18px 50px rgba(2,6,23,0.28); border:1px solid rgba(2,6,23,0.06); transform:translateY(-6px); transition:transform .18s ease, opacity .18s ease; }
  .uiinv-modal-body { padding:20px 22px 22px 22px; max-height:72vh; overflow:auto; }
  .uiinv-modal-header { padding:18px 22px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(15,23,42,0.04); }
  .uiinv-modal-title { font-weight:700; font-size:1.05rem; color:#0b1220; }
  .uiinv-close { background:transparent; border:0; padding:8px; border-radius:8px; cursor:pointer; color:#475569; }
  .uiinv-close:hover { background:rgba(15,23,42,0.04); }

  /* ---------- Form ---------- */
.uiinv-form { 
  display: grid;
  gap: 12px;
  grid-template-columns: repeat(2, 1fr);
  padding: 25px;
  
  /* Add scrollbar */
  max-height: 700px;       /* Adjust height as needed */
  overflow-y: auto;         /* Enables vertical scrollbar */
  scrollbar-width: thin;    /* For Firefox */
  scrollbar-color: #ccc #f5f5f5; /* Thumb and track colors */
}

/* Optional: for WebKit browsers (Chrome, Edge, Safari) */
.uiinv-form::-webkit-scrollbar {
  width: 8px;
}

.uiinv-form::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 4px;
}

.uiinv-form::-webkit-scrollbar-track {
  background-color: #f5f5f5;
}

  .uiinv-field { display:flex; flex-direction:column; gap:6px; }
  .uiinv-field.full { grid-column:1 / -1; }
  .uiinv-label { font-size:0.86rem; color:#334155; font-weight:600; }
  .uiinv-input, .uiinv-textarea, .uiinv-select {
    padding:10px 12px; border-radius:8px; border:1px solid rgba(15,23,42,0.06); background:#fff; font-size:0.95rem; color:#0b1220;
    outline:none; transition:box-shadow .12s ease, border-color .12s ease;
  }
  .uiinv-input:focus, .uiinv-textarea:focus, .uiinv-select:focus { box-shadow:0 8px 20px rgba(37,99,235,0.08); border-color:rgba(37,99,235,0.2); }

  .uiinv-textarea { min-height:90px; resize:vertical; }

  .uiinv-submit-row { display:flex; justify-content:flex-end; gap:10px; padding:14px 22px 22px; border-top:1px solid rgba(15,23,42,0.04); }

  .uiinv-small { font-size:0.86rem; color:#64748b; }

  /* ---------- Toggles ---------- */
  .uiinv-toggle { display:inline-flex; align-items:center; gap:8px; cursor:pointer; }
  .uiinv-toggle input[type="checkbox"] { width:0; height:0; opacity:0; position:absolute; }
  .uiinv-switch { width:42px; height:24px; background:#e6eefc; border-radius:999px; position:relative; display:inline-block; transition:all .18s ease; }
  .uiinv-switch::after { content:""; position:absolute; left:4px; top:4px; width:16px; height:16px; background:#fff; border-radius:50%; box-shadow:0 4px 10px rgba(2,6,23,0.08); transition:transform .18s ease; }
  .uiinv-toggle input[type="checkbox"]:checked + .uiinv-switch { background:linear-gradient(135deg,#4f46e5,#2563eb); }
  .uiinv-toggle input[type="checkbox"]:checked + .uiinv-switch::after { transform:translateX(18px); }

  /* ---------- Toast ---------- */
  .uiinv-toast { position:fixed; right:24px; bottom:24px; background:#10b981; color:#fff; padding:10px 14px; border-radius:10px; box-shadow:0 10px 30px rgba(16,185,129,0.12); z-index:1400; opacity:0; transform:translateY(12px); transition:all .22s ease; }
  .uiinv-toast.show { opacity:1; transform:translateY(0); }

  /* ---------- Responsive ---------- */
  @media (max-width:980px) { .uiinv-grid { grid-template-columns: 1fr; } .uiinv-form { grid-template-columns: 1fr; } .uiinv-value { text-align:left; } .uiinv-card--small { order: -1; } }
  .uiinv-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 8px;
  background-color: transparent;
  font-family: 'Inter', sans-serif;
}

.uiinv-table thead {
  background: #f8fafc;
}

.uiinv-table th {
  text-align: left;
  font-weight: 600;
  font-size: 14px;
  color: #334155;
  padding: 12px 16px;
  border-bottom: 2px solid #e2e8f0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.uiinv-table tbody tr {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  transition: all 0.2s ease;
}

.uiinv-table tbody tr:hover {
  background: #f9fafb;
  transform: translateY(-1px);
}

.uiinv-table td {
  padding: 12px 16px;
  font-size: 14px;
  color: #475569;
  border-bottom: 1px solid #f1f5f9;
}

.uiinv-table tbody tr:last-child td {
  border-bottom: none;
}

.uiinv-btn.ghost {
  background: transparent;
  border: 1px solid #cbd5e1;
  color: #1e293b;
  padding: 6px 14px;
  font-size: 13px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.uiinv-btn.ghost:hover {
  background: #1d4ed8;
  color: #fff;
  border-color: #1d4ed8;
  box-shadow: 0 2px 6px rgba(29,78,216,0.2);
}
<style>
  .uiinv-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
  }
  .uiinv-modal-panel.small {
    background: #fff;
    border-radius: 12px;
    width: 400px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    animation: fadeIn .2s ease;
  }
  .uiinv-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }
  .uiinv-modal-title {
    font-size: 18px;
    font-weight: 600;
  }
  .uiinv-close {
    background: none;
    border: none;
    cursor: pointer;
  }
  @keyframes fadeIn {
    from {opacity:0; transform:scale(0.95);}
    to {opacity:1; transform:scale(1);}
  }
</style>
  
  </style>

  <div class="uiinv-grid">

    <!-- Left: Invoice Card -->
    <div class="uiinv-card">
      <h3>
        Invoices
       @php
            $company = \App\Models\Company::where('user_id', auth()->id())->first();
            $record = $company ? \App\Models\InvoiceSetting::where('company_id', $company->id)->first() : null;
        @endphp

        <div class="flex justify-end mb-4">
            @if ($record)
                <button class="uiinv-btn" id="open-edit-modal">Edit</button>
            @else
                <button class="uiinv-btn" id="open-create-modal">Add</button>
            @endif
        </div>
      </h3>
      <hr style="margin-bottom: 30px;"> 

      <div class="uiinv-info" aria-live="polite">
        <div class="uiinv-key">ABN</div>
        <div class="uiinv-value" data-key="abn">{{ $invoiceSetting->abn ?? '—' }}</div>

        <div class="uiinv-key">Address</div>
        <div class="uiinv-value" data-key="address">{{ $invoiceSetting->address ?? '—' }}</div>

        <div class="uiinv-key">Phone</div>
        <div class="uiinv-value" data-key="phone">{{ $invoiceSetting->phone ?? '—' }}</div>

        <div class="uiinv-key">Payment Terms</div>
        <div class="uiinv-value" data-key="payment_terms">{{ $invoiceSetting->payment_terms ?? '—' }}</div>

        <div class="uiinv-key">Contact Email</div>
        <div class="uiinv-value" data-key="contact_email">{{ $invoiceSetting->contact_email ?? '—' }}</div>

        <div class="uiinv-key">Email Message</div>
        <div class="uiinv-value" data-key="email_message">{{ $invoiceSetting->email_message ?? '—' }}</div>

        <div class="uiinv-key">Payment Rounding</div>
        <div class="uiinv-value" data-key="payment_rounding">{{ $invoiceSetting->payment_rounding ?? '—' }}</div>

        <div class="uiinv-key">NDIA Provider Number</div>
        <div class="uiinv-value" data-key="ndia_provider_number">{{ $invoiceSetting->ndia_provider_number ?? '—' }}</div>

        <div class="uiinv-key">Cost Calculation</div>
        <div class="uiinv-value" data-key="cost_calculation_is_based_on">{{ $invoiceSetting->cost_calculation_is_based_on ?? '—' }}</div>

        <div class="uiinv-key">Cancelled by Client Label</div>
        <div class="uiinv-value" data-key="cancelled_by_client_label">{{ $invoiceSetting->cancelled_by_client_label ?? '—' }}</div>

        <div class="uiinv-key">Cancel Message</div>
        <div class="uiinv-value" data-key="cancel_message">{{ $invoiceSetting->cancel_message ?? '—' }}</div>

        <div class="uiinv-key">Invoice Item Default Format</div>
        <div class="uiinv-value" data-key="invoice_item_default_format">{{ $invoiceSetting->invoice_item_default_format ?? '—' }}</div>

        <div class="uiinv-key">Default Invoice Due Days</div>
        <div class="uiinv-value" data-key="default_invoice_due_days">{{ $invoiceSetting->default_invoice_due_days ?? '—' }}</div>

      </div>
    </div>

    <!-- Right: Taxes Card (small) -->
   <div class="uiinv-card--small">
  <div style="display:flex;justify-content: space-between;align-items:center;">
    <h3>Taxes</h3>
    @if(!$tax)
      <button class="uiinv-btn ghost" style="padding:8px 10px;" id="uiinv-add-tax">Add</button>
    @endif
  </div>

  <hr style="margin-top:20px;margin-bottom:20px">

  <table class="uiinv-table" role="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Rate</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($taxes as $tax)
        <tr>
          <td>{{ $tax->name }}</td>
          <td>{{ $tax->rate }}%</td>
          <td>
            <button class="uiinv-btn ghost uiinv-edit-tax" data-id="{{ $tax->id }}" data-name="{{ $tax->name }}" data-rate="{{ $tax->rate }}">
              Edit
            </button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="3" style="text-align:center;color:gray;">No taxes found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- Tax Modal -->
<div class="uiinv-modal-backdrop hidden" id="uiinv-tax-modal">
  <div class="uiinv-modal-panel small">
    <div class="uiinv-modal-header">
      <h4 class="uiinv-modal-title" id="uiinv-tax-modal-title">Add Tax</h4>
      <button class="uiinv-close" id="uiinv-tax-close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <form id="uiinv-tax-form" method="POST" action="{{ route('filament.taxes.save') }}">
      @csrf
      <input type="hidden" name="id" id="tax_id">

      <div class="uiinv-form">
        <div class="uiinv-field">
          <label class="uiinv-label">Name</label>
          <input name="name" id="tax_name" class="uiinv-input" required>
        </div>

        <div class="uiinv-field">
          <label class="uiinv-label">Rate (%)</label>
          <input name="rate" id="tax_rate" class="uiinv-input" type="number" step="0.01" required>
        </div>
      </div>

      <div class="uiinv-submit-row">
        <button type="button" class="uiinv-btn ghost" id="uiinv-tax-cancel">Cancel</button>
        <button class="uiinv-btn" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

  {{-- ✅ Create Modal --}}
<div class="uiinv-modal-backdrop uiinv-create-modal hidden">
    <div class="uiinv-modal-panel">
        <div class="uiinv-modal-header">
            <div class="uiinv-modal-title">Create Invoice Settings</div>
            <button class="uiinv-close" data-close-modal>&times;</button>
        </div>

        <form class="uiinv-form" action="{{ route('invoice-settings.store') }}" method="POST">
            @csrf

            <div class="uiinv-field"><label>ABN</label><input name="abn" class="uiinv-input" required /></div>
            <div class="uiinv-field"><label>Address</label><input name="address" class="uiinv-input" required /></div>
            <div class="uiinv-field"><label>Phone</label><input name="phone" class="uiinv-input" required /></div>
            <div class="uiinv-field"><label>Payment Terms</label><input name="payment_terms" class="uiinv-input" required /></div>
            <div class="uiinv-field"><label>Contact Email</label><input name="contact_email" type="email" class="uiinv-input" required /></div>
            <div class="uiinv-field"><label>Email Message</label><textarea name="email_message" class="uiinv-textarea"></textarea></div>

            <div class="uiinv-field"><label>Payment Rounding</label><input name="payment_rounding" class="uiinv-input" value="decimal" /></div>
            <div class="uiinv-field"><label>NDIA Provider Number</label><input name="ndia_provider_number" class="uiinv-input" /></div>
            <div class="uiinv-field"><label>Cost Calculation</label><input name="cost_calculation_is_based_on" class="uiinv-input" value="end_time" /></div>
            <div class="uiinv-field"><label>Cancelled by Client Label</label><input name="cancelled_by_client_label" class="uiinv-input" /></div>
            <div class="uiinv-field"><label>Cancel Message</label><textarea name="cancel_message" class="uiinv-textarea"></textarea></div>
            <div class="uiinv-field"><label>Invoice Item Default Format</label><input name="invoice_item_default_format" class="uiinv-input" /></div>
            <div class="uiinv-field"><label>Default Invoice Due Days</label><input type="number" name="default_invoice_due_days" class="uiinv-input" value="14" /></div>

           
            <div class="uiinv-submit-row">
                <button type="button" class="uiinv-btn-cancel" data-close-modal>Cancel</button>
                <button type="submit" class="uiinv-btn">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Edit Modal --}}
@if ($record)
<div class="uiinv-modal-backdrop uiinv-edit-modal hidden">
    <div class="uiinv-modal-panel">
        <div class="uiinv-modal-header">
            <div class="uiinv-modal-title">Edit Invoice Settings</div>
            <button class="uiinv-close" data-close-modal>&times;</button>
        </div>

        <form class="uiinv-form" action="{{ route('invoice-settings.update', $record->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="uiinv-field"><label>ABN</label><input name="abn" class="uiinv-input" value="{{ $record->abn }}" /></div>
            <div class="uiinv-field"><label>Address</label><input name="address" class="uiinv-input" value="{{ $record->address }}" /></div>
            <div class="uiinv-field"><label>Phone</label><input name="phone" class="uiinv-input" value="{{ $record->phone }}" /></div>
            <div class="uiinv-field"><label>Payment Terms</label><input name="payment_terms" class="uiinv-input" value="{{ $record->payment_terms }}" /></div>
            <div class="uiinv-field"><label>Contact Email</label><input name="contact_email" class="uiinv-input" type="email" value="{{ $record->contact_email }}" /></div>
            <div class="uiinv-field"><label>Email Message</label><textarea name="email_message" class="uiinv-textarea">{{ $record->email_message }}</textarea></div>

            <div class="uiinv-field"><label>Payment Rounding</label><input name="payment_rounding" class="uiinv-input" value="{{ $record->payment_rounding }}" /></div>
            <div class="uiinv-field"><label>NDIA Provider Number</label><input name="ndia_provider_number" class="uiinv-input" value="{{ $record->ndia_provider_number }}" /></div>
            <div class="uiinv-field"><label>Cost Calculation</label><input name="cost_calculation_is_based_on" class="uiinv-input" value="{{ $record->cost_calculation_is_based_on }}" /></div>

            <div class="uiinv-field"><label>Cancelled by Client Label</label><input name="cancelled_by_client_label" class="uiinv-input" value="{{ $record->cancelled_by_client_label }}" /></div>
            <div class="uiinv-field"><label>Cancel Message</label><textarea name="cancel_message" class="uiinv-textarea">{{ $record->cancel_message }}</textarea></div>
            <div class="uiinv-field"><label>Invoice Item Default Format</label><input name="invoice_item_default_format" class="uiinv-input" value="{{ $record->invoice_item_default_format }}" /></div>
            <div class="uiinv-field"><label>Default Invoice Due Days</label><input type="number" name="default_invoice_due_days" class="uiinv-input" value="{{ $record->default_invoice_due_days }}" /></div>


            <div class="uiinv-submit-row">
                <button type="button" class="uiinv-btn-cancel" data-close-modal>Cancel</button>
                <button type="submit" class="uiinv-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endif

  <!-- Toast -->
  <div class="uiinv-toast" id="uiinv-toast" role="status" aria-live="polite"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const createModal = document.querySelector(".uiinv-create-modal");
    const editModal = document.querySelector(".uiinv-edit-modal");
    document.getElementById("open-create-modal")?.addEventListener("click", () => createModal.classList.remove("hidden"));
    document.getElementById("open-edit-modal")?.addEventListener("click", () => editModal.classList.remove("hidden"));
    document.querySelectorAll("[data-close-modal]").forEach(btn => {
        btn.addEventListener("click", () => {
            createModal?.classList.add("hidden");
            editModal?.classList.add("hidden");
        });
    });
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.querySelector('#uiinv-add-tax');
    const modal = document.querySelector('#uiinv-tax-modal');
    const closeBtn = document.querySelector('#uiinv-tax-close');
    const cancelBtn = document.querySelector('#uiinv-tax-cancel');
    const form = document.querySelector('#uiinv-tax-form');
    const title = document.querySelector('#uiinv-tax-modal-title');
    const idField = document.querySelector('#tax_id');
    const nameField = document.querySelector('#tax_name');
    const rateField = document.querySelector('#tax_rate');

    // open add modal
    if (addBtn) {
      addBtn.addEventListener('click', () => {
        title.textContent = 'Add Tax';
        form.reset();
        idField.value = '';
        modal.classList.remove('hidden');
      });
    }

    // edit modal open
    document.querySelectorAll('.uiinv-edit-tax').forEach(btn => {
      btn.addEventListener('click', () => {
        title.textContent = 'Edit Tax';
        idField.value = btn.dataset.id;
        nameField.value = btn.dataset.name;
        rateField.value = btn.dataset.rate;
        modal.classList.remove('hidden');
      });
    });

    // close modal
    [closeBtn, cancelBtn].forEach(el => {
      if (el) el.addEventListener('click', () => modal.classList.add('hidden'));
    });
  });
</script>
</div>



</x-filament-panels::page>
