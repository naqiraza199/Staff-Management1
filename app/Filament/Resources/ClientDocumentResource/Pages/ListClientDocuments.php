<?php

namespace App\Filament\Resources\ClientDocumentResource\Pages;

use App\Filament\Resources\ClientDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientDocuments extends ListRecords
{
    protected static string $resource = ClientDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
