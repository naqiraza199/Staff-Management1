<?php

namespace App\Filament\Resources\AllowanceResource\Pages;

use App\Filament\Resources\AllowanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllowance extends EditRecord
{
    protected static string $resource = AllowanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
