<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Role;
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
        $user_name = str_replace(' ', '_', $googleUser->getName());
  
        $path = public_path("assets/image/user/profile_picture");
        $timestamp = date('d-m-Y_H-i-s');
        $profilePicture = $user_name . "-profile-" . $timestamp . '.' . '.jpg';
        file_put_contents($path . "/" . $profilePicture, file_get_contents($googleUser->getAvatar()));

        $user = User::create([
          'name' => $googleUser->getName(),
          'username' => $googleUser->getNickname() ?? $user_name,
          'email' => $googleUser->getEmail(),
          'google_id' => $googleUser->getId(),
          'profile_picture'=> $profilePicture,
          'password' => Hash::make(12345678),
          'email_verified_at' => now(),
          'role_id' => Role::where('name', 'user')->first()->id,
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
        if ($user->google_id) {
          return response()->json([
            'status' => 'success',
            'message' => 'Login with google account success',
            'data' => $token,
          ], 200);
        } else {
          return response()->json([
            'status' => 'success',
            'message' => 'Login with email success',
            'data' => $token,
          ], 200);
        }
      }
    } catch (\Exception $e) {
      return response()->json(['error' => 'Google authentication error', 'details' => $e->getMessage()], 500);
    }
  }
}
