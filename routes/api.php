<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UssdController;
use App\Http\Controllers\PaymentsController;


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
Route::get('/ussd',  [UssdController::class, 'index']);
   // Route::get('/ussd', 'App\Http\Controllers\API\UssdController@index');
Route::post('/patriotsMomo', [PaymentsController::class,'momo'])->name('patriots.momo');

