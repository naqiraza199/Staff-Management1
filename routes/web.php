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
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TimesheetController;
use Illuminate\Support\Facades\DB;
use App\Models\Timesheet;
use App\Models\Company;
use App\Filament\Exports\TimesheetsGroupedExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\SetPasswordController;
use App\Notifications\SetPasswordNotification;

use App\Http\Controllers\PdfController;

use App\Models\Invoice;

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


Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
Route::get('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

Route::get('/subscription/success/admin', [SubscriptionController::class, 'successAdmin'])->name('admin.subscription.success');
Route::get('/subscription/cancel/admin', [SubscriptionController::class, 'cancelAdmin'])->name('admin.subscription.cancel');

Route::get('/make-superadmin', function () {

    $user = User::where('email', 'jodip@mailinator.com')->first();

    if (!$user) {
        return '❌ User with this email not found.';
    }

    $user->assignRole('Admin');

    return '✅role assigned successfully to ' . $user->email;
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


Route::middleware(['web','auth'])->group(function () {

    Route::get('/filament/timesheets/print', function () {
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');

        $data = Timesheet::query()
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
            ->when($companyId, fn($q) => $q->where('timesheets.company_id', $companyId))
            ->groupBy('timesheets.user_id', 'users.name')
            ->get();

        return view('filament.pages.timesheet-print-inline', compact('data'));
    })->name('filament.timesheets.reports.print');

});


Route::get('/timesheet/print', [TimesheetController::class, 'printReport'])
    ->name('timesheet.print');




Route::post('/admin/clients/{client}/archive', function (Client $client) {
    $client->update(['is_archive' => 'Archive']);

    Notification::make()
        ->title('Client archived successfully.')
        ->success()
        ->send();

    return redirect()->route('filament.admin.resources.clients.index');
})->name('filament.admin.resources.clients.archive');


Route::get('/refresh-roles', function () {
    $user = Auth::user();
    Auth::logout();
    Auth::login($user);
    return back();
})->middleware('auth');


Route::get('/ssl-test', function () {
    return Http::get('https://www.google.com')->status();
});
// Route::get('/pdf/edit', [PdfController::class, 'editAndAnnotate']);
// Route::get('/pdf/fill', [PdfController::class, 'fillForm']);
// Route::get('/pdf/search', [PdfController::class, 'searchText']);
// Route::get('/pdf-editor', [PdfController::class, 'showForm'])->name('pdf.editor');
// Route::post('/pdf-editor/process', [PdfController::class, 'process'])->name('pdf.editor.process');


// Route::get('/pdf-editor/upload', [PdfController::class, 'uploadPdf'])
//     ->name('pdf.editor.upload');


Route::get('/pdfco/test-edit', [PdfController::class, 'testEdit']);     
Route::post('/pdfco/edit-custom', [PdfController::class, 'editCustom']);



Route::get('/set-password/{user}', [SetPasswordController::class, 'show'])
    ->name('user.set-password');

Route::post('/set-password/{user}', [SetPasswordController::class, 'update'])
    ->name('user.set-password.update');




Route::post('/admin/send-set-password-email/{id}', function ($id) {
    $user = User::findOrFail($id);

    $user->notify(new SetPasswordNotification($user));

    if (class_exists(Notification::class)) {
        Notification::make()
            ->title('Email Sent')
            ->body('Set password email sent to ' . $user->email)
            ->success()
            ->send();
    }

    return back();
})->name('admin.send-set-password-email');



Route::get('/update-invoices', function () {
    $invoices = Invoice::orderBy('created_at')->get();
    $sequence = 1;

    foreach ($invoices as $invoice) {
        $invoice->invoice_sequence = $sequence;
        $invoice->invoice_no = str_pad($sequence, 7, '0', STR_PAD_LEFT);
        $invoice->save();
        $sequence++;
    }

    return "Invoices updated successfully!";
});
