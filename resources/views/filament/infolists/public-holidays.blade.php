@php

use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\PublicHoliday;

$user = Auth::user();
$company = Company::where('user_id', $user->id)->first();

$publicHolidays = PublicHoliday::where('company_id',$company->id)->get();
@endphp


<div class="tags-container">
    @foreach($publicHolidays as $holiday)
        <div class="category-tag" style="background-color:#daf7c9">
            {{ \Carbon\Carbon::parse($holiday->date)->format('D - j M Y') }}
        </div>
    @endforeach
</div>
