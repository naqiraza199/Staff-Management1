<?php

namespace App\Filament\Exports;

use App\Models\Timesheet;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TimesheetsGroupedExport extends Exporter
{
    protected static ?string $model = Timesheet::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user_name')
                ->label('User Name')
                ->getStateUsing(fn ($record) => $record->user_name ?? 'Unknown'),

            ExportColumn::make('approved_status')
                ->label('Approved Status')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('weekday_12a_6a')
                ->label('Weekday 12a-6a')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('weekday_6a_8p')
                ->label('Weekday 6a-8p')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('weekday_8p_10p')
                ->label('Weekday 8p-10p')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('weekday_10p_12a')
                ->label('Weekday 10p-12a')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('saturday')
                ->label('Saturday')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('sunday')
                ->label('Sunday')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('standard_hours')
                ->label('Standard Hours')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('break_time')
                ->label('Break Time')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('public_holidays')
                ->label('Public Holidays')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('total')
                ->label('Total')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('mileage')
                ->label('Mileage')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('expense')
                ->label('Expense')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),

            ExportColumn::make('sleepover')
                ->label('Sleepover')
                ->formatStateUsing(fn ($state) => (float) ($state ?? 0)),
        ];
    }

    public static function getQuery()
    {
        try {
            $authUser = Auth::user();
            $companyId = Company::where('user_id', $authUser->id)->value('id');

            if (!$companyId) {
                Log::warning('No company found for user ID: ' . ($authUser->id ?? 'null'));
                return Timesheet::query()->whereRaw('1 = 0');
            }

            $query = Timesheet::query()
                ->join('users', 'timesheets.user_id', '=', 'users.id')
                ->select([
                    'users.name as user_name',
                    DB::raw('COALESCE(SUM(timesheets.approved_status), 0) as approved_status'),
                    DB::raw('COALESCE(SUM(timesheets.weekday_12a_6a), 0) as weekday_12a_6a'),
                    DB::raw('COALESCE(SUM(timesheets.weekday_6a_8p), 0) as weekday_6a_8p'),
                    DB::raw('COALESCE(SUM(timesheets.weekday_8p_10p), 0) as weekday_8p_10p'),
                    DB::raw('COALESCE(SUM(timesheets.weekday_10p_12a), 0) as weekday_10p_12a'),
                    DB::raw('COALESCE(SUM(timesheets.saturday), 0) as saturday'),
                    DB::raw('COALESCE(SUM(timesheets.sunday), 0) as sunday'),
                    DB::raw('COALESCE(SUM(timesheets.standard_hours), 0) as standard_hours'),
                    DB::raw('COALESCE(SUM(timesheets.break_time), 0) as break_time'),
                    DB::raw('COALESCE(SUM(timesheets.public_holidays), 0) as public_holidays'),
                    DB::raw('COALESCE(SUM(timesheets.total), 0) as total'),
                    DB::raw('COALESCE(SUM(timesheets.mileage), 0) as mileage'),
                    DB::raw('COALESCE(SUM(timesheets.expense), 0) as expense'),
                    DB::raw('COALESCE(SUM(timesheets.sleepover), 0) as sleepover'),
                ])
                ->where('timesheets.company_id', $companyId)
                ->groupBy('users.name');

            // Log the raw query results
            $rows = $query->get();
            Log::info('Timesheet export query executed for company ID: ' . $companyId, [
                'row_count' => $rows->count(),
                'rows' => $rows->toArray(),
            ]);

            if ($rows->isEmpty()) {
                Log::warning('No data found for timesheet export for company ID: ' . $companyId);
            }

            return $query;
        } catch (\Exception $e) {
            Log::error('Timesheet export query failed: ' . $e->getMessage(), ['exception' => $e]);
            return Timesheet::query()->whereRaw('1 = 0');
        }
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your timesheet export has completed and ' .
            number_format($export->successful_rows) . ' ' .
            str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' .
                str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}