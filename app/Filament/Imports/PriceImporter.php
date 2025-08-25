<?php

namespace App\Filament\Imports;

use App\Models\Price;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PriceImporter extends Importer
{
    protected static ?string $model = Price::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('support_item_number')->rules(['nullable']),
            ImportColumn::make('support_item_name')->rules(['nullable']),
            ImportColumn::make('registration_group_number')->rules(['nullable']),
            ImportColumn::make('registration_group_name')->rules(['nullable']),
            ImportColumn::make('support_category_name')->rules(['nullable']),
            ImportColumn::make('support_category_number')->rules(['nullable']),
            ImportColumn::make('support_category_number_pace')->rules(['nullable']),
            ImportColumn::make('support_category_name_pace')->rules(['nullable']),
            ImportColumn::make('unit')->rules(['nullable']),
            ImportColumn::make('quote')->rules(['nullable']),

            // dates: permissive (no strict date validation to avoid many failures)
            ImportColumn::make('start_date')->rules(['nullable']),
            ImportColumn::make('end_date')->rules(['nullable']),

            // numeric: allow nullable and numeric (use 'integer' instead of 'numeric' if you want integers only)
            ImportColumn::make('act')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('nsw')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('nt')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('qld')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('sa')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('tas')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('vic')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('wa')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('remote')->numeric()->rules(['nullable', 'numeric']),
            ImportColumn::make('very_remote')->numeric()->rules(['nullable', 'numeric']),

            ImportColumn::make('non_face_to_face_support_provision')->rules(['nullable']),
            ImportColumn::make('provider_travel')->rules(['nullable']),
            ImportColumn::make('short_notice_cancellations')->rules(['nullable']),
            ImportColumn::make('NDIA_requested_reports')->rules(['nullable']),
            ImportColumn::make('irregular_SIL_supports')->rules(['nullable']),
            ImportColumn::make('type')->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Price
    {
        if (empty($this->data['support_item_number'])) {
            // skip rows without key
            return null;
        }

        $record = Price::where('support_item_number', $this->data['support_item_number'])->first();

        return $record ?: new Price();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your price import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
