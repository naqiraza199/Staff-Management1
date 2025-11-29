<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use App\Models\BillingReport;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Widgets\BillingStats;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use App\Models\PriceBook;
use App\Models\PriceBookDetail;
use App\Models\Company;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;




class BillingReportsClient extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-document-currency-pound';

    protected static string $view = 'filament.pages.billing-reports-client';

    protected static ?string $title = null;
    public ?int $client_id = null;
    public bool $actionsVisible = false;
    public bool $showWidgets = true;
  

public array $records = [];

public function mount(): void
{
    $this->client_id = request()->query('client_id');

    // preload the records for Alpine
    $this->records = BillingReport::when($this->client_id, fn ($q) => $q->where('client_id', $this->client_id))
        ->get(['id', 'total_cost'])
        ->toArray();
}




           public static function shouldRegisterNavigation(): bool
{
    return false;
}


    public function getTitle(): string
    {
        return 'Billing Reports';
    }

    public function getSelectedTableRecordsProperty()
{
    return BillingReport::whereIn('id', $this->selected)->get();
}




public function table(Table $table): Table
{
    $authUser = Auth::user();
    $companyId = Company::where('user_id', $authUser->id)->value('id');

    return $table->query(fn (): Builder =>
            BillingReport::query()
                ->when($this->client_id, fn ($q) => $q->where('client_id', $this->client_id))
        )
            ->recordClasses(fn (BillingReport $record): array => [
                'paid-row' => $record->status === 'Paid',
            ])
            ->columns([
                Tables\Columns\TextColumn::make('date')
                ->label('Date')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('D, d M Y')),

                Tables\Columns\TextColumn::make('shift')
                ->label('Shift')
                ->formatStateUsing(function ($record) {
                    $shift = \App\Models\Shift::find($record->shift_id);

                    if (! $shift) {
                        return 'N/A';
                    }

                    $clientSection = is_string($shift->client_section) ? json_decode($shift->client_section, true) : $shift->client_section;
                    $timeAndLocation = is_string($shift->time_and_location) ? json_decode($shift->time_and_location, true) : $shift->time_and_location;
                    $shiftSection = is_string($shift->shift_section) ? json_decode($shift->shift_section, true) : $shift->shift_section;

                    $shiftText = '';

                    if (! $shift->is_advanced_shift) {
                        $clientName = \App\Models\Client::find($record['client_id'] ?? null)->display_name ?? 'Unknown Client';
                        $priceBookName = \App\Models\PriceBook::find($record['price_book_id'] ?? null)->name ?? 'Unknown Price Book';

                        $start = !empty($timeAndLocation['start_time']) 
                            ? \Carbon\Carbon::parse($timeAndLocation['start_time'])->format('h:i a') 
                            : '';
                        $end = !empty($timeAndLocation['end_time']) 
                            ? \Carbon\Carbon::parse($timeAndLocation['end_time'])->format('h:i a') 
                            : '';

                        $shiftText = "{$clientName} - {$priceBookName} | {$start} - {$end}";
                    } else {
                        $clientDetails = $clientSection['client_details'][0] ?? null;
                        if (! $clientDetails) {
                            $shiftText = 'Advanced Shift';
                        } else {
                            $start = !empty($record['start_time']) 
                            ? \Carbon\Carbon::parse($record['start_time'])->format('h:i a') 
                            : '';
                        $end = !empty($record['end_time']) 
                            ? \Carbon\Carbon::parse($record['end_time'])->format('h:i a') 
                            : '';
                            $clientName = \App\Models\Client::find($record['client_id'] ?? null)->display_name ?? 'Unknown Client';
                            $ratio = $clientDetails['hours'] ?? '';
                            $priceBookName = \App\Models\PriceBook::find($record['price_book_id'] ?? null)->name ?? 'Unknown Price Book';

                            $shiftText = "{$clientName} - {$priceBookName} | {$start} - {$end}";
                        }
                    }

                    return "<a href='#' target='_blank' style='color:#1a88d7'>{$shiftText}</a>";
                })
                ->html(),

               Tables\Columns\TextColumn::make('staff')
    ->label('Staff')
    ->formatStateUsing(function ($record) {
        $shift = \App\Models\Shift::find($record->shift_id);

        if (! $shift) {
            return 'N/A';
        }

        $carerSection = is_string($shift->carer_section)
            ? json_decode($shift->carer_section, true)
            : $shift->carer_section;

        // 🟢 Normal (non-advanced) shifts
        if (! $shift->is_advanced_shift) {
            $userId = $carerSection['user_id'] ?? null;
            return $userId
                ? (\App\Models\User::find($userId)?->name ?? 'Unknown Staff')
                : 'Unknown Staff';
        }

        // 🟢 Advanced shifts
        $userDetails = $carerSection['user_details'] ?? [];
        if (empty($userDetails)) {
            return 'Advanced Staff';
        }

        // Collect user_ids from user_details
        $userIds = collect($userDetails)
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->all();

        if (empty($userIds)) {
            return 'Advanced Staff';
        }

        // Load actual names from DB
        $names = \App\Models\User::whereIn('id', $userIds)
            ->pluck('name')
            ->filter()
            ->implode(', ');

        return $names ?: 'Advanced Staff';
    }),

                Tables\Columns\TextColumn::make('start_time')
                ->label('Start Time')
                ->formatStateUsing(function ($state, $record) {
                    if (! $state || ! $record->date) {
                        return null;
                    }

                    $dateTime = \Carbon\Carbon::parse($record->date . ' ' . $state);

                    return $dateTime->format('h:i a (d/m/Y)');
                }),

                Tables\Columns\TextColumn::make('end_time')
                ->label('Finish Time')
                ->formatStateUsing(function ($state, $record) {
                    if (! $state || ! $record->date) {
                        return null;
                    }

                    $shift = \App\Models\Shift::find($record->shift_id);

                    $timeAndLocation = $shift && is_string($shift->time_and_location)
                        ? json_decode($shift->time_and_location, true)
                        : ($shift->time_and_location ?? []);

                    $date = \Carbon\Carbon::parse($record->date);

                    if (!empty($timeAndLocation['shift_finishes_next_day'])) {
                        $date->addDay();
                    }

                    $dateTime = \Carbon\Carbon::parse($date->format('Y-m-d') . ' ' . $state);

                    return $dateTime->format('h:i a (d/m/Y)');
                }),

                Tables\Columns\TextColumn::make('hours_x_rate')->label('Hours x Rate'),
                Tables\Columns\TextColumn::make('additional_cost')->label('Additional Cost')
                    ->formatStateUsing(fn ($state) => $state !== null ? '$' . number_format($state, 2) : null),

                Tables\Columns\TextColumn::make('distance_x_rate')->label('Distance x Rate'),
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->formatStateUsing(fn ($state) => $state !== null ? '$' . number_format($state, 2) : null),

                Tables\Columns\TextColumn::make('running_total')
                    ->label('Running Cost')
                    ->formatStateUsing(fn ($state) => $state !== null ? '$' . number_format($state, 2) : null),
            ])
            ->headerActions([ 
                Tables\Actions\ActionGroup::make([
               Tables\Actions\Action::make('only_timesheet')
                    ->label('Only Timesheet')
                    ->icon('heroicon-s-document-text')
                    ->color('info')
                    ->url(fn () => route('billing-reports.print', ['clientId' => $this->client_id]))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('detailed')
                    ->label('Detailed')
                    ->icon('heroicon-s-table-cells')
                    ->color('warning')
                    ->url(fn () => route('billing-reports.detailed', ['clientId' => $this->client_id]))
                    ->openUrlInNewTab(),


                ])
                ->label('Print')
                ->icon('heroicon-s-printer')
                ->color('stripe')
                ->label('')
                ->button(),

                        Tables\Actions\Action::make('Download')
                    ->label('')
                    ->icon('heroicon-s-cloud-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $clientId = request()->query('client_id');

                        $records = $this->getTable()->getRecords()
                            ->when($clientId, fn ($q) => $q->where('client_id', $clientId));

                        if ($records->isEmpty()) {
                            $this->notify('warning', 'No records found to export.');
                            return;
                        }

                        $filename = 'invoices.csv';

                        $headers = [
                            'Date',
                            'Shift',
                            'Staff',
                            'Start Time',
                            'Finish Time',
                            'Hours x Rate',
                            'Additional Cost',
                            'Distance x Rate',
                            'Total Cost',
                            'Running Total',
                        ];

                        $callback = function () use ($records, $headers) {
                            $file = fopen('php://output', 'w');
                            fputcsv($file, $headers);

                            foreach ($records as $record) {
                                $row = [
                                    \Carbon\Carbon::parse($record->date)->format('D, d M Y'),
                                    $record->shift_id,
                                    $record->staff ?? 'Unknown Staff',
                                    $record->start_time ? \Carbon\Carbon::parse($record->date . ' ' . $record->start_time)->format('h:i a') : '',
                                    $record->end_time ? \Carbon\Carbon::parse($record->date . ' ' . $record->end_time)->format('h:i a') : '',
                                    $record->hours_x_rate,
                                    $record->additional_cost !== null ? '$' . number_format($record->additional_cost, 2) : '',
                                    $record->distance_x_rate,
                                    $record->total_cost !== null ? '$' . number_format($record->total_cost, 2) : '',
                                    $record->running_total !== null ? '$' . number_format($record->running_total, 2) : '',
                                ];

                                fputcsv($file, $row);
                            }

                            fclose($file);
                        };

                        return response()->streamDownload($callback, $filename, [
                            'Content-Type' => 'text/csv',
                        ]);
                    }),




            ])
      ->bulkActions([
        Tables\Actions\BulkAction::make('invoice_summary')
            ->label('')
            ->color('warning')
            ->icon('heroicon-s-clipboard-document-list')
            ->tooltip('Count Invoice Total')
            ->action(function (Collection $records) {
                $total = $records->sum('total_cost');

                $this->actionsVisible = true;
                    $this->dispatch('refreshWidgets');
            }),

            Tables\Actions\BulkActionGroup::make([
              Tables\Actions\BulkAction::make('add_tax')
                        ->label('Add Tax')
                        ->icon('heroicon-s-plus-circle')
                        ->action(function (Collection $records) {
                            // Collect multiple billing IDs
                            $billingIds = $records->pluck('id')->toArray();

                            // Sum total_cost across selected records
                            $totalCost = $records->sum('total_cost');

                            // Assume all belong to the same client (or pick the first one if mixed)
                            $clientId = $records->first()?->client_id;

                            if ($clientId && !empty($billingIds)) {
                                return redirect()->to(
                                    '/admin/add-tax?client_id=' . $clientId .
                                    '&billing_ids=' . implode(',', $billingIds)
                                );
                            }
                        }),


                    Tables\Actions\BulkAction::make('tax_free')
                    ->label('Tax Free')
                    ->icon('heroicon-s-minus-circle')
                    ->action(function (Collection $records) {
                            $billingIds = $records->pluck('id')->toArray();

                            $totalCost = $records->sum('total_cost');

                            $clientId = $records->first()?->client_id;

                            if ($clientId && !empty($billingIds)) {
                                return redirect()->to(
                                    '/admin/tax-free?client_id=' . $clientId .
                                    '&billing_ids=' . implode(',', $billingIds)
                                );
                            }
                        }),
                ])
                  ->label(function (): string {
                        $total = $this->getSelectedTableRecords()->sum('total_cost');

                        return $total > 0
                            ? 'Invoices ($' . number_format($total, 2) . ')'
                            : 'Invoices';
                    })
                ->icon('heroicon-s-currency-dollar')
                ->color('rado')
                ->hidden(fn () => !$this->actionsVisible),
])

            ->filters([
                Tables\Filters\Filter::make('fund')
                ->form([
                    Forms\Components\Select::make('fund_id')
                    ->label('Select Fund')
                    ->options([
                        'Fund' => 'Fund'
                    ])
                    ->searchable()
                    ->default(null) // No default selected
                    ->dehydrated(false), // Prevents sending to server unless changed
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['fund_id'], fn ($query, $fundId) => $query->where('fund_id', $fundId));
                }),

                Tables\Filters\Filter::make('date_range')
                ->form([
                    Grid::make(2)
                    ->schema([
                        DatePicker::make('start_date')
                        ->label('From')
                        ->closeOnDateSelection(),
                        DatePicker::make('end_date')
                        ->label('To')
                        ->closeOnDateSelection(),
                    ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['start_date'] && $data['end_date'],
                        fn ($query) => $query->whereBetween('date', [$data['start_date'], $data['end_date']])
                    );
                }),
            ])
            ->actions([
               Tables\Actions\EditAction::make()
    ->label('')
    ->hidden( fn($record) => $record->status == 'Paid')
    ->modalHeading('Client Attendance')
    ->modalDescription('Editing start and end time will update shift time according to update shift time checkbox.')
    ->form([
        Select::make('price_book_id')
            ->label('Prices')
            ->options(
                PriceBook::where('company_id', $companyId)
                    ->pluck('name', 'id')
                    ->toArray()
            )
            ->required()
            ->searchable(),

        Grid::make(3)->schema([
            Select::make('fund')
                ->label('Fund')
                ->options([
                    'no_fund' => 'No Fund',
                ])
                ->columnSpan(2)
                ->searchable(),
            
            Placeholder::make('ratio')
                ->label('Ratio')
                ->content('1:1')
        ]),

        Grid::make(2)->schema([
            TimePicker::make('start_time')
                ->label('Start Time')
                ->seconds(false)
                ->required(),
            
            TimePicker::make('end_time')
                ->label('End Time')
                ->seconds(false)
                ->required(),
        ]),

        Grid::make(2)->schema([
            TextInput::make('mileage')
                ->label('Mileage')
                ->numeric()
                ->default(0),
            
            TextInput::make('expense')
                ->label('Expense')
                ->numeric()
                ->default(0.0),
        ]),

        Grid::make(2)->schema([
            Checkbox::make('is_absent')
                ->label('Absent'),
            
            Checkbox::make('is_remove')
                ->label('Remove'),
        ]),

        Textarea::make('comment')
            ->label('What was updated? Add a comment...')
            ->rows(2),

        Checkbox::make('update_shift_time')
            ->label('Update Shift Time'),
    ])
    ->action(function (array $data, $record): void {
        // Parse values
        $shiftDate   = $record->date ?? now(); // If you store date in record
        $shiftStart  = Carbon::parse($data['start_time']);
        $shiftEnd    = Carbon::parse($data['end_time']);
        $priceBookId = $data['price_book_id'];

        $hours = $shiftStart->floatDiffInHours($shiftEnd);

        $dayOfWeek = Carbon::parse($shiftDate)->format('l');
        $dayType = match ($dayOfWeek) {
            'Saturday' => 'Saturday',
            'Sunday'   => 'Sunday',                                                                             //yaad rahe create or edit me change karna he code uske baad advanced pe jana he
            default    => 'Weekdays - I',
        };

        // ✅ fetch price by END TIME
        $priceDetail = PriceBookDetail::where('price_book_id', $priceBookId)
    ->where('day_of_week', $dayType)
    ->where(function ($q) use ($shiftEnd) {
        $endTime = $shiftEnd->format('H:i:s');

        $q->where(function ($sub) use ($endTime) {
            // normal ranges
            $sub->whereRaw('? BETWEEN start_time AND end_time', [$endTime])
                ->whereColumn('end_time', '>', 'start_time');
        })
        ->orWhere(function ($sub) use ($endTime) {
            // over-midnight ranges like 20:00 → 00:00
            $sub->whereColumn('end_time', '<', 'start_time')
                ->where(function ($wrap) use ($endTime) {
                    $wrap->where('start_time', '<=', $endTime)
                         ->orWhere('end_time', '>=', $endTime);
                });
        })
        ->orWhere(function ($sub) {
            // all-day special
            $sub->whereTime('start_time', '00:00:00')
                ->whereTime('end_time', '00:00:00');
        });
    })
    ->orderBy('start_time')
    ->first();



        $rate = $priceDetail?->per_hour ?? 0;
        $per_km_price = $priceDetail?->per_km ?? 0;

        // --- Calculations ---
        $hoursTotal      = $hours * $rate;
        $distanceTotal   = ($data['mileage'] ?? 0) * $per_km_price;
        $additionalCost  = $data['expense'] ?? 0;
        $totalCost       = $hoursTotal + $distanceTotal + $additionalCost;

        $hoursXRate = number_format($hours, 1)
            . ' x $' . number_format($rate, 2);

        $distanceXRate = ($data['mileage'] ?? 0)
            . ' x $' . number_format($per_km_price, 2);

        $record->update([
            'price_book_id'   => $priceBookId,
            'start_time'      => $shiftStart->format('H:i'),
            'end_time'        => $shiftEnd->format('H:i'),
            'hours_x_rate'    => $hoursXRate,
            'distance_x_rate' => $distanceXRate,
            'additional_cost' => $additionalCost,
            'total_cost'      => $totalCost,
            'fund'      => $data['fund'],
            'mileage'      => $data['mileage'],
            'expense'      => $data['expense'],
            'is_absent'      => $data['is_absent'],
            'is_remove'      => $data['is_remove'],
            'comment'      => $data['comment'],
            'update_shift_time'      => $data['update_shift_time'],
        ]);

        Notification::make()
        ->title('Billing Report Updated')
        ->body('Billing Report Updated Successfully')
        ->success()
        ->send();
    })
            ]);
    }

//   protected function getHeaderWidgets(): array
//     {
//         if ($this->showWidgets && $this->actionsVisible) {
//             return [
//                 \App\Filament\Widgets\BillingReportsStats::class,
//             ];
//         }

//         return [];
//     }
    public function getData(): array
    {
        $clientId = request()->query('client_id');

        $reports = BillingReport::query()
            ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
            ->get();

        $totalCost = $reports->sum('total_cost');

        $totalHours = $reports->sum(function ($report) {
            if (!$report->start_time || !$report->end_time || !$report->date) {
                return 0;
            }

            $start = Carbon::parse($report->date . ' ' . $report->start_time);
            $end   = Carbon::parse($report->date . ' ' . $report->end_time);

            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            return abs($end->diffInMinutes($start) / 60);
        });

        return [
            'totalCost' => $totalCost,
            'totalHours' => $totalHours,
        ];
    }
}
