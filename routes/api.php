<?php

use App\Http\Controllers\api\TransactionsController;
use App\Http\Controllers\api\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. Theseter
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'v1'], static function () {
    Route::post('login', [UsersController::class, 'login']);

 /*   Route::group(['middleware' => ['auth:api']], static function () {*/
        Route::post('init', [TransactionsController::class, 'init']);

        Route::post('post-transaction', [TransactionsController::class, 'createTransaction']);
/*    });*/

});
