<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ConversationController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::get('contacts', [ContactController::class, 'index']);

Route::get('conversations', [ConversationController::class, 'index']);
Route::post('conversations', [ConversationController::class, 'store']);
Route::get('conversations/{id}', [ConversationController::class, 'show']);


Route::get('conversations/{id}/messages/', [MessageController::class, 'index']);
Route::post('conversations/{id}/messages/', [MessageController::class, 'create']);
Route::get('conversations/{id}/messages/{message_id}', [MessageController::class, 'show']);

