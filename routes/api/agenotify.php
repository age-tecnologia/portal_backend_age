<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {


    Route::prefix('sac')->group(function() {

        Route::post('pix', [\App\Http\Controllers\AgeNotify\Sac\AlertPixController::class, 'sendEmail']);

    });
});

Route::get('/b2b/welcome-client', [\App\Http\Controllers\AgeNotify\B2b\WelcomeClientController::class, 'index']);
