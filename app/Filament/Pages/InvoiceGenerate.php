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
use Filament\Tables\Columns\ToggleColumn;
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
use Filament\Tables;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use App\Filament\Widgets\BillingStats;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use App\Models\PriceBook;
use App\Models\PriceBookDetail;
use App\Models\Company;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Models\Invoice;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;


class InvoiceGenerate extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-s-document-chart-bar';
    protected static string $view = 'filament.pages.invoice-generate';
    protected static ?string $navigationGroup = 'Invoices';
     public ?string $group_by = 'client';
     public array $selectedRows = [];
    public $clients;
    public $count;
    public array $selectedClients = [];



    public function getTitle(): string
    {
        return 'Invoices Generate';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $authUser = auth()->user();

       $this->clients = Client::withSum(
                ['billingReports as unpaid_total_cost' => function ($query) {
                    $query->where('status', 'Unpaid');
                }],
                'total_cost'
            )
            ->withCount([
                'billingReports as not_paid_reports_count' => function ($query) {
                    $query->where('status', '!=', 'Paid');
                }
            ])
            ->where('is_archive', 'Unarchive')
            ->where('user_id', $authUser->id)
            ->having('unpaid_total_cost', '>', 0)
            ->get();

    }

    #[On('generateInvoices')]
    public function generateInvoices(array $selectedClients): void
    {
        $authUser = auth()->user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');

        foreach ($selectedClients as $clientData) {
            $clientId  = $clientData['id'];
            $contactId = $clientData['additional_contact_id'] ?? null;
            $issueDate = now()->toDateString(); // today’s date
            $paymentDue = $clientData['payment_due'];
            $purchaseOrder = $clientData['ref_no'] ?? null;

            // Fetch unpaid billing reports
            $billingReports = BillingReport::where('client_id', $clientId)
                ->where('status', 'Unpaid')
                ->get();

            if ($billingReports->isEmpty()) {
                continue;
            }

            $totalCost = $billingReports->sum('total_cost');
            $billingReportIds = $billingReports->pluck('id')->toArray();

            // Tax handling
            $isTaxChecked = $clientData['tax_checked'] ?? false;
            $taxAmount = $isTaxChecked ? $totalCost * 0.10 : 0.00;

            // Random invoice no & NDIS/ref_no
            $invoiceNo = random_int(1000000, 9999999);
            $ndisRef = random_int(100000000, 999999999);

            // Create invoice
            $invoiceCreate = Invoice::create([
                'company_id'            => $companyId,
                'client_id'             => $clientId,
                'additional_contact_id' => $contactId,
                'billing_reports_ids'   => $billingReportIds,
                'invoice_no'            => "#{$invoiceNo}",
                'issue_date'            => $issueDate,
                'payment_due'           => $paymentDue,
                'NDIS'                  => $ndisRef,
                'ref_no'                => $ndisRef,
                'status'                => 'Unpaid/Overdue',
                'amount'                => $totalCost,
                'tax'                   => $taxAmount,
                'balance'               => $totalCost + $taxAmount,
            ]);

            // Update billing reports → Paid
            BillingReport::whereIn('id', $billingReportIds)->update([
                'status' => 'Paid',
            ]);
        }

        Event::create([
            'invoice_id' => $invoiceCreate->id,
            'title'    => $authUser->name . ' Created Invoice',
            'from'     => 'Invoice',
            'body'     => 'Invoice created',
        ]);

        // Show Filament notification
        Notification::make()
            ->title('Invoices Generated Successfully')
            ->success()
            ->send();

        // Refresh page
        $this->redirect(request()->header('Referer'));
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


   
}