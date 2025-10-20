@php
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\DocumentCategory;

$user = Auth::user();
$companyId = Company::where('user_id', $user->id)->value('id');
$competencies = DocumentCategory::where('company_id', $companyId)
    ->where('is_staff_doc', 1)
    ->where('is_competencies',1)
    ->get();

$qualifications = DocumentCategory::where('company_id', $companyId)
->where('is_staff_doc', 1)
->where('is_qualifications',1)
->get();
@endphp


            <div class="tags-container">
                <div style="width: 100%;
                            font-size: 12px;
                            font-weight: 500;
                            margin-bottom: 15px;
                            margin-top: 15px;" class="data-label">competency:</div>
                @foreach($competencies as $document)
                <div class="category-tag">{{ $document?->name ?? '-' }}</div>
                @endforeach

                <div style="width: 100%;
                            font-size: 12px;
                            font-weight: 500;
                            margin-bottom: 15px;
                            margin-top: 15px;" class="data-label">qualification:</div>
                @foreach($qualifications as $document)
                <div class="category-tag">{{ $document?->name ?? '-' }}</div>
                @endforeach
            </div>
