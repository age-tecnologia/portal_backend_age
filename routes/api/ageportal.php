<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {


    Route::prefix('aux')->group(function() {


        Route::prefix('modules')->group(function() {

            Route::controller(\App\Http\Controllers\AgePortal\_Aux\AccessModulesController::class)
                ->prefix('access')
                ->group(function() {
                    Route::get('/', 'getModulesAndSections');
                });

        });
    });


    Route::prefix('billing-rule')->group(function() {

        Route::controller(\App\Http\Controllers\AgeCommunicate\BillingRule\BuilderController::class)->prefix('send')->group(function () {
            Route::get('/', 'build');
        });

    });
});
