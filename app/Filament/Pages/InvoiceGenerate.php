<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Client;
use App\Models\AdditionalContact;
use Filament\Tables\Columns\TextInputColumn;
use Carbon\Carbon;
use App\Models\BillingReport;

class InvoiceGenerate extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-document-chart-bar';
    protected static string $view = 'filament.pages.invoice-generate';
    protected static ?string $navigationGroup = 'Invoices';
     public ?string $group_by = 'client';
     public array $selectedRows = [];


    public function getTitle(): string
    {
        return 'Invoices Generate';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'group_by' => 'client',
            'metrics' => ['hours', 'mileage', 'expenses'], // Updated to match CheckboxList name
            'shift_start' => '18-09-2025',
            'shift_end' => '24-09-2025',
            'advanced_options' => false, // Toggle for advanced section
            'due_at' => '08-10-2025',
            'issued_at' => '24-09-2025',
        ]);
    }

protected function getHeaderActions(): array
{
    return [
        \Filament\Pages\Actions\Action::make('generate')
            ->label('Generate')
            ->color('primary')
            ->action(function () {
             
            }),
    ];
}

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        \Filament\Forms\Components\Grid::make(3)
                            ->schema([
                               Select::make('group_by')
                                    ->label('GROUP BY')
                                    ->options([
                                        'client' => 'Client',
                                        'fund' => 'Fund',
                                        'payment_type' => 'Payment Type',
                                    ])
                                    ->reactive()
                                    ->default('client')
                                    ->afterStateUpdated(fn ($state, $set) => $this->group_by = $state)
                                    ->searchable(),

                                CheckboxList::make('metrics') // Renamed from 'cost' to match default
                                    ->label('Cost')
                                    ->columns(3)
                                    ->default(['hours', 'mileage', 'expenses'])
                                    ->options([
                                        'hours' => 'HOURS',
                                        'mileage' => 'MILEAGE',
                                        'expenses' => 'EXPENSES',
                                    ]),
                                DatePicker::make('shift_start')
                                    ->label('SHIFT DATE')
                                    ->displayFormat('d-m-Y')
                                    ->default('18-09-2025'),
                            ])
                            ->extraAttributes(['class' => 'mb-4 border-b border-gray-200 pb-4']),
                    ])
                    ->label(''),
                Toggle::make('advanced_options')
                    ->label('Advanced Options')
                    ->reactive()
                    ->onIcon('heroicon-o-chevron-down')
                    ->offIcon('heroicon-o-chevron-right')
                    ->extraAttributes(['class' => 'text-sm text-blue-600 cursor-pointer']),
                Section::make('')
                    ->schema([
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                DatePicker::make('due_at')
                                    ->label('DUE AT')
                                    ->displayFormat('d-m-Y')
                                    ->default('08-10-2025'),
                                DatePicker::make('issued_at')
                                    ->label('ISSUED AT')
                                    ->displayFormat('d-m-Y')
                                    ->default('24-09-2025'),
                            ])
                            ->extraAttributes(['class' => 'gap-4']),
                    ])
                    ->hidden(fn ($get) => !$get('advanced_options'))
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function generate(): void
    {
        $data = $this->form->getState();
        // Logic to generate/filter data based on $data
        $this->table->getQuery()->applyFilters($data); // Refresh table with filters
        session()->flash('message', 'Invoices generated!');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Client::query())
            ->columns([
             CheckboxColumn::make('id') // use any name not in DB
                    ->label(''),

                    


                TextColumn::make('display_name')
                    ->label('Client')
                    ->weight('bold'),
                TextColumn::make('first_name')
                        ->label('Total Shifts')
                        ->formatStateUsing(function ($record) {
                            $count = $record->billingReports()->count(); // count reports for this client

                           return "<a href='" . url('/admin/billing-reports-client?client_id=' . $record->id) . "' target='_blank'
                                        style='
                                            display:inline-block;
                                            padding:4px 8px;
                                            font-size:12px;
                                            font-weight:500;
                                            border-radius:12px;
                                            background:#e0f0ff;
                                            color:#1a88d7;
                                            text-decoration:none;
                                        '>
                                        {$count} View Reports
                                    </a>";

                        })
                        ->html(),

                   TextColumn::make('fund_name')
                                ->label('Fund Name')
                                ->getStateUsing(fn ($record) => '-')
                                ->visible(fn ($record, $livewire) => $livewire->group_by === 'fund'),

                            TextColumn::make('fund_type')
                                ->label('Fund Type')
                                ->getStateUsing(fn ($record) => 'N/A')
                                ->visible(fn ($record, $livewire) => $livewire->group_by === 'fund'),


                            TextColumn::make('payment_Types')
                        ->label('Payment Type')
                        ->getStateUsing(function ($record) {
                            return 'N\A';
                        })
                                ->visible(fn ($record, $livewire) => $livewire->group_by === 'payment_type'),

                   
                   SelectColumn::make('to')
                        ->label('To')
                        ->options(function ($record) {
                            if (! $record || ! $record->id) {
                                return [];
                            }

                            // Fetch contacts
                            $contacts = AdditionalContact::where('client_id', $record->id)
                                ->get()
                                ->mapWithKeys(function ($contact) {
                                    return [$contact->id => $contact->first_name . ' ' . $contact->last_name];
                                })
                                ->toArray();

                            // Prepend "Client" option at the top
                            return ['client' => 'Client'] + $contacts;
                        })
                        ->searchable(),

               TextInputColumn::make('purchase_order')
                        ->label('Purchase Order')
                        ->placeholder('Enter Purchase')
                        ->rules(['string', 'nullable'])
                        ->sortable()
                        ->searchable(),

                 
                TextInputColumn::make('due_at')
                        ->label('Due At')
                        ->type('date') // browser date picker
                        ->getStateUsing(fn ($record) => $record->due_at
                            ? Carbon::parse($record->due_at)->format('Y-m-d')
                            : null
                        )
                        ->rules(['nullable', 'date'])
                        ->placeholder('YYYY-MM-DD'),

                CheckboxColumn::make('tax')
                    ->label('Tax'),
                TextColumn::make('total_cost')
                        ->label('Total Cost')
                        ->money('USD')
                        ->getStateUsing(function ($record) {
                            return BillingReport::where('client_id', $record->id)
                                ->sum('total_cost');
                        }),
               BadgeColumn::make('status_check')
                    ->label('Status')
                    ->getStateUsing(fn () => 'Ready to invoice')
                    ->colors([
                        'warning' => fn ($state) => $state === 'Ready to invoice',
                    ]),
            ])
            ->striped()
            ->emptyStateHeading('No invoices ready.');
    }
}