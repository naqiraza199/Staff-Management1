<style>
     /* --- Table Styling --- */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.9rem;
            text-align: left;
        }

        .data-table th, .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table th {
            font-weight: 600;
            color: #4b5563;
            background-color: #f9fafb;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.05em;
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background-color: #f3f4f6;
            cursor: pointer;
        }
        
        .data-table td a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .data-table td a:hover {
            text-decoration: underline;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            
            /* Specific 'Active' styling */
            background-color: #e3f2fd;
            color: #0d6efd;
            border: 1px solid #0d6efd;
        }
          .status-badge-yellow {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            
            /* Specific 'Active' styling */
            background-color: #fcfde3ff;
            color: #daac08ff;
            border: 1px solid #daac08ff;
        }

         .status-badge-green {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            
            /* Specific 'Active' styling */
            background-color: #e3fde3ff;
            color: #11a625ff;
            border: 1px solid #11a625ff;
        }

</style>
@php
use App\Models\User;
use App\Models\Company;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentCategory;
use App\Models\Document;
use Carbon\Carbon;

$authUser = Auth::user();

$companyId = Company::where('user_id', $authUser->id)->value('id');

if (!$companyId) {
    $query = User::query()->whereRaw('1=0'); 
} else {
    $staffUserIds = StaffProfile::where('company_id', $companyId)
        ->where('is_archive', 'Unarchive')
        ->pluck('user_id');

    $query = User::whereIn('id', $staffUserIds)
        ->role('staff');

    if (! $staffUserIds->contains($authUser->id)) {
        $query = User::whereIn('id', $staffUserIds->push($authUser->id))
            ->role('staff');
    }
}

$categories = DocumentCategory::query()
    ->where('is_staff_doc', 1)
    ->where('is_compliance', 1)
    ->get();
@endphp
@php
    $staff = $getRecord();
@endphp
<div class="overflow-x-auto">
    <table class="data-table w-full text-sm border-collapse">
        <thead>
            <tr>
                <th>Category</th>
                <th>Expires At</th>
                <th>Last Update</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                @php
                    $document = Document::where('user_id', $staff->id)
                        ->where('document_category_id', $category->id)
                        ->first();

                    if (!$document) {
                        $expiresAt = '-';
                    } elseif ($document->no_expiration) {
                        $expiresAt = 'No Expiration';
                    } else {
                        $expiresAt = $document->expired_at 
                            ? Carbon::parse($document->expired_at)->format('d/m/Y')
                            : '-';
                    }

                    $lastUpdate = $category->updated_at
                        ? Carbon::parse($category->updated_at)->format('d/m/Y')
                        : '-';
                @endphp

                <tr class="border-b hover:bg-gray-50 cursor-pointer" style="font-size: 13px;">
                    <td data-label="Category">{{ $category->name }}</td>
                    <td data-label="Expires At">{{ $expiresAt }}</td>
                    <td data-label="Last Update">{{ $lastUpdate }}</td>
                    <td data-label="Status"><span class="status-badge">{{ $category->status }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
