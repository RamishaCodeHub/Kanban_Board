<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\MessageController;
use App\Events\SendMessage;

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

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban');
Route::get('/cardInfo', [KanbanController::class, 'index']);
Route::post('/send-email', [EmailController::class, 'sendEmail']);

Route::get('/setting/background-color', [SettingController::class, 'getBackgroundColor']);
Route::post('/setting/background-color', [SettingController::class, 'setBackgroundColor']);

Route::get('/setting/getbackground-image', [SettingController::class, 'getBackgroundImage']);
Route::post('/setting/background-image', [SettingController::class, 'setBackgroundImage']);


Route::post('/cards', [KanbanController::class, 'storeCard']);
Route::post('/cardStatus', [KanbanController::class, 'storeColumn']);
Route::put('/cards/{columnId}', [KanbanController::class, 'updateCardPosition']);

Route::post('/infoInsert', [AdminController::class, 'insert']);
Route::put('/infoUpdate/{edit_info_id}', [AdminController::class, 'update']);
Route::delete('/infoDelete/{user}', [AdminController::class, 'destroy']);

Route::post('/send-message', [MessageController::class, 'messageStore']);


Route::middleware('auth:sanctum')->group( function () {
    Route::get('/cardStatus', [KanbanController::class, 'index']);
    Route::get('/info', [AdminController::class, 'index']);
    Route::get('/logout', [AuthController::class, 'logout']);
    // Route::get('chat', [MessageController::class, 'index']);
    Route::get('/get-messages', [MessageController::class, 'getmessage']);
});
