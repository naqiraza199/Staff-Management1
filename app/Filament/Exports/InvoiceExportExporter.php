<?php

namespace App\Filament\Exports;

use App\Models\Invoice;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Carbon\Carbon;

class InvoiceExportExporter extends Exporter
{
    protected static ?string $model = Invoice::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('invoice_no')
                ->label('Invoice #'),

            ExportColumn::make('issue_date')
                ->label('Issue Date')
                ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d M Y') : ''),

            ExportColumn::make('client.display_name')
                ->label('To'),

            ExportColumn::make('client.address')
                ->label('Address'),

            ExportColumn::make('purchase_order')
                ->label('Purchase Order'),

            ExportColumn::make('ref_no')
                ->label('Reference'),

            ExportColumn::make('amount')
                ->label('Amount'),

            ExportColumn::make('tax')
                ->label('Tax'),

            ExportColumn::make('balance')
                ->label('Balance'),

            
            ExportColumn::make('payment_due')
                ->label('Due At')
                ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d M Y') : ''),

                
            ExportColumn::make('status')
                ->label('Status'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your invoice export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
