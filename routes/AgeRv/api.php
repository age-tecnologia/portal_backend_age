<?php

namespace api;

use Illuminate\Routing\Route;
use Illuminate\Http\Request;


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
    Route::post('new-user', [\App\Http\Controllers\UsersController::class, 'newUserAgeRv']);
    Route::get('new-password/{id}', [\App\Http\Controllers\UsersController::class, 'newPasswordAgeRv']);
    Route::resource('meta', \App\Http\Controllers\AgeRv\CollaboratorMetaController::class);
});

Route::prefix('analytics')->group(function () {
    Route::get('/', [\App\Http\Controllers\AgeRv\SalesAnalyticController::class, 'index']);
    Route::post('/simulator', [\App\Http\Controllers\AgeRv\Management\SimulatorController::class, 'index']);
});

Route::middleware('AccessMaster')->prefix('routines')->group(function () {
    Route::resource('/voalle-sales', \App\Http\Controllers\AgeRv\VoalleSalesController::class);
});
