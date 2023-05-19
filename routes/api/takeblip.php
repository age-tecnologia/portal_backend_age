<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {


    Route::prefix('active-message')->group(function() {


        Route::controller(\App\Http\Controllers\TakeBlip\SendingMessageActiveController::class)->prefix('sending')->group(function() {
            Route::post('/', 'index');
        });

    });
});
