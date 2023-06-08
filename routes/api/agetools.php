<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {


    Route::prefix('tools')->group(function() {


        Route::controller(\App\Http\Controllers\AgeTools\Tools\AntiFraud::class)->prefix('antifraud')->group(function() {
            Route::get('/', 'index');
        });

        Route::prefix('mailer')->group(function () {
            Route::resource('mailers', \App\Http\Controllers\AgeTools\Tools\Mailer\MailersController::class);
            Route::controller(\App\Http\Controllers\AgeTools\Tools\Mailer\TemplatesController::class)->prefix('templates')->group(function () {
                Route::get('/', 'index');
            });
            Route::controller(\App\Http\Controllers\AgeTools\Tools\Mailer\SendEmailController::class)->prefix('email')->group(function () {
                Route::post('sending', 'index');
            });
        });

        Route::prefix('schedule')->group(function () {
            Route::controller(\App\Http\Controllers\AgeTools\Tools\Schedule\ScheduleController::class)->prefix('dashboard')->group(function () {
                Route::get('filters', 'getFilters');
                Route::get('data', 'getData');
                Route::post('download-excel', 'downloadExcel');
            });
            Route::controller(\App\Http\Controllers\AgeTools\Tools\Schedule\Note\ExecutedController::class)->prefix('notes')->group(function () {

                Route::post('new-executed', 'store');

            });
        });

    });
});
