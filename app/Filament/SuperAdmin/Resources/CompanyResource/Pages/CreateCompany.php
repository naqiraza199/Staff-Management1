<?php

namespace App\Filament\SuperAdmin\Resources\CompanyResource\Pages;

use App\Filament\SuperAdmin\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use App\Models\Company;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;



protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['company_no'] = 'CN#' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

    $baseUrl = config('app.url') . '/staff/register';
    $encodedCompanyName = urlencode($data['name']);
    $encodedEmail = base64_encode(optional(\App\Models\User::find($data['user_id']))->email);

    $data['staff_invitation_link'] = "{$baseUrl}?company_name={$encodedCompanyName}&manager_email={$encodedEmail}";

    return $data;
}
}
