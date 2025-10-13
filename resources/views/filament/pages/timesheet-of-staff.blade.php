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
    color: #ffffffff;
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

    .status-success { background: #3C8DBC; }  /* Green */
    .status-warning { background: #00A65A;  }  /* Yellow */
    .status-danger  { background: #DD4B39; }  /* Red */
    .status-info    { background: #00A65A; }  /* Teal */
    .status-primary { background: #3C8DBC; }  /* Blue */
</style> 

    @php
        $totals = $this->getBadgeTotals();
    @endphp

<div class="status-badge-row">
    <div class="status-badge status-success">
        <div>Sleepover</div>
        <div>{{ $totals['sleepover'] }}</div>
    </div>
    <div class="status-badge status-warning">
        <div>Mileage</div>
        <div>{{ $totals['mileage'] }}</div>
    </div>
    <div class="status-badge status-danger">
        <div>Expenses</div>
        <div>${{ $totals['expense'] }}</div>
    </div>
    <div class="status-badge status-info">
        <div>Approved</div>
        <div>{{ number_format($totals['approved'], 2) }} hrs</div>
    </div>
    <div class="status-badge status-primary">
        <div>Total</div>
        <div>{{ number_format($totals['total'], 2) }} hrs</div>
    </div>
</div>
    {{ $this->table }}
</x-filament-panels::page>


