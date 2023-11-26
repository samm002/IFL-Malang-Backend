<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Traits\TokenResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GoogleController extends Controller
{
  use TokenResponse;
  public function redirectToGoogle()
  {
    // $authUrl = Socialite::driver('google')
    //   ->stateless()
    //   ->redirectUrl(route('auth.google.callback'))
    //   ->redirect()
    //   ->getTargetUrl();

    // return response()->json(['auth_url' => $authUrl], 200);
    return Socialite::driver('google')->stateless()->redirect();
  }

  public function handleGoogleCallback()
  {
    try {
      $googleUser = Socialite::driver('google')->stateless()->user();

      // Check if the user exists in your local database
      $localUser = User::where('email', $googleUser->getEmail())->first();

      if (!$localUser) {
        // User doesn't exist, perform registration
        $localUser = User::create([
          'username' => $googleUser->getName(),
          'email' => $googleUser->getEmail(),
          'google_id' => $googleUser->getId(),
          'password' => Hash::make(12345678),
        ]);
        $localUser->email_verified_at = now();
      }

      // Perform login logic using your JWT logic
      $token = $this->jwtLogin($localUser);

      return response()->json([
        'token' => $token,
        'user' => $localUser,
      ], 200);
    } catch (\Exception $e) {
      // Log the exception for debugging
      Log::error('Google authentication error: ' . $e->getMessage());

      // Return a more informative error response
      return response()->json(['error' => 'Google authentication error', 'details' => $e->getMessage()], 500);
    }
  }

  protected function jwtLogin($user)
  {
    $token = JWTAuth::fromUser($user);

    if (!$token) {
      throw ValidationException::withMessages([
        'credentials' => ['Invalid credentials'],
      ]);
    }

    if (!$user->hasVerifiedEmail()) {
      throw new AuthenticationException('User email not verified.');
    }

    $userId = $user->id;

    return $this->respondWithToken($userId, $token);
  }
}
