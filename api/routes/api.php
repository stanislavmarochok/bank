<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JwtAuthController;
use App\Http\Controllers\BankAccountController;

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

Route::group([
    'middleware' => 'api',
    'prefix'     => 'auth'
], function ($router) 
{
    Route::get('/user',           [JwtAuthController::class, 'user']);

    Route::post('/signup',        [JwtAuthController::class, 'register']);
    Route::post('/signin',        [JwtAuthController::class, 'login']);
    Route::post('/token-refresh', [JwtAuthController::class, 'refresh']);
    Route::post('/signout',       [JwtAuthController::class, 'signout']);
});

Route::group([
    'middleware' => 'api',
    'prefix'     => 'bank'
], function ($router) 
{
    Route::get('/details',           [BankAccountController::class, 'getBankAccount']);
    Route::get('/amount',            [BankAccountController::class, 'getAmount']);
    Route::post('/add_amount',       [BankAccountController::class, 'addAmount']);
    Route::post('/charge_amount',    [BankAccountController::class, 'chargeAmount']);
});
