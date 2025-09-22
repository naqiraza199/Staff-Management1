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

class BillingReportsClient extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-document-currency-pound';

    protected static string $view = 'filament.pages.billing-reports-client';

    protected static ?string $title = null;

    public function getTitle(): string
    {
        return 'Billing Reports';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => BillingReport::query())
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
                        $clientName = \App\Models\Client::find($clientSection['client_id'] ?? null)->display_name ?? 'Unknown Client';
                        $priceBookName = \App\Models\PriceBook::find($clientSection['price_book_id'] ?? null)->name ?? 'Unknown Price Book';

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
                            $clientName = $clientDetails['client_name'] ?? 'Unknown Client';
                            $ratio = $clientDetails['hours'] ?? '';
                            $priceBookName = \App\Models\PriceBook::find($clientDetails['price_book_id'] ?? null)->name ?? 'Unknown Price Book';

                            $shiftText = "{$clientName} - {$ratio} - {$priceBookName}";
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

                    if (! $shift->is_advanced_shift) {
                        $userId = $carerSection['user_id'] ?? null;
                        return \App\Models\User::find($userId)->name ?? 'Unknown Staff';
                    } else {
                        $userDetails = $carerSection['user_details'] ?? [];
                        if (empty($userDetails)) {
                            return 'Advanced Staff';
                        }

                        $names = collect($userDetails)
                            ->pluck('user_name')
                            ->filter()
                            ->implode(', ');

                        return $names ?: 'Advanced Staff';
                    }
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
                            ->url(route('billing-report.timesheet.print'))
                            ->openUrlInNewTab(),


                            Tables\Actions\Action::make('detailed')
                                    ->label('Detailed')
                                    ->icon('heroicon-s-table-cells')
                                    ->color('warning')
                                    ->url(route('billing-report.timesheet.detailed'), true)

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
        $records = $this->getTable()->getRecords();

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
                    $record->shift_id, // you can also join client/pricebook like in table
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('add_tax')
                    ->label('Add Tax')
                    ->icon('heroicon-s-plus-circle')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update([
                                'total_cost' => $record->total_cost * 1.1, // +10% example
                            ]);
                        }
                    }),

                    Tables\Actions\BulkAction::make('tax_free')
                    ->label('Tax Free')
                    ->icon('heroicon-s-minus-circle')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update([
                                'total_cost' => $record->total_cost, // keep as is
                            ]);
                        }
                    }),
                ])
                // The label closure has been fixed to correctly retrieve the selected records.
                ->label(fn () => 'Invoices: $' . number_format($this->getSelectedTableRecords()->sum('total_cost'), 2))
                ->icon('heroicon-s-currency-dollar')
                ->color('info'),
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
                        ->default('2025-09-14') // Default as per image
                        ->closeOnDateSelection(),
                        DatePicker::make('end_date')
                        ->label('To')
                        ->default('2025-09-20') // Default as per image
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
                Tables\Actions\EditAction::make()->label(''),
            ]);
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            BillingStats::class,
        ];
    }
}
