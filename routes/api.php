<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ObjectController;

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

Route::middleware('auth:api')->post('/user', function (Request $request) {
    return $request->user();
});

Route::post('/object', [ObjectController::class, 'updateOrCreate']);
Route::get('/object/{mykey}', [ObjectController::class, 'get']);
Route::get('/object/{mykey}?timestamp={timestamp}', [ObjectController::class, 'getByTimestamp']);