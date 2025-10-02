<x-filament-panels::page>
<style>
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

    .status-badge:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    .status-success { background: #E8F6F9; }  /* Green */
    .status-warning { background: #f2e8f9ff;  }  /* Yellow */
    .status-danger  { background: #E3F5ED; }  /* Red */
    .status-info    { background: #FFEFDB; }  /* Teal */
    .status-primary { background: #e2edc7; }  /* Blue */
</style>  
@php
    $totals = $this->getTotals();
@endphp

<div class="status-badge-row">
    <div class="status-badge status-success">
        <div>Invoiced</div>
        <div>${{ number_format($totals['grandTotal'], 2) }}</div>
    </div>
    <div class="status-badge status-warning">
        <div>Tax</div>
        <div>${{ number_format($totals['totalTax'], 2) }}</div>
    </div>
    <div class="status-badge status-danger">
        <div>Paid</div>
        <div>${{ number_format($totals['paidAmount'], 2) }}</div>
    </div>
    <div class="status-badge status-info">
        <div>Unpaid</div>
        <div>${{ number_format($totals['unpaidOverdueBalance'], 2) }}</div>
    </div>
    <div class="status-badge status-primary">
        <div>Overdue</div>
        <div>${{ number_format($totals['overdueBalance'], 2) }}</div>
    </div>
</div>


    {{ $this->table }}
</x-filament-panels::page>
