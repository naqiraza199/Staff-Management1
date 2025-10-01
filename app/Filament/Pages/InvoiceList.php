<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Models\User;
use App\Models\Company;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentCategory;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\AdditionalContact;
use App\Models\InvoicePayment;
use Str;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class InvoiceList extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    protected static string $view = 'filament.pages.invoice-list';

    protected static ?string $navigationGroup = 'Invoices';
    

    public function table(Table $table): Table
    {
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');

    return $table
         ->query(fn (): Builder =>
                Invoice::query()->where('company_id', $companyId)
            )
        ->columns([
            TextColumn::make('invoice_no')
                ->label('Invoice Number')
                ->searchable(),

            TextColumn::make('display_name')
                ->label('To')
                ->getStateUsing(function ($record) {
                    if ($record->additional_contact_id) {
                        $contact = AdditionalContact::find($record->additional_contact_id);
                        return $contact
                            ? trim($contact->first_name . ' ' . $contact->last_name)
                            : '-';
                    }

                    return $record->client->display_name ?? '-';
                }),

              TextColumn::make('amount')
                    ->label('Amount')
                    ->money('usd', true) 
                    ->sortable(),

                TextColumn::make('tax')
                    ->label('Tax')
                    ->money('usd', true) 
                    ->sortable(),

                TextColumn::make('balance')
                    ->label('Balance')
                    ->money('usd', true) 
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Paid',
                        'warning' => 'Unpaid/Overdue',
                        'danger'  => 'Overdue',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        if ($state === 'Paid') {
                            $lastPayment = InvoicePayment::where('invoice_id', $record->id)
                                ->latest('payment_date')
                                ->first();

                            if ($lastPayment) {
                                return "Paid " . Carbon::parse($lastPayment->payment_date)->format('d M Y');
                            }

                            return "Paid";
                        }

                            if ($state === 'Unpaid/Overdue' && $record->payment_due) {
                                $dueDate = Carbon::parse($record->payment_due)->startOfDay();
                                $today   = Carbon::now()->startOfDay();

                                $daysRemaining = $today->diffInDays($dueDate, false); // signed integer only

                                if ($daysRemaining > 0) {
                                    return "Due in {$daysRemaining} " . Str::plural('day', $daysRemaining);
                                } elseif ($daysRemaining === 0) {
                                    return "Due Today";
                                } else {
                                    return "Overdue";
                                }
                            }


                        if ($state === 'Overdue') {
                            return "Overdue";
                        }

                        return $state;
                    })
                    ->sortable(),

                TextColumn::make('issue_date')
                    ->label('Issued Date')
                    ->date('d M Y') 
                    ->sortable(),

        ])

        ->headerActions([
    Tables\Actions\Action::make('export')
        ->label('')
        ->tooltip('Export')
        ->color('success')
        ->icon('heroicon-s-arrow-down-tray')
        ->action(function () {
            // Your custom logic here (download, redirect, etc.)
            // Example: export invoices to CSV
        }),

    Tables\Actions\Action::make('import')
        ->label('')
        ->tooltip('Import')
        ->color('ligi')
        ->icon('heroicon-s-arrow-up-tray')
        ->action(function () {
            // Your custom logic here (download, redirect, etc.)
            // Example: export invoices to CSV
        }),

    Tables\Actions\Action::make('print')
        ->label('')
        ->tooltip('Print')
        ->color('stripe')
        ->icon('heroicon-s-printer')
        ->action(function () {
            // Your custom logic here (download, redirect, etc.)
            // Example: export invoices to CSV
        }),

    Tables\Actions\Action::make('generate')
        ->label('Generate')
        ->icon('heroicon-s-plus')
        ->url(fn () => route('filament.admin.pages.invoice-generate')),
])

        ->filters([

    // 1. Client filter
    SelectFilter::make('client_id')
        ->label('Client')
        ->options(
            \App\Models\Client::where('user_id', $authUser->id)
                ->pluck('display_name', 'id')
        ),

    // 2. Status filter
    SelectFilter::make('status')
        ->label('Status')
        ->options([
            'Paid' => 'Paid',
            'Unpaid/Overdue' => 'Unpaid/Overdue',
            'Overdue' => 'Overdue',
        ])
        ->query(function (Builder $query, array $data) {
            if ($data['value'] ?? null) {
                $query->where('status', $data['value']);
            }
        }),

    // 3. Date filter
    SelectFilter::make('date_range')
        ->label('Date Range')
        ->options([
            'today' => 'Today',
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_quarter' => 'This Quarter',
            'last_week' => 'Last Week',
            'last_fortnight' => 'Last Fortnight',
            'last_month' => 'Last Month',
            'last_quarter' => 'Last Quarter',
            'custom' => 'Custom Date',
        ])
        ->query(function (Builder $query, array $data) {
            $today = Carbon::today();

            switch ($data['value'] ?? null) {
                case 'today':
                    $query->whereDate('issue_date', $today);
                    break;
                case 'this_week':
                    $query->whereBetween('issue_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('issue_date', Carbon::now()->month)
                          ->whereYear('issue_date', Carbon::now()->year);
                    break;
                case 'this_quarter':
                    $query->whereBetween('issue_date', [Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()]);
                    break;
                case 'last_week':
                    $query->whereBetween('issue_date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                    break;
                case 'last_fortnight':
                    $query->whereBetween('issue_date', [Carbon::now()->subDays(14), Carbon::now()]);
                    break;
                case 'last_month':
                    $query->whereMonth('issue_date', Carbon::now()->subMonth()->month)
                          ->whereYear('issue_date', Carbon::now()->subMonth()->year);
                    break;
                case 'last_quarter':
                    $query->whereBetween('issue_date', [
                        Carbon::now()->subQuarter()->firstOfQuarter(),
                        Carbon::now()->subQuarter()->lastOfQuarter(),
                    ]);
                    break;
            }
        }),

    // Extra: Custom date picker
    Filter::make('custom_date')
        ->form([
            DatePicker::make('from')->label('From'),
            DatePicker::make('to')->label('To'),
        ])
        ->query(function (Builder $query, array $data) {
            if ($data['from'] && $data['to']) {
                $query->whereBetween('issue_date', [$data['from'], $data['to']]);
            }
        }),
])

        ->recordUrl(fn ($record) => url("/admin/invoice-view?invoice_id={$record->id}"));
    }

            public function getFilteredInvoices()
        {
            $query = $this->getFilteredTableQuery(); 
            return $query->get();
        }

        public function getTotals(): array
        {
            $invoices = $this->getFilteredInvoices();

            $totalAmount = $invoices->sum('amount');
            $totalTax    = $invoices->sum('tax');
            $grandTotal  = $totalAmount + $totalTax;

            $invoiceIds  = $invoices->pluck('id');
            $paidAmount  = InvoicePayment::whereIn('invoice_id', $invoiceIds)->sum('paid_amount') ?? 0;

            $unpaidOverdueBalance = $invoices->where('status', 'Unpaid/Overdue')->sum('balance');
            $overdueBalance       = $invoices->where('status', 'Overdue')->sum('balance');

            return [
                'grandTotal' => $grandTotal,
                'totalTax'   => $totalTax,
                'paidAmount' => $paidAmount,
                'unpaidOverdueBalance' => $unpaidOverdueBalance,
                'overdueBalance' => $overdueBalance,
            ];
        }

}
