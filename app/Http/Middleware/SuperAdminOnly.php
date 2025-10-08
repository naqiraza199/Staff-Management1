<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('superadmin')) {

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Notification::make()
                ->title('Access Denied')
                ->body('You are not authorized to access the Super Admin area.')
                ->danger()
                ->send();

            return redirect()->route('filament.superAdmin.auth.login');
        }

        return $next($request);
    }
}
