<x-filament-panels::page>
    <style>
    .stats-grid {
    display: flex;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
        width: 370px;
}

.stat-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Titles */
.stat-title {
    font-size: 14px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Main value */
.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #222;
    margin-top: 10px;
    animation: fadeIn 0.8s ease;
}

/* Sub text */
.stat-sub {
    margin-top: 12px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
}

/* Icons */
.stat-sub .icon {
    margin-right: 8px;
    font-size: 18px;
    display: inline-block;
    animation: bounce 2s infinite;
}

/* Colors */
.stat-card.green {
    border-left: 5px solid #28a745;
}
.stat-card.green .stat-sub {
    color: #28a745;
}

.stat-card.blue {
    border-left: 5px solid #1a88d7;
}
.stat-card.blue .stat-sub {
    color: #1a88d7;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

/* Paid status row styling */
.paid-row {
    background-color: #f0fdf4 !important;
    border-left: 4px solid #22c55e !important;
}

.paid-row td {
    background-color: transparent !important;
}

/* More specific targeting for Filament table rows */
.fi-ta-row.paid-row {
    background-color: #f0fdf4 !important;
    border-left: 4px solid #22c55e !important;
}

.fi-ta-row.paid-row td {
    background-color: transparent !important;
}

/* Even more specific targeting */
.fi-ta tbody .fi-ta-row.paid-row {
    background-color: #f0fdf4 !important;
    border-left: 4px solid #22c55e !important;
}

.fi-ta tbody .fi-ta-row.paid-row td {
    background-color: transparent !important;
}

</style>


<div class="stats-grid">
    <!-- Total Cost -->
    <div class="stat-card green">
        <div class="stat-title">Total Cost</div>
        <div class="stat-value">
            ${{ number_format($this->getData()['totalCost'], 2) }}
        </div>
        <div class="stat-sub">
            <span class="icon">$</span>
            Total billing generated so far
        </div>
    </div>

    <!-- Total Hours -->
    <div class="stat-card blue">
        <div class="stat-title">Total Hours</div>
        <div class="stat-value">
            {{ number_format($this->getData()['totalHours'], 2) }} hrs
        </div>
        <div class="stat-sub">
            <span class="icon">⏱</span>
            Sum of worked hours across all shifts
        </div>
    </div>
</div>

    {{ $this->table }}
</x-filament-panels::page>
