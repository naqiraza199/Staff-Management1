@php
    $staff = $getRecord();
    $payroll = \App\Models\StaffPayrollSetting::with('payGroup')->where('user_id', $staff->id)->first();
@endphp

<style>
    .data-grid {
        display: grid;
        grid-template-columns: 3fr 2fr !important;
        gap: 15px 20px;
    }
</style>

<div class="data-grid">
    <div class="data-label">Pay Group</div>
    <div class="data-value">
        {{ $payroll?->payGroup?->name ?? '-' }}
    </div>

    <div class="data-label">Allowances</div>
    <div class="data-value">
        {{ $payroll?->allowances ?? '-' }}
    </div>

    <div class="data-label">Daily Hours</div>
    <div class="data-value">
        {{ $payroll?->daily_hours ?? '-' }}
    </div>

    <div class="data-label">Weekly Hours</div>
    <div class="data-value">
        {{ $payroll?->weekly_hours ?? '-' }}
    </div>

    <div class="data-label">External System Identifier</div>
    <div class="data-value">
        {{ $payroll?->external_system_identifier ?? '-' }}
    </div>
</div>
