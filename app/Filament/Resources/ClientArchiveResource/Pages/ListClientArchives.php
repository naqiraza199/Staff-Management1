<?php

namespace App\Filament\Resources\ClientArchiveResource\Pages;

use App\Filament\Resources\ClientArchiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientArchives extends ListRecords
{
    protected static string $resource = ClientArchiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
