<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class SetPasswordController extends Controller
{
    public function show(User $user, Request $request)
    {
        // Validate the signed URL
        if (!$request->hasValidSignature()) {
            Notification::make()
                ->title('Invalid Link')
                ->body('This password reset link is invalid or has expired.')
                ->danger()
                ->send();
                
            return redirect()->route('filament.admin.auth.login');
        }

        // Validate the token matches and hasn't been used before
        $token = $request->token;
        
        if (!$user->set_password_token || $user->set_password_token !== $token) {
            Notification::make()
                ->title('Invalid Link')
                ->body('This password reset link has already been used or is invalid.')
                ->danger()
                ->send();
                
            return redirect()->route('filament.admin.auth.login');
        }

        // Check if token has expired (24 hours)
        if ($user->set_password_sent_at && $user->set_password_sent_at->addHours(24)->isPast()) {
            // Clear the expired token
            $user->set_password_token = null;
            $user->set_password_sent_at = null;
            $user->save();
            
            Notification::make()
                ->title('Link Expired')
                ->body('This password reset link has expired. Please request a new one.')
                ->danger()
                ->send();
                
            return redirect()->route('filament.admin.auth.login');
        }

        return view('auth.set-password', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|confirmed|min:8',
            'token' => 'required|string',
        ]);

        // Validate the token again
        $token = $request->token;
        
        if (!$user->set_password_token || $user->set_password_token !== $token) {
            Notification::make()
                ->title('Invalid Link')
                ->body('This password reset link has already been used or is invalid.')
                ->danger()
                ->send();
                
            return back()->withErrors(['token' => 'Invalid or expired token']);
        }

        // Check if token has expired
        if ($user->set_password_sent_at && $user->set_password_sent_at->addHours(24)->isPast()) {
            $user->set_password_token = null;
            $user->set_password_sent_at = null;
            $user->save();
            
            Notification::make()
                ->title('Link Expired')
                ->body('This password reset link has expired. Please request a new one.')
                ->danger()
                ->send();
                
            return back()->withErrors(['token' => 'Token has expired']);
        }

        // Set the password
        $user->password = Hash::make($request->password);
        
        // Clear the token after successful password set (invalidate the link)
        $user->set_password_token = null;
        $user->set_password_sent_at = null;
        $user->save();

        Notification::make()
            ->title('Password Created')
            ->body('Your password has been set successfully. You can now login.')
            ->success()
            ->send();

        return redirect()->route('filament.admin.auth.login');
    }
}
