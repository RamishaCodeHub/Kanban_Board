<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\SettingController;

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

Route::get('/cardStatus', [KanbanController::class, 'index']);
Route::post('/cards', [KanbanController::class, 'storeCard']);
Route::post('/cardStatus', [KanbanController::class, 'storeColumn']);
Route::put('/cards/{columnId}', [KanbanController::class, 'updateCardPosition']);

Route::get('/setting/background-color', [SettingController::class, 'getBackgroundColor']);
Route::post('/setting/background-color', [SettingController::class, 'setBackgroundColor']);

Route::get('/setting/background-image', [SettingController::class, 'getBackgroundImage']);
Route::post('/setting/background-image', [SettingController::class, 'setBackgroundImage']);




