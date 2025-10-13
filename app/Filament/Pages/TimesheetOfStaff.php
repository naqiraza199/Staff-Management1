<?php

namespace App\Filament\Pages;

use App\Models\TimesheetReport;
use App\Models\Shift;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Support\Colors\Color;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\DB;
use App\Models\Allowance;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Timesheet;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TimesheetOfStaff extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.timesheet-of-staff';
    protected static ?string $title = 'Timesheet Report';

    public ?int $userId = null;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getRoutePath(): string
    {
        return '/timesheet-of-staff';
    }

    public function mount(): void
    {
        $this->userId = request()->query('user_id');
    }

  public function table(Table $table): Table
{

$formatClientsForExport = function ($clientsData, $includePriceBookLookup = true) {
        if (empty($clientsData)) {
            return '-';
        }

        $clients = is_string($clientsData) ? @json_decode($clientsData, true) : (is_array($clientsData) ? $clientsData : []);

        if (empty($clients)) {
            return '-';
        }

        // Handle the case where clients is a single object, not an array of objects
        if (isset($clients['client_id']) && !isset($clients[0])) {
            $clients = [$clients];
        }

        $formatted = collect($clients)->map(function ($client) use ($includePriceBookLookup) {
            $clientId = data_get($client, 'client_id');
            $clientName = data_get($client, 'client_name');
            $priceBookId = data_get($client, 'price_book_id');

            // Lookup Client Name if missing
            if (!$clientName && $clientId) {
                $clientName = DB::table('clients')->where('id', $clientId)->value('display_name') ?? 'Unknown Client';
            } elseif (!$clientName) {
                 $clientName = 'Unknown Client';
            }

            // Determine Price Book Name
            $priceBookName = 'Community Services'; // Default for shift_id column
            if ($includePriceBookLookup && $priceBookId) {
                // Lookup Price Book Name for the 'clients' column
                $priceBookName = DB::table('price_books')->where('id', $priceBookId)->value('name') ?? 'Unknown Price Book';
            }

            return "{$clientName} - {$priceBookName}";
        });

        // Use comma as separator for CSV/Print
        return $formatted->implode(', ');
    };

    // Helper function to format break time into total minutes string
    $formatBreakTimeForExport = function ($state) {
        if (empty($state) || $state === '0') {
            return '0 mins';
        }

        if (is_numeric($state)) {
            return "{$state} mins";
        }

        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $state, $matches)) {
            $hours = (int) ($matches[1] ?? 0);
            $minutes = (int) ($matches[2] ?? 0);
            $totalMinutes = ($hours * 60) + $minutes;
            return "{$totalMinutes} mins";
        }

        return "{$state} mins";
    };


    return $table
        ->query(fn () => TimesheetReport::query()
            ->where('user_id', $this->userId)
            ->latest('date')
        )
        ->columns([
            TextColumn::make('date')
                ->label('Date')
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('D, d M Y'))
                ->sortable(),

           TextColumn::make('shift_id')
                ->label('Shift')
                ->formatStateUsing(function ($record) {
                    $clientsData = $record->clients;

                    if (empty($clientsData)) {
                        return '-';
                    }

                    if (is_string($clientsData)) {
                        $decoded = json_decode($clientsData, true);
                        $clients = is_array($decoded) ? $decoded : [];
                    } elseif (is_array($clientsData)) {
                        $clients = $clientsData;
                    } else {
                        $clients = [];
                    }

                    if (isset($clients['client_id'])) {
                        $clients = [$clients];
                    }

                    if (empty($clients)) {
                        return '-';
                    }

                    $formatted = collect($clients)->map(function ($client) {
                        $clientId = data_get($client, 'client_id');
                        $clientName = data_get($client, 'client_name');

                        if (!$clientName && $clientId) {
                            $clientName = DB::table('clients')->where('id', $clientId)->value('display_name');
                        }

                        if (!$clientName) {
                            $clientName = 'Unknown';
                        }

                        $priceBook = 'Community Services';
                        return e("{$clientName} - {$priceBook}");
                    });

                    $output = $formatted->implode(',<br>');

                    return new HtmlString("<span style='color:#1a88d7;font-size:13px '>{$output}</span>");
                })
                ->html(),


            TextColumn::make('clients')
                ->label('Clients')
                ->formatStateUsing(function ($record) {
                    $clientsData = $record->clients;

                    if (empty($clientsData)) {
                        return '-';
                    }

                    if (is_string($clientsData)) {
                        $decoded = json_decode($clientsData, true);
                        $clients = is_array($decoded) ? $decoded : [];
                    } elseif (is_array($clientsData)) {
                        $clients = $clientsData;
                    } else {
                        $clients = [];
                    }

                    if (isset($clients['client_id'])) {
                        $clients = [$clients];
                    }

                    if (empty($clients)) {
                        return '-';
                    }

                    $formatted = collect($clients)->map(function ($client) {
                        $clientId = data_get($client, 'client_id');
                        $priceBookId = data_get($client, 'price_book_id');
                        $clientName = data_get($client, 'client_name');

                        if (!$clientName && $clientId) {
                            $clientName = DB::table('clients')->where('id', $clientId)->value('display_name');
                        }

                        $priceBookName = DB::table('price_books')->where('id', $priceBookId)->value('name') ?? 'Unknown';

                        // Final formatted line
                        return e("{$clientName} - {$priceBookName}");
                    });

                    // Separate multiple clients with comma + new line
                    $output = $formatted->implode(',<br>');

                    // Return as HTML with styling
                    return new HtmlString("<span style='color:black; font-size:13px;'>{$output}</span>");
                })
                ->html(),

            TextColumn::make('start_time')
                ->label('Start Time')
                ->formatStateUsing(fn ($state) => $state ? Carbon::createFromFormat('H:i:s', $state)->format('h:i a') : '-'),

            TextColumn::make('end_time')
                ->label('Finish Time')
                ->formatStateUsing(fn ($state) => $state ? Carbon::createFromFormat('H:i:s', $state)->format('h:i a') : '-'),


            TextColumn::make('break_time')
                ->label('Break Time')
                ->formatStateUsing(function ($state) {
                    if (empty($state) || $state === '0') {
                        return '0 mins';
                    }

                    if (is_numeric($state)) {
                        return "{$state} mins";
                    }

                    if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $state, $matches)) {
                        $hours = (int) ($matches[1] ?? 0);
                        $minutes = (int) ($matches[2] ?? 0);
                        $totalMinutes = ($hours * 60) + $minutes;

                        return "{$totalMinutes} mins";
                    }

                    return "{$state} mins";
                }),



            TextColumn::make('hours')
                ->label('Hours')
                ->formatStateUsing(fn($state) => max(0, (float) $state) . ' hrs'),

            TextColumn::make('distance')
                ->label('Distance')
                ->formatStateUsing(fn ($state) => number_format($state, 1)),

            TextColumn::make('expense')
                ->label('Expense')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),

            TextColumn::make('allowances')
    ->label('Allowances')
    ->formatStateUsing(function ($record) {
        $allowances = $record->allowances;

        // ensure it's an array
        if (!is_array($allowances)) {
            $allowances = @json_decode($allowances, true) ?: [];
        }

        if (empty($allowances)) {
            return '-';
        }

        // fetch allowance names
        $names = Allowance::whereIn('id', $allowances)
            ->pluck('name')
            ->filter()
            ->toArray();

        if (empty($names)) {
            return '-';
        }

        // return multiple badges as HTML safely
        return collect($names)
            ->map(fn($name) => "<span style='font-size:12px;'>{$name}</span>")
            ->implode(',');
    })
    ->html(),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->colors([
                    'warning' => 'Pending',
                    'info'    => 'Clockin',
                    'success' => 'Approved',
                ]),
        ]) ->actions([
            
            
            Action::make('approved_clockin')
                    ->label('')
                    ->icon('heroicon-s-hand-thumb-up')
                    ->color('success')
                    ->tooltip('Approve clockin/out times')
                    ->hidden( fn($record) => $record->status == 'Approved')
                    ->action(function ($record) {
                    $record->update(['status' => 'Clockin']);
                            Notification::make()
                                ->title('Timesheet Clockin')
                                ->body("Timesheet Clockin Successfully.")
                                ->success()
                                ->send();
                        })
                    ->button(),  
            
            Action::make('approved')
                    ->label('')
                    ->icon('heroicon-s-hand-thumb-up')
                    ->color('warning')
                    ->tooltip('Approve scheduled times')
                    ->hidden( fn($record) => $record->status == 'Clockin')
                    ->action(function ($record) {
                    $record->update(['status' => 'Approved']);
                            Notification::make()
                                ->title('Timesheet Approved')
                                ->body("Timesheet Approved Successfully.")
                                ->success()
                                ->send();
                        })
                    ->button(),

            Action::make('redo')
                ->label('')
                ->icon('heroicon-s-arrow-path')
                ->color('stripe')
                ->tooltip('Redo')
                ->hidden(fn ($record) => $record->status === 'Pending')
                ->action(function ($record) {
                    $record->update(['status' => 'Pending']);
                    Notification::make()
                        ->title('Timesheet Redo')
                        ->body("Timesheet Redo Successfully.")
                        ->success()
                        ->send();
                })
                ->button(),


            Tables\Actions\EditAction::make()
                ->label('EDIT')
                ->icon('heroicon-s-pencil-square')
                ->color('info')
                ->tooltip('Edit timesheet')
                ->button()
                ->modalHeading('Attendance') 
                // ->modalDescription('Editing start and end time will update shift time according to update shift time checkbox.')
                ->form([
                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\TimePicker::make('start_time')
                                ->label('Start At')
                                ->required(),

                            Forms\Components\TimePicker::make('end_time')
                                ->label('End At')
                                ->required(),

                            Forms\Components\TimePicker::make('clockin')
                                ->label('Clockin')
                                ->placeholder('Clockin...'),
                            
                            Forms\Components\TimePicker::make('clockout')
                                ->label('Clockout')
                                ->placeholder('Clockout...'),
                        ]),

                    // Forms\Components\Checkbox::make('update_shift_time')
                    //     ->label('Update Shift Time')
                    //     ->inline(true) 
                    //     ->default(true),

                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Select::make('allowances')
                                    ->label('Allowances')
                                    ->multiple() 
                                    ->options(function () {
                                        $user = Auth::user();
                                        return Allowance::where('user_id', $user->id)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->placeholder('Choose Allowances')
                                    ->preload(), 

                            Forms\Components\TextInput::make('distance')
                                ->label('Mileage')
                                ->numeric()
                                ->prefix('km')
                                ->suffixIcon('heroicon-m-truck'), 

                            Forms\Components\TextInput::make('expense')
                                ->label('Expense')
                                ->numeric()
                                ->prefix('$')
                                ->default(0.00)
                                ->inputMode('decimal'), 
                        ]),
                ]),

            ])

            ->headerActions([
            Action::make('appovedAll')
                ->label('Approve All')
                ->icon('heroicon-s-check')
                ->color('success')
                ->tooltip('Approve All Timesheets for this User')
                ->requiresConfirmation()
                ->modalHeading('Approve All Timesheets')
                ->modalDescription('Are you sure you want to approve ALL timesheets?')
                ->action(function () {
                    $query = TimesheetReport::query()
                        ->where('user_id', $this->userId);

                    $query->where('status', '!=', 'Approved');

                    $count = $query->update(['status' => 'Approved']);

                     Notification::make()
                                ->title('Success')
                                ->body("Successfully approved {$count} timesheet reports!")
                                ->success()
                                ->send();
                }),

        Action::make('DownloadDetailedTimesheets')
                ->label('')
                ->icon('heroicon-s-cloud-arrow-down')
                ->color('ngree')
                ->tooltip('Download Timesheet')
                ->action(function () use ($formatClientsForExport, $formatBreakTimeForExport) {
                    $records = $this->getTable()->getRecords();
                    if ($records->isEmpty()) {
                        $this->notify('warning', 'No records found to export.');
                        return;
                    }
                    $headers = [ 'Date', 'Shift', 'Clients', 'Start Time', 'Finish Time', 'Break Time', 'Hours', 'Distance', 'Expense', 'Allowances', 'Status', ];
                    $filename = 'detailed_timesheet_report.csv';
                    $callback = function () use ($records, $headers, $formatClientsForExport, $formatBreakTimeForExport) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $headers);
                        foreach ($records as $record) {
                            $allowances = is_string($record->allowances) ? @json_decode($record->allowances, true) : (is_array($record->allowances) ? $record->allowances : []);
                            $allowanceNames = !empty($allowances) ? Allowance::whereIn('id', $allowances)->pluck('name')->filter()->toArray() : [];
                            $row = [
                                Carbon::parse($record->date)->format('D, d M Y'),
                                $formatClientsForExport($record->clients, false),
                                $formatClientsForExport($record->clients, true),
                                $record->start_time ? Carbon::createFromFormat('H:i:s', $record->start_time)->format('h:i a') : '-',
                                $record->end_time ? Carbon::createFromFormat('H:i:s', $record->end_time)->format('h:i a') : '-',
                                $formatBreakTimeForExport($record->break_time),
                                max(0, (float) $record->hours) . ' hrs',
                                number_format($record->distance, 1),
                                '$' . number_format($record->expense, 2),
                                implode(', ', $allowanceNames) ?: '-',
                                $record->status,
                            ];
                            fputcsv($file, $row);
                        }
                        fclose($file);
                    };
                    return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
                }),

            // --- PRINT ACTION (Updated to open new route) ---
            Action::make('printTimesheet')
                ->label('')
                ->icon('heroicon-s-printer')
                ->color('stripe')
                ->tooltip('Print Timesheet')
                ->url(function () {
                    // 1. Get the IDs of the records currently shown in the table
                    $recordIds = $this->getTable()->getRecords()->pluck('id')->implode(',');

                    if (empty($recordIds)) {
                        $this->notify('warning', 'No records found to print.');
                        return '#';
                    }

                    // 2. Generate the URL for the print route.
                    // IMPORTANT: You MUST define a route named 'timesheet.print' that accepts the 'ids' query parameter.
                    try {
                         return route('timesheet.print', ['ids' => $recordIds]);
                    } catch (\InvalidArgumentException $e) {
                        // Fallback if route() is not defined or visible in this context
                        return url("/timesheet/print?ids={$recordIds}");
                    }
                }, true),
            ])
            ->paginated(true)
            ->striped()
            ->defaultSort('date', 'desc')
            ->filters([
            Tables\Filters\Filter::make('date_range')
                ->form([
                        DatePicker::make('start_date')
                        ->label('From')
                        ->closeOnDateSelection(),
                        DatePicker::make('end_date')
                        ->label('To')
                        ->closeOnDateSelection(),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['start_date'] && $data['end_date'],
                        fn ($query) => $query->whereBetween('date', [$data['start_date'], $data['end_date']])
                    );
                }),
            ]);
    }

             public function getBadgeTotals(): array
                {
                    $query = TimesheetReport::query()
                        ->where('user_id', $this->userId);

                    $reportIds = (clone $query)->pluck('id');

                    return [
                        'sleepover' => (float) Timesheet::whereIn('timesheet_report_id', $reportIds)->sum('sleepover'),

                        'mileage'   => (float) $query->sum('distance'),
                        'expense'   => (float) $query->sum('expense'),
                        'approved'  => (float) $query->where('status', 'Approved')->sum('hours'),
                        'total'    => (float) TimesheetReport::where('user_id', $this->userId)->sum('hours'),
                    ];
                }
}
