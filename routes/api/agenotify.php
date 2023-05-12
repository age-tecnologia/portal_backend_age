<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {


    Route::prefix('sac')->group(function() {

        Route::post('pix', [\App\Http\Controllers\AgeNotify\Sac\AlertPixController::class, 'sendEmail']);
        Route::post('billing-error', [\App\Http\Controllers\AgeNotify\Sac\BillingErrorController::class, 'sendEmail']);

    });
});

Route::post('/b2b/welcome-client', [\App\Http\Controllers\AgeNotify\B2b\WelcomeClientController::class, 'send']);
