<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\InvoicePrintController;
use App\Http\Controllers\InvoicePaymentController;
use Filament\Notifications\Notification;


use Illuminate\Http\Request;

Route::get('/', [RedirectController::class, 'home']);



Route::post('/set-selected-date', function (Request $request) {
    session(['selected_date' => $request->dateKey]);
    return response()->json(['success' => true]);
})->name('set-selected-date');




Route::get('/billing-reports/{clientId}/print', [App\Http\Controllers\DataPrintController::class, 'printTimesheet'])
    ->name('billing-reports.print');

Route::get('/billing-reports/{clientId}/detailed', [App\Http\Controllers\DataPrintController::class, 'printDetailed'])
    ->name('billing-reports.detailed');

// Invoice print friendly view
Route::post('/invoices/{invoice}/payments', [InvoicePaymentController::class, 'store'])
    ->name('invoices.payments.store');

Route::delete('/invoice-payments/{invoicePayment}', [InvoicePaymentController::class, 'destroy'])
    ->name('invoice-payments.destroy');

Route::get('/invoices/{invoice}/print', [InvoicePrintController::class, 'show'])
    ->name('invoices.print');

Route::post('/invoices/{invoice}/notes', [InvoicePaymentController::class, 'addNote'])
->name('invoices.notes.store');

Route::put('/invoices/{invoice}', [InvoicePaymentController::class, 'update'])
->name('invoices.update');

Route::get('/invoices/print-list', [InvoicePrintController::class, 'printList'])
    ->name('invoices.print-list');

Route::get('/invoices/print-void-list', [InvoicePrintController::class, 'voidList'])
    ->name('invoices.print-void-list');

Route::post('/invoices/{invoice}/void', [InvoicePaymentController::class, 'void'])
->name('invoices.void');

// routes/web.php
Route::post('/invoices/{invoice}/send-email', [InvoicePaymentController::class, 'sendEmail'])
    ->name('invoices.sendEmail');



Route::get('test' , function(){
    $recipient = auth()->user();

Notification::make()
    ->title('Saved successfully')
    ->sendToDatabase($recipient);
});