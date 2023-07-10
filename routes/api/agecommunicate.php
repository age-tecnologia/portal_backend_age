<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {


    Route::get('/', function () {
        return "to aqui";
    });

//    Route::prefix('/ageCommunicate')->group(function () {
//       Route::get('/', function () {
//           return "agecomunica";
//       });
//    });

});
