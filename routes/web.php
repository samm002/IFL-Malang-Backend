<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Donation\DonationViewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

Route::get('google', function () {
  return view('googleAuth');
});

Route::get('/donate', [DonationViewController::class, 'donationForm'])->name('donation.form');
Route::post('/donate/{campaign_id}', [DonationViewController::class, 'donate'])->name('donation.donate');
Route::get('/invoice/{transaction_id}', [DonationViewController::class, 'donate'])->name('donation.donate');
