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

        // 🚫 Skip if user is not logged in
        if (!$user) {
            return $next($request);
        }

        // 🚫 Apply only to users with Admin role
        if (!$user->hasRole('Admin')) {
            return $next($request);
        }

        // 🚫 Skip if user is logging out (any logout route)
        if ($request->routeIs('filament.admin.auth.logout') || $request->is('logout')) {
            return $next($request);
        }

        // 🚫 Skip Profile Settings page itself to avoid redirect loop
        if ($request->routeIs('filament.admin.pages.profile-setting')) {
            return $next($request);
        }

        // ✅ Profile check
        $profileIncomplete = empty($user->name) || empty($user->email);

        // ✅ Company + subscription check
        $companyData = Cache::remember("user:{$user->id}:company:min", now()->addMinutes(5), function () use ($user) {
            $company = $user->company()->select('id', 'name', 'is_subscribed', 'subscription_plan_id')->first();
            return $company ? [
                'id' => $company->id,
                'name' => $company->name,
                'is_subscribed' => $company->is_subscribed,
                'subscription_plan_id' => $company->subscription_plan_id,
            ] : null;
        });

        $companyIncomplete = !$companyData || empty($companyData['name']);
        $subscriptionInvalid = !$companyData || $companyData['is_subscribed'] == 0 || empty($companyData['subscription_plan_id']);

        if ($profileIncomplete || $companyIncomplete || $subscriptionInvalid) {
            Notification::make()
                ->title('Action Required')
                ->body('Please complete your profile, company setup, and ensure an active subscription before continuing.')
                ->warning()
                ->persistent()
                ->send();

            return redirect()->route('filament.admin.pages.profile-setting');
        }

        return $next($request);
    }
}
