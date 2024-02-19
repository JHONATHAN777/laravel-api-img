<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register',[AuthController::class, 'register_user']);
Route::post('login',[AuthController::class, 'login']);
Route::get('user_profile',[AuthController::class, 'user_profile']);
Route::post('logout',[AuthController::class, 'logout']);
Route::get('users', [AuthController::class, 'index']);
Route::delete('users/{id}', [AuthController::class, 'delete']);
Route::get('/users/{user}/edit', [AuthController::class, 'edit'])->name('users.edit');
Route::post('/users/{user}', [AuthController::class, 'update'])->name('users.update');
Route::get('users/{id}', [AuthController::class, 'show']);

Route::group(['middleware' => ['auth:sanctum']],function(){

});