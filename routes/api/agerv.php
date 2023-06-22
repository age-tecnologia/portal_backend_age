<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {

    Route::middleware('AccessAgeRv')->group(function () {

        Route::get('/Access', function () {

            $level = auth()->user()->nivel_acesso_id;

            if($level === 2 || $level === 3) {

                return [
                    'levelAccess' => 'Admin',
                    'function' => 'Admin'
                ];

            } else {
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
            }
        });

        Route::prefix('dashboard')->group(function () {
            Route::get('/seller', [\App\Http\Controllers\AgeRv\RvSellerController::class, 'seller']);
        });


        Route::middleware('AccessAdmin')->prefix('management')->group(function () {
            Route::resource('collaborators', \App\Http\Controllers\AgeRv\CollaboratorController::class);
            Route::get('collaborator/list/', [\App\Http\Controllers\AgeRv\CollaboratorController::class, 'showList']);
            Route::post('new-user', [\App\Http\Controllers\UsersController::class, 'newUserAgeRv']);
            Route::get('new-password/{id}', [\App\Http\Controllers\UsersController::class, 'newPasswordAgeRv']);
            Route::resource('meta', \App\Http\Controllers\AgeRv\CollaboratorMetaController::class);
            Route::get('meta-add-mass', [\App\Http\Controllers\AgeRv\CollaboratorMetaController::class, 'metaAddMass']);
            Route::post('meta-add-supervisors', [\App\Http\Controllers\AgeRv\CollaboratorMetaController::class, 'metaAddSupervisors']);
            Route::post('meta-add-sellers', [\App\Http\Controllers\AgeRv\CollaboratorMetaController::class, 'metaAddSellers']);
            Route::get('users-not-link', [\App\Http\Controllers\AgeRv\LinkUserController::class, 'getUsersNotLinkAgeRv']);
            Route::put('user-link', [\App\Http\Controllers\AgeRv\LinkUserController::class, 'linkUserAndReleaseAccess']);





        });

        Route::controller(\App\Http\Controllers\AgeRv\Collaborators\CollaboratorsController::class)->prefix('collaborators')->group(function () {
            Route::get('list', 'getList');
        });
        Route::prefix('analytics')->group(function () {
            Route::get('/', [\App\Http\Controllers\AgeRv\SalesAnalyticController::class, 'index']);
            Route::get('/payment', [\App\Http\Controllers\AgeRv\SalesRulesController::class, 'index']);
            Route::resource('/consolidated', \App\Http\Controllers\AgeRv\CommissionConsolidatedController::class);
            Route::post('/simulator', [\App\Http\Controllers\AgeRv\Management\SimulatorController::class, 'index']);
        });

        Route::resource('level', \App\Http\Controllers\LevelAccessController::class);
        Route::resource('function', \App\Http\Controllers\FunctionController::class);

        Route::resource('commission', \App\Http\Controllers\AgeRv\CommissionController::class);

        Route::middleware('AccessMaster')->prefix('routines')->group(function () {
            Route::resource('/voalle-sales', \App\Http\Controllers\AgeRv\VoalleSalesController::class);
        });

    });


});

