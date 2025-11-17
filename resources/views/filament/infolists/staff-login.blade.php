@php
use App\Models\User;

$staff = $getRecord();
$user = User::find($staff->id);
@endphp
<style>
    button[ type="submit" ][title="Send Email"],
button.send-email-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1.25rem;
  font-size: 0.875rem; /* text-sm */
  font-weight: 600;
  color: #fff;
  background-color: #2563eb; /* Tailwind blue-600 */
  border: none;
  border-radius: 0.5rem; /* rounded-lg */
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
  transition: all 0.2s ease-in-out;
}

button[ type="submit" ][title="Send Email"]:hover,
button.send-email-btn:hover {
  background-color: #1d4ed8; /* blue-700 */
  box-shadow: 0 4px 10px rgba(29, 78, 216, 0.35);
  transform: translateY(-1px);
}

button[ type="submit" ][title="Send Email"]:active,
button.send-email-btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(29, 78, 216, 0.25);
}

button[ type="submit" ][title="Send Email"]:focus,
button.send-email-btn:focus {
  outline: 3px solid rgba(37, 99, 235, 0.3);
  outline-offset: 2px;
}

</style>
@if (empty($user->last_login_at) && empty($user->password))
    <div class="data-value">
        <form action="{{ route('admin.send-set-password-email', $user->id) }}" method="POST">
            @csrf
            <button 
            type="submit"
            class="send-email-btn"
            title="Send Email">
            Send Email
            </button>

        </form>
    </div>
@else
    <div class="sapace">
        <div>
            <span class="data-label">Last Login</span>
        </div>
        <div class="flex items-center justify-between mt-1">
            <span style="padding: 1px 9px;font-size: 10px;" class="status-badge">
               {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : '-' }}
            </span>
        </div>
    </div>
@endif
