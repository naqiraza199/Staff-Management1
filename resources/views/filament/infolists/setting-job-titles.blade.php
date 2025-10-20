@php

use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\JobTitle;

$user = Auth::user();
$company = Company::where('user_id', $user->id)->first();

$jobTitles = JobTitle::where('company_id',$company->id)->get();
@endphp


<div class="tags-container">
    @foreach($jobTitles as $job)
        <div class="category-tag" >
                    {{ $job?->name ?? '-' }}
        </div>
    @endforeach
</div>
