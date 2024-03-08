<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\Donation\CampaignController;
use App\Http\Controllers\Donation\CategoryController;
use App\Http\Controllers\Donation\DonationController;
use App\Http\Controllers\Donation\TransactionController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;

/*
  NOTES :
  - token jwt didapat dari login user
  - middleware 'jwt.verify' sama seperti auth, karena pakai jwt dan stateless, jadi bikin middleware sendiri (route yang pakai middleware ini harus menyertakan jwt token di bagian authorization)
  - middleware 'verified' untuk route yang memerlukan user sudah verifikasi email
*/

Route::prefix('v1')->group(function () {
  Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [RegisterController::class, 'register'])->name('register');
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
    Route::post('email/check', [EmailVerificationController::class, 'checkEmailVerified'])->name('verification.check');
    Route::get('google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google.login');
    Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('refresh-token', [LoginController::class, 'refreshToken'])->name('refresh.token');
    
    Route::middleware(['jwt.verify', 'verified'])->group(function () {
      Route::get('check-token-duration', [LoginController::class, 'checkTokenDuration'])->name('check.token.duration');
      Route::post('update-password', [UpdatePasswordController::class, 'updatePassword'])->name('update.password');
      Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::get('password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

    Route::get('notice/notVerified', [NoticeController::class, 'emailNotVerifiedNotice'])->name('verification.notice');
    Route::get('notice/notAdmin', [NoticeController::class, 'userNotAdminNotice'])->name('notAdmin.notice');
  });

  Route::middleware(['jwt.verify', 'verified'])->group(function () {
    Route::get('profile', [ProfileController::class, 'showProfile'])->name('profile.show');
    Route::put('profile/edit', [ProfileController::class, 'updateProfile'])->name('profile.update');


    Route::group(['prefix' => 'admin', 'middleware' => 'role:admin'], function () {
      Route::get('/', [AdminController::class, 'index']);
      Route::apiResource('role', RoleController::class);
      
      Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'getAllUser'])->name('get.all.user');
        Route::post('/', [UserController::class, 'createUser'])->name('create.user');
        Route::put('/{id}', [UserController::class, 'updateUser'])->name('update.user');
        Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('delete.user');
        Route::get('/verified', [UserController::class, 'getAllVerifiedUser'])->name('get.all.verified.user');
        Route::get('/unverified', [UserController::class, 'getAllNotVerifiedUser'])->name('get.all.not.verified.user');
        Route::get('/email/{email}', [UserController::class, 'getUserByEmail'])->name('get.user.by.email');
        Route::get('/id/{id}', [UserController::class, 'getUserByid'])->name('get.user.by.id');
      });
    });
  });

  Route::apiResource('/category', CategoryController::class);
  Route::apiResource('/campaign', CampaignController::class);
  Route::post('/donation/donate/{campaign_id}', [DonationController::class, 'donate'])->name('donate');
  Route::delete('/donation/deleteAll', [DonationController::class, 'deleteAll']);
  Route::apiResource('/donation', DonationController::class);
  Route::post('/payment-callback', [TransactionController::class, 'paymentCallback'])->name('paymentCallback');
});
