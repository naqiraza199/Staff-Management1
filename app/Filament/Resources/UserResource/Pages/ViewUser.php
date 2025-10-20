<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return $this->record->name . ' - Staff Details';
    }

   protected function getHeaderActions(): array
{
    return [
        ActionGroup::make([
            // Action::make('add_shift')
            //     ->label('Add Shift')
            //     ->icon('heroicon-s-plus'),

            // Action::make('communications')
            //     ->label('Communications')
            //     ->icon('heroicon-s-chat-bubble-left-right'),

            Action::make('timesheet')
                ->label('Timesheet')
                ->url(fn ($record) => url("/admin/timesheet-of-staff?user_id={$record->id}"))
                ->icon('heroicon-s-clock'),

            Action::make('calendar')
                ->label('Calendar')
                ->icon('heroicon-s-calendar')
                ->url(fn ($record) => route('filament.admin.pages.staff-calender', ['user_id' => $record->id]))
                ->openUrlInNewTab(),

            Action::make('documents')
                ->label('Documents')
                ->url(fn ($record) => route('filament.admin.pages.staff-own-docs', ['user_id' => $record->id]))
                ->icon('heroicon-s-document'),
        ])
            ->button()                     
            ->label('Manage')        
            ->icon('heroicon-m-chevron-down'),
    ];
}
}
