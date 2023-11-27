<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Traits\TokenResponse;
use App\Traits\GoogleLogin;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
  use TokenResponse, GoogleLogin;
  public function redirectToGoogle()
  {
    $authUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();

    return response()->json([
      'auth_url' => $authUrl,
    ]);
  }

  public function handleGoogleCallback()
  {
    try {
      $googleUser = Socialite::driver('google')->stateless()->user();

      $user = User::where('email', $googleUser->getEmail())->first();

      if (!$user) {
        $user = User::create([
          'username' => $googleUser->getName(),
          'email' => $googleUser->getEmail(),
          'google_id' => $googleUser->getId(),
          'password' => Hash::make(12345678),
          'email_verified_at' => now(),
        ]);
      }

      $token = $this->login(['email' => $user->email, 'password' => '12345678']);

      if ($user->wasRecentlyCreated) {
        return response()->json([
          'status' => 'success',
          'message' => 'Register with google account success',
          'user' => $user,
          'data' => $token,
        ], 200);
      } else {
        return response()->json([
          'status' => 'success',
          'message' => 'Login with google account success',
          'data' => $token,
        ], 200);
      }
    } catch (\Exception $e) {
      return response()->json(['error' => 'Google authentication error', 'details' => $e->getMessage()], 500);
    }
  }
}
