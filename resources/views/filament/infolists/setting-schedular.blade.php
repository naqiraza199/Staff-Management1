@php

use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\ClientType;

$user = Auth::user();
$company = Company::where('user_id', $user->id)->first();

$clientTypes = ClientType::where('company_id',$company->id)->get();
@endphp

<div class="data-grid">
    <div class="data-label">Timezone:</div>
    <div class="data-value">
        {{ $company?->timezone ?? '-' }}
    </div>

    <div class="data-label">Minute Interval:</div>
    <div class="data-value">
        {{ $company?->minute_interval ?? '-' }}
    </div>

    <div class="data-label">Pay run:</div>
    <div class="data-value">
        {{ $company?->pay_run ?? '-' }}
    </div>
</div>

   <div class="tags-container">
                <div style="width: 100%;
                            font-size: 12px;
                            font-weight: 500;
                            margin-bottom: 15px;
                            margin-top: 15px;" class="data-label">Client Types:</div>
    @foreach($clientTypes as $type)
                <div class="category-tag">{{ $type?->name ?? '-' }}</div>
    @endforeach
</div>