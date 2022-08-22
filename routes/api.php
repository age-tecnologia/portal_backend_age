<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('teste',[\App\Http\Controllers\TestController::class, 'index']);

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('login_ad', [\App\Http\Controllers\AuthController::class, 'login_ad']);
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('refresh', [\App\Http\Controllers\AuthController::class, 'refresh']);
    Route::post('me', [\App\Http\Controllers\AuthController::class, 'me']);
});

Route::group(['middleware' => 'auth:api'], function() {

    Route::get('/validatedToken', function() {
        return true;
    });

    Route::get('report/list-connections', [\App\Http\Controllers\ReportApp\ReportAllController::class, 'list_connections']);
    Route::get('report/dici', [\App\Http\Controllers\ReportApp\ReportAllController::class, 'dici']);

});
