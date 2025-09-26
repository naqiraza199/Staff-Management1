<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RedirectController;

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
