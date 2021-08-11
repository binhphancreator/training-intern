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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tag/{id}',[\App\Http\Controllers\TagController::class,'show']);

Route::post('/tag/add',[\App\Http\Controllers\TagController::class,'store']);

Route::get('/tags',[\App\Http\Controllers\TagController::class,'index']);

Route::get('/tag/add/after',[\App\Http\Controllers\TagController::class,'storeAfter']);

Route::get('/now',function(){return app('now');});
