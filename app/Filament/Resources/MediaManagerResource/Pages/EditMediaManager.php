<?php

namespace App\Filament\Resources\MediaManagerResource\Pages;

use App\Filament\Resources\MediaManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMediaManager extends EditRecord
{
    protected static string $resource = MediaManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
