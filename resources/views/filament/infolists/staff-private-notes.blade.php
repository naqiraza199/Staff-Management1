@php
use App\Models\User;

    $staff = $getRecord();
    $user = User::where('id',$staff->id)->first();
@endphp

    <div class="data-label">Private Notes:</div>
    <div style="color:#7a7b7d;margin-top:15px" class="data-value">
        {{ $user->private_note ?? '-' }}
    </div>
