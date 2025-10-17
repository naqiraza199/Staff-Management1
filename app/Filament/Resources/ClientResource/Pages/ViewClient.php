<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Illuminate\View\View;
use Livewire\WithPagination;
use App\Models\Document;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\AdditionalContact;
use App\Models\InvoicePayment;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ViewClient extends ViewRecord 
{

    use WithPagination;

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
                ->url(fn ($record) => route('filament.admin.pages.client-own-docs', ['client_id' => $record->id]))
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




public function mount(string|int $record): void
{
    $this->record = \App\Models\Client::findOrFail($record);
}

public function getFooter(): ?View
    {
        $client = $this->record;

        $documents = Document::where('client_id', $client->id)
            ->with('documentCategory')
            ->orderByDesc('updated_at')
            ->paginate(10, ['*'], 'documents_page');

        $invoices = Invoice::where('client_id', $client->id)
            ->where('is_void', 0)
            ->orderByDesc('issue_date')
            ->paginate(10, ['*'], 'invoices_page');

        return view('client.view', [
            'client' => $client,
            'documents' => $documents,
            'invoices' => $invoices,
        ]);
    }




} 