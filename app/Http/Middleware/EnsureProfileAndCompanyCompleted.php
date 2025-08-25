<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class EnsureProfileAndCompanyCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request); 
        }

        if ($request->routeIs('filament.admin.pages.profile-setting')) {
            return $next($request);
        }

        $profileIncomplete = empty($user->name) || empty($user->email);

        $companyData = Cache::remember("user:{$user->id}:company:min", now()->addMinutes(5), function () use ($user) {
            $company = $user->company()->select('id', 'name')->first();
            return $company ? ['id' => $company->id, 'name' => $company->name] : null;
        });

        $companyIncomplete = !$companyData || empty($companyData['name']);

        if ($profileIncomplete || $companyIncomplete) {
            Notification::make()
                ->title('Complete Your Profile')
                ->body('Please complete your profile and company setup before continuing.')
                ->warning()
                ->persistent()
                ->send();

            return redirect()->route('filament.admin.pages.profile-setting');
        }

        return $next($request);
    }
}
