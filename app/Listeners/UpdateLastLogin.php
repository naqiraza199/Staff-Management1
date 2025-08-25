<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class UpdateLastLogin
{
        public function handle(Login $event): void
        {
            \Log::info('Login event fired for user: ' . $event->user->email);

            $event->user->update([
                'last_login_at' => now(),
            ]);
        }
}
