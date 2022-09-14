<?php

use App\Models\AgeRv\AccessPermission as AccessPermissionAlias;
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

Route::get('teste', [\App\Http\Controllers\TestController::class, 'index']);

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

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('/validatedToken', function () {

        $access = null;
        $levelAccess = \App\Models\LevelAccess::
        where('id', auth()->user()->nivel_acesso_id)
            ->first();

        return [
            'levelAccess' => $levelAccess->nivel,
            'status' => true
        ];
    });

    Route::middleware('AccessAgeReport')->prefix('agerreport')->group(function () {

        Route::get('/Access', function () {
            return true;
        });

        Route::get('report/list-connections', [\App\Http\Controllers\ReportApp\ReportAllController::class, 'list_connections']);
        Route::get('report/dici', [\App\Http\Controllers\ReportApp\ReportAllController::class, 'dici']);
    });


    Route::middleware('AccessAgeRv')->prefix('agerv')->group(function () {

        Route::get('/Access', function () {
            $accesPermissions = \Illuminate\Support\Facades\DB::table('agerv_usuarios_permitidos as up')
                                ->leftJoin('portal_colaboradores_funcoes as cf', 'up.funcao_id', '=', 'cf.id')
                                ->leftJoin('portal_nivel_acesso as na', 'up.nivel_acesso_id', '=', 'na.id')
                                ->where('user_id', auth()->user()->id)
                                ->select('cf.funcao', 'na.nivel')
                                ->first();
            $access = null;
            return [
                'levelAccess' => $accesPermissions->nivel,
                'function' => $accesPermissions->funcao
            ];
        });

        Route::prefix('dashboard')->group(function () {
            Route::get('/seller', [\App\Http\Controllers\AgeRv\RvSellerController::class, 'seller']);
        });

        Route::middleware('AccessAdmin')->prefix('management')->group(function () {
            Route::resource('collaborators', \App\Http\Controllers\AgeRv\CollaboratorController::class);
        });

        Route::prefix('analytics')->group(function () {
            Route::get('/', [\App\Http\Controllers\AgeRv\SalesAnalyticController::class, 'index']);
        });

        Route::middleware('AccessMaster')->prefix('routines')->group(function () {
            Route::resource('/voalle-sales', \App\Http\Controllers\AgeRv\VoalleSalesController::class);
        });

    });

});
