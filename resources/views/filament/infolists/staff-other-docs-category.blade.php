@php
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\DocumentCategory;

$user = Auth::user();
$companyId = Company::where('user_id', $user->id)->value('id');
$compliances = DocumentCategory::where('company_id', $companyId)
    ->where('is_staff_doc', 1)
    ->where('is_compliance',1)
    ->get();

$kpis = DocumentCategory::where('company_id', $companyId)
->where('is_staff_doc', 1)
->where('is_kpi',1)
->get();

$others = DocumentCategory::where('company_id', $companyId)
->where('is_staff_doc', 1)
->where('is_other',1)
->get();
@endphp


            <div class="tags-container">
                <div style="width: 100%;
                            font-size: 12px;
                            font-weight: 500;
                            margin-bottom: 15px;
                            margin-top: 15px;" class="data-label">Compliance:</div>
                @foreach($compliances as $document)
                <div class="category-tag">{{ $document?->name ?? '-' }}</div>
                @endforeach

                <div style="width: 100%;
                            font-size: 12px;
                            font-weight: 500;
                            margin-bottom: 15px;
                            margin-top: 15px;" class="data-label">KPI:</div>
                @foreach($kpis as $document)
                <div class="category-tag">{{ $document?->name ?? '-' }}</div>
                @endforeach

                            <div style="width: 100%;
                            font-size: 12px;
                            font-weight: 500;
                            margin-bottom: 15px;
                            margin-top: 15px;" class="data-label">Other:</div>
                @foreach($others as $document)
                <div class="category-tag">{{ $document?->name ?? '-' }}</div>
                @endforeach
            </div>
