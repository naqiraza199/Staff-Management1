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