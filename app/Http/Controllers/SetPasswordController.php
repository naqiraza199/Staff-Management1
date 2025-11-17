<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;


class SetPasswordController extends Controller
{
    public function show(User $user)
    {
        return view('auth.set-password', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

          Notification::make()
                ->title('Password Create')
                ->body('Your password has been set. You can now login.')
                ->success()
                ->send();

        return redirect()->route('filament.admin.auth.login');
    }
}
