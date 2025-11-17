<?php

namespace App\Http\Responses;

use Filament\Notifications\Notification;
use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class StaffLoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Auth::user();

        if ($user) {
            // ❌ Check if user has no access
            if ($user->no_access) {
                // Logout user immediately
                Auth::logout();

                // Send Filament notification
                Notification::make()
                    ->title('No Access')
                    ->body('You have not access to login. Please contact the administrator.')
                    ->danger()
                    ->send();

                // Redirect back to login
                return redirect()->route('filament.admin.auth.login');
            }

            // ✅ Update last login time
            $user->update([
                'last_login_at' => Carbon::now(),
            ]);
        }

        // 👇 Redirect Staff users to their custom page
        if ($user && $user->hasRole('Staff')) {
            return redirect()->to('/admin/own-staff-scheduler?user_id=' . $user->id);
        }

        // Default redirect (Filament dashboard)
        return parent::toResponse($request);
    }
}
