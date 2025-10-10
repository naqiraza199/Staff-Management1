<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Yahan pe aap sari API routes define karte ho. Ye automatically
| "/api" prefix ke sath access hoti hain.
|
*/

Route::get('/test', function () {
    return response()->json(['message' => 'API working fine!']);
});

Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);