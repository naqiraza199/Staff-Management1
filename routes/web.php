<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\InvoicePrintController;
use App\Http\Controllers\InvoicePaymentController;
use Filament\Notifications\Notification;
use App\Http\Controllers\ClientDocumentController;
use App\Http\Controllers\InvoiceSettingsController;
use App\Models\User;
use Spatie\Permission\Models\Role;

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


Route::get('/documents/sign/{token}', [ClientDocumentController::class, 'show'])->name('documents.sign');
Route::post('/documents/sign/{token}', [ClientDocumentController::class, 'store'])->name('documents.sign.store');
Route::get('/documents/demo', [ClientDocumentController::class, 'demo'])->name('documents.demo');


  Route::post('/invoice-settings/store', [InvoiceSettingsController::class, 'store'])->name('invoice-settings.store');
    Route::put('/invoice-settings/update/{id}', [InvoiceSettingsController::class, 'update'])->name('invoice-settings.update');

    Route::post('/filament/taxes/save', [InvoiceSettingsController::class, 'taxSaving'])
    ->name('filament.taxes.save');



Route::get('test' , function(){
    $recipient = auth()->user();

Notification::make()
    ->title('Saved successfully')
    ->sendToDatabase($recipient);
});



Route::get('/make-superadmin', function () {

    $user = User::where('email', 'superadmin@gmail.com')->first();

    if (!$user) {
        return '❌ User with this email not found.';
    }

    $user->assignRole('superadmin');

    return '✅ Superadmin role assigned successfully to ' . $user->email;
});

Route::get('/remove-admin-role', function () {
    $user = User::where('email', 'superadmin@gmail.com')->first();

    if (! $user) {
        return '❌ User not found.';
    }

    if (! $user->hasRole('Admin')) {
        return 'ℹ️ This user does not have the admin role.';
    }

    $user->removeRole('Admin');

    return '✅ Admin role removed successfully from ' . $user->email;
});