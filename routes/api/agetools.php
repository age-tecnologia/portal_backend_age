<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {


    Route::prefix('tools')->group(function() {


        Route::controller(\App\Http\Controllers\AgeTools\Tools\AntiFraud::class)->prefix('antifraud')->group(function() {
            Route::get('/', 'index');
        });

    });
});
