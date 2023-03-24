<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {


    Route::prefix('sac')->group(function() {

        Route::post('pix', [\App\Http\Controllers\AgeNotify\Sac\AlertPixController::class, 'sendEmail']);

    });
});
