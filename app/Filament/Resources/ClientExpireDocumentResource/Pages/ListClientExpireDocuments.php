<?php

namespace App\Filament\Resources\ClientExpireDocumentResource\Pages;

use App\Filament\Resources\ClientExpireDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientExpireDocuments extends ListRecords
{
    protected static string $resource = ClientExpireDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
