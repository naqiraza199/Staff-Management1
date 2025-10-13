<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\Timesheet;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Filament\Exports\TimesheetsGroupedExport;
use Filament\Tables\Actions\ExportAction;

class TimesheetList extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static ?string $navigationLabel = 'List';
    protected static ?string $navigationGroup = 'Timesheet';
    protected static ?string $title = 'Timesheets';
    protected static string $view = 'filament.pages.timesheet-list';

    public function table(Table $table): Table
    {
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');

        return $table
            ->query(
                    Timesheet::query()
                        ->join('users', 'timesheets.user_id', '=', 'users.id')
                        ->select([
                            'timesheets.user_id as id', 
                            'users.name as user_name',  
                            DB::raw('SUM(timesheets.weekday_12a_6a) as weekday_12a_6a'),
                            DB::raw('SUM(timesheets.weekday_6a_8p) as weekday_6a_8p'),
                            DB::raw('SUM(timesheets.weekday_8p_10p) as weekday_8p_10p'),
                            DB::raw('SUM(timesheets.weekday_10p_12a) as weekday_10p_12a'),
                            DB::raw('SUM(timesheets.saturday) as saturday'),
                            DB::raw('SUM(timesheets.sunday) as sunday'),
                            DB::raw('SUM(timesheets.standard_hours) as standard_hours'),
                            DB::raw('SUM(timesheets.break_time) as break_time'),
                            DB::raw('SUM(timesheets.public_holidays) as public_holidays'),
                            DB::raw('SUM(timesheets.total) as total'),
                            DB::raw('SUM(timesheets.mileage) as mileage'),
                            DB::raw('SUM(timesheets.expense) as expense'),
                            DB::raw('SUM(timesheets.sleepover) as sleepover'),
                            DB::raw('SUM(timesheets.approved_status) as approved_status'),
                        ])
                        ->when($companyId, fn($query) => $query->where('timesheets.company_id', $companyId))
                        ->groupBy('timesheets.user_id', 'users.name')
                )

            ->columns([
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),


                Tables\Columns\TextColumn::make('approved_status')
                    ->badge(),

                Tables\Columns\TextColumn::make('weekday_12a_6a')->label('Weekdays 12a-6a'),
                Tables\Columns\TextColumn::make('weekday_6a_8p')->label('Weekdays 6a-8p'),
                Tables\Columns\TextColumn::make('weekday_8p_10p')->label('Weekdays 8p-10p'),
                Tables\Columns\TextColumn::make('weekday_10p_12a')->label('Weekdays 10p-12a'),
                Tables\Columns\TextColumn::make('saturday')->label('Saturday'),
                Tables\Columns\TextColumn::make('sunday')->label('Sunday'),
                Tables\Columns\TextColumn::make('standard_hours')->label('Standard Hours')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('break_time')->label('Break Time')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('public_holidays')->label('Public Holidays'),
                Tables\Columns\TextColumn::make('total')->label('Total'),
                Tables\Columns\TextColumn::make('mileage')->label('Mileage')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('expense')->label('Expense')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sleepover')->label('Sleepover')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
                ->headerActions([
                        Tables\Actions\Action::make('DownloadAggregatedTimesheets')
                                    ->label('')
                                    ->icon('heroicon-s-cloud-arrow-down')
                                    ->color('success')
                                    ->action(function () {
                                        // Get the records currently filtered and displayed in the table
                                        $records = $this->getTable()->getRecords();

                                        if ($records->isEmpty()) {
                                            // Use $this->notify() if available in your component context
                                            // If not, use Filament's Notification facade
                                            // Assumed context allows $this->notify
                                            $this->notify('warning', 'No records found to export.');
                                            return;
                                        }

                                        $filename = 'aggregated_timesheet_summary.csv';

                                        // Define headers that match the aggregated query results
                                        $headers = [
                                            'User Name',
                                            'Wkday 12a-6a (Hrs)',
                                            'Wkday 6a-8p (Hrs)',
                                            'Wkday 8p-10p (Hrs)',
                                            'Wkday 10p-12a (Hrs)',
                                            'Saturday (Hrs)',
                                            'Sunday (Hrs)',
                                            'Public Holidays (Hrs)',
                                            'Total Hours',
                                            'Standard Hours (Hrs)',
                                            'Break Time (Hrs)',
                                            'Mileage (Total)',
                                            'Expense (Total)',
                                            'Sleepover (Total)',
                                            'Timesheets Approved Status',
                                        ];

                                        $callback = function () use ($records, $headers) {
                                            $file = fopen('php://output', 'w');
                                            // Write CSV headers
                                            fputcsv($file, $headers);

                                            foreach ($records as $record) {
                                                $row = [
                                                    $record->user_name,
                                                    number_format($record->weekday_12a_6a, 2),
                                                    number_format($record->weekday_6a_8p, 2),
                                                    number_format($record->weekday_8p_10p, 2),
                                                    number_format($record->weekday_10p_12a, 2),
                                                    number_format($record->saturday, 2),
                                                    number_format($record->sunday, 2),
                                                    number_format($record->public_holidays, 2),
                                                    number_format($record->total, 2),
                                                    number_format($record->standard_hours, 2),
                                                    number_format($record->break_time, 2),
                                                    number_format($record->mileage, 2),
                                                    number_format($record->expense, 2),
                                                    number_format($record->sleepover, 2),
                                                    number_format($record->approved_count, 2),
                                                ];

                                                fputcsv($file, $row);
                                            }

                                            fclose($file);
                                        };

                                        // Return the streaming download response
                                        return response()->streamDownload($callback, $filename, [
                                            'Content-Type' => 'text/csv',
                                        ]);
                                    }),


               Action::make('print')
                    ->label('')
                    ->icon('heroicon-s-printer')
                    ->color('stripe')
                    ->tooltip('Print Timesheet')
                    ->url(route('filament.timesheets.reports.print'))
                    ->openUrlInNewTab(),

                // ExportAction::make('export')
                //     ->label('')
                //     ->tooltip('Download Timesheet')
                //     ->icon('heroicon-s-arrow-down-tray')
                //     ->color('success')
                //     ->exporter(TimesheetsGroupedExport::class)

            ])
            ->searchable(false)
            ->defaultSort('user_id', 'asc')
            ->recordUrl(fn ($record) => url("/admin/timesheet-of-staff?user_id={$record->id}"));
    }
}
