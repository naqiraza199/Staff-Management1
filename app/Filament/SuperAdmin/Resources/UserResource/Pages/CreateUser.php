<?php

namespace App\Filament\SuperAdmin\Resources\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Spatie\Permission\Models\Role;


class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

protected function afterCreate(): void
{
    $user = $this->record; // the newly created user
    $roleName = $this->data['role'] ?? null; // value is role name now

    if ($roleName) {
        $user->assignRole($roleName); // assign directly by name

        \Filament\Notifications\Notification::make()
            ->title('Role Assigned')
            ->body("{$user->name} has been assigned the '{$roleName}' role.")
            ->success()
            ->send();
    }
}

    
}
