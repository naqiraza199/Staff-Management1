<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the assignees relationship data
        $data['assignees'] = $this->record->assignees()->pluck('users.id')->toArray();
        
        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->assignees()->sync($this->form->getState()['assignees']);
    }
}
