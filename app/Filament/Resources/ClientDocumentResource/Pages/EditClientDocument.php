<?php

namespace App\Filament\Resources\ClientDocumentResource\Pages;

use App\Filament\Resources\ClientDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientDocument extends EditRecord
{
    protected static string $resource = ClientDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
