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

Route::get('/sync-WooProducts', function () {
    \App\Jobs\SyncWooProductJob::dispatch();
    return 'Syncing products from woocommerce shop to the DB...';
});

Route::post('/register', [\App\Http\Controllers\Controller::class, 'register']);

