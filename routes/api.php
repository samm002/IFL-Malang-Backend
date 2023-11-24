<?php

use App\Http\Controllers\Admin\Role_UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\ProfileController;

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

/*
  NOTES :
  - token jwt didapat dari login user
  - middleware 'jwt.verify' sama seperti auth, karena pakai jwt dan stateless, jadi bikin middleware sendiri (route yang pakai middleware ini harus menyertakan jwt token di bagian authorization)
  - middleware 'verified' untuk route yang memerlukan user sudah verifikasi email
*/

// Routh untuk Auth
Route::prefix('v1')->group(function () {
  Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [RegisterController::class, 'register'])->name('register');
    Route::post('login', [LoginController::class, 'login'])->name('login');

    // Route::middleware('jwt.verify')->group(function () {
    Route::get('email/verify', [NoticeController::class, 'emailNotVerifiedNotice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
    // });

    //google login
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

    Route::middleware(['jwt.verify', 'verified'])->group(function () {
      Route::get('refresh_token', [LoginController::class, 'refreshToken'])->name('refresh.token');
      Route::get('check_token_duration', [LoginController::class, 'checkTokenDuration'])->name('check.token.duration');
      Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.reset');
    Route::get('role', [NoticeController::class, 'userNotAdmin'])->name('account.notAdmin');
  });

  Route::middleware(['jwt.verify', 'verified'])->group(function () {
    Route::get('profile', [ProfileController::class, 'showProfile'])->name('profile.show');
    Route::put('profile/edit', [ProfileController::class, 'updateProfile'])->name('profile.update');

    Route::middleware('role:admin')->group(function () {
      Route::apiResource('role', RoleController::class);
    });

    Route::group(['prefix' => 'admin', 'middleware' => 'role:admin'], function () {
      Route::apiResource('role_user', Role_UserController::class);
      Route::put('role_user/user_id/{user}', [Role_UserController::class, 'updateByUserId']);
    });
  });
});
