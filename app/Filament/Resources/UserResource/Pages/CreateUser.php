<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\Company;
use App\Models\StaffProfile;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $companyId = Company::where('user_id', Auth::id())->value('id');

        $data['company_id'] = $companyId;

        $user = parent::handleRecordCreation($data);

        $staffProfileFields = [
            'salutation', 'first_name', 'middle_name', 'last_name', 'mobile_number', 'phone_number',
            'role_type', 'role_id', 'gender', 'dob', 'employment_type', 'address', 'profile_pic'
        ];

        $profileData = array_intersect_key($data, array_flip($staffProfileFields));
        $profileData['user_id'] = $user->id;
        $profileData['company_id'] = $companyId; 

        if (array_filter($profileData)) {
            StaffProfile::create($profileData);
        }

        return $user;
    }
}
