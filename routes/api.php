<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Routh untuk Auth
Route::prefix('v1')->group(function () {
  Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
  });
  Route::get('profile', [UserController::class, 'index'])->name('profile')->middleware('jwt.verify');
  Route::get('email/verify/{id}', [AuthController::class, 'verify'])->name('verification.verify');
  Route::get('email/verify', [AuthController::class, 'notice'])->name('verification.notice');
  Route::get('email/resend', [AuthController::class, 'resend'])->name('verification.resend');
  Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
  Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.reset');
});
