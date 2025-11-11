<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Illuminate\Support\Facades\Auth;

class StaffLoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Auth::user();

        // 👇 Redirect Staff users to their custom page
        if ($user && $user->hasRole('Staff')) {
            return redirect()->to('/admin/own-staff-scheduler?user_id=' . $user->id);
        }

        // Default redirect (Filament dashboard)
        return parent::toResponse($request);
    }
}
