<?php

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

Route::middleware('ValidAdmin')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
});

Route::group(['middleware' => 'ValidAdmin'], function () {
    Route::get('admins', [\App\Http\Controllers\AdminController::class, 'getAdmins']);
    Route::post('create/admins', [\App\Http\Controllers\AdminController::class, 'createAdmins']);
    Route::put('edit/{id}/admin', [\App\Http\Controllers\AdminController::class, 'editAdmins']);
    Route::delete('delete/{id}/admin', [\App\Http\Controllers\AdminController::class, 'deleteAdmin']);
});
