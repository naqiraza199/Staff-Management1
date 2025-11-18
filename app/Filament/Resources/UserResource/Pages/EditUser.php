<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\StaffProfile;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Notifications\SetPasswordNotification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $staffProfile = $this->record->staffProfile;

        if ($staffProfile) {
            $data = array_merge($data, [
                'salutation' => $staffProfile->salutation,
                'first_name' => $staffProfile->first_name,
                'middle_name' => $staffProfile->middle_name,
                'last_name' => $staffProfile->last_name,
                'mobile_number' => $staffProfile->mobile_number,
                'phone_number' => $staffProfile->phone_number,
                'role_type' => $staffProfile->role_type,
                'role_id' => $staffProfile->role_id,
                'gender' => $staffProfile->gender,
                'dob' => $staffProfile->dob,
                'employment_type' => $staffProfile->employment_type,
                'address' => $staffProfile->address,
                'profile_pic' => $staffProfile->profile_pic,
            ]);
        }

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $updatedUser = parent::handleRecordUpdate($record, $data);

        $staffProfileFields = [
            'salutation', 'first_name', 'middle_name', 'last_name', 'mobile_number', 'phone_number',
            'role_type', 'role_id', 'gender', 'dob', 'employment_type', 'address', 'profile_pic'
        ];

       $profileData = array_intersect_key($data, array_flip($staffProfileFields));

        if (isset($profileData['role_id']) && is_array($profileData['role_id'])) {
            $profileData['role_id'] = $profileData['role_id'][0] ?? null;
        }

        $profileData['user_id'] = $updatedUser->id;

        $updatedUser->staffProfile()->updateOrCreate(
            ['user_id' => $updatedUser->id],
            $profileData
        );


            if (!empty($data['send_onboarding_email'])) {
        $updatedUser->notify(new SetPasswordNotification($updatedUser));
    }
        

        return $updatedUser;
    }
}
