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
            // ❌ Check if user has no access flag
            if ($user->no_access) {
                Auth::logout();

                Notification::make()
                    ->title('No Access')
                    ->body('You do not have access to login. Please contact the administrator.')
                    ->danger()
                    ->send();

                return redirect()->route('filament.admin.auth.login');
            }

            // ❌ Check if user has NO role (Spatie permission)
            if ($user->roles()->count() === 0) {
                Auth::logout();

                Notification::make()
                    ->title('No Role Assigned')
                    ->body('You cannot login because no role is assigned to your account. Please contact the administrator.')
                    ->danger()
                    ->send();

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
