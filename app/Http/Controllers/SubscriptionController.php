<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;

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

}
