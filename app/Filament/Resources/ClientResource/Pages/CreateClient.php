<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the user_id to the logged-in user
        $data['user_id'] = Auth::id();
        
        // Generate client number in format CLIENT#12345 (5 random numbers)
        $randomNumbers = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $data['client_no'] = 'CLIENT#' . $randomNumbers;
        
        return $data;
    }
}
