<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

      public function getTitle(): string
    {
        return $this->record->display_name . ' - Client Details';
    }

protected function getHeaderActions(): array
{
    return [
        ActionGroup::make([
            Action::make('add_shift')
                ->label('Add Shift')
                ->icon('heroicon-o-plus'),

            Action::make('add_expense')
                ->label('Add Expense')
                ->icon('heroicon-o-banknotes'),

            Action::make('communications')
                ->label('Communications')
                ->icon('heroicon-o-chat-bubble-left-right'),
                
            Action::make('billing_report')
                ->label('Billing Report')
                ->icon('heroicon-o-chart-bar'),

            Action::make('timesheet')
                ->label('Timesheet')
                ->icon('heroicon-o-clock'),

            Action::make('calendar')
                ->label('Calendar')
                ->icon('heroicon-o-calendar'),

            Action::make('documents')
                ->label('Documents')
                ->icon('heroicon-o-document'),

            Action::make('print_roster')
                ->label('Print Roster')
                ->icon('heroicon-o-printer'),
        ])
            ->button()
            ->label('MANAGE')
            ->size('sm')
            ->icon('heroicon-m-chevron-down'),
    ];
}

} 