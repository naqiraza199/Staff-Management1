<style>
    .data-grid {
        display: grid;
        grid-template-columns: 3fr 2fr !important;
        gap: 15px 20px;
    }
      .data-label {
            font-weight: 400;
            color: var(--color-text-light);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-value {
            font-weight: 600;
            color: var(--color-text-dark);
            font-size: 12px;
            overflow-wrap: break-word; 
        }
</style>
@php

use Illuminate\Support\Facades\Auth;
use App\Models\Company;

$user = Auth::user();
$company = Company::where('user_id', $user->id)->first();

@endphp
<div class="data-grid">
    <div class="data-label">Quote Title</div>
    <div class="data-value">
        {{ $company?->quote_title ?? '-' }}
    </div>

    <div class="data-label">Default Quote Terms</div>
    <div class="data-value">
        {{ $company?->quote_terms ?? '-' }}
    </div>

   
</div>