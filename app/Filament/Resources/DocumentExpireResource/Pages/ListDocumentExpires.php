<?php

namespace App\Filament\Resources\DocumentExpireResource\Pages;

use App\Filament\Resources\DocumentExpireResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentExpires extends ListRecords
{
    protected static string $resource = DocumentExpireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
