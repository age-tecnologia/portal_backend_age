<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {

    Route::prefix('management')->group(function() {

        Route::controller(\App\Http\Controllers\AgeControl\ConductorController::class)->group(function () {
            Route::post('conductor', 'store');
            Route::get('conductor', 'index');
            Route::get('conductor-complete', 'viewConductorComplete');
        });

        Route::resource('services', \App\Http\Controllers\AgeControl\ServicesController::class);
        Route::resource('vehicle-type', \App\Http\Controllers\AgeControl\VehicleTypeController::class);
        Route::resource('vehicle-modality', \App\Http\Controllers\AgeControl\VehicleModalityController::class);
        Route::resource('report', \App\Http\Controllers\AgeControl\ReportsController::class);
        Route::resource('report-periods', \App\Http\Controllers\AgeControl\ReportPeriodsController::class);

    });

});
