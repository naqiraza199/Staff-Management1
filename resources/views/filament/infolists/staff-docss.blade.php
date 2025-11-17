@php
use App\Models\User;
use App\Models\Company;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Str;

$authUser = Auth::user();
$staff = $getRecord();

$companyId = Company::where('user_id', $authUser->id)->value('id');

// ✅ Fetch documents as a collection
$documents = Document::where('user_id', $staff->id)->get();
@endphp

<div class="overflow-x-auto">
    <table class="data-table w-full text-sm border-collapse">
        <thead>
            <tr>
                <th>Type</th>
                <th>Category</th>
                <th>Document</th>
                <th>Expired</th>
                <th>No Expiration</th>
                <th>Last Update</th>
            </tr>
        </thead>
<tbody>
    @forelse($documents as $document)
        <tr class="border-b hover:bg-gray-50 cursor-pointer" style="font-size: 13px;">
            <td data-label="Type">{{ $document->type }}</td>
            <td data-label="Category">{{ $document->category ? $document->category->name : '-' }}</td>
            <td data-label="Document">{{ Str::after($document->name, 'documents/') }}</td>
            <td data-label="Expired">
                {{ $document->expired_at ? Carbon::parse($document->expired_at)->format('d M Y') : '-' }}
            </td>
            <td data-label="No Expiration">{{ $document->no_expiration ? 'Yes' : 'No' }}</td>
            <td data-label="Last Update">
                {{ $document->updated_at ? Carbon::parse($document->updated_at)->diffForHumans(['parts' => 2]) : '-' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-gray-500 py-4">
                No documents found for this staff.
            </td>
        </tr>
    @endforelse
</tbody>
    </table>
</div>
