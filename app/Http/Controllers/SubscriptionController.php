<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{

    public function success()
    {
        Notification::make()
            ->title('Subscription activated successfully!')
            ->success()
            ->body('Your subscription is now active.')
            ->send();

        return Redirect::route('filament.admin.pages.profile-setting');
    }

    public function cancel()
    {
        Notification::make()
            ->title('Subscription cancelled')
            ->danger()
            ->body('You cancelled the subscription process.')
            ->send();

        return Redirect::route('filament.admin.pages.profile-setting');
    }

        public function successAdmin()
    {
        $user = Auth::user();

        if (!$user && session()->has('recent_user_id')) {
            $user = User::find(session('recent_user_id'));
            if ($user) {
                Auth::login($user);
            }
        }

        Notification::make()
            ->title('Subscription activated successfully!')
            ->success()
            ->body('Your subscription is now active.')
            ->send();

        return Redirect::route('filament.admin.pages.dashboard-view');
    }

    /**
     * Handle cancelled subscription checkout
     */
    public function cancelAdmin()
    {
        Notification::make()
            ->title('Subscription cancelled')
            ->danger()
            ->body('You cancelled the subscription process.')
            ->send();

        return Redirect::route('filament.admin.pages.admin-registration');
    }

}
