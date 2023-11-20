<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
  public function sendResetLinkEmail(Request $request)
  {
      $request->validate(['email' => 'required|email']);

      $status = Password::sendResetLink(
          $request->only('email')
      );

      return $status === Password::RESET_LINK_SENT
          ? response()->json(['status' => 'success', 'message' => 'Password reset email have been send', 'detail' => $status])
          : response()->json(['status' => 'failed', 'message' => 'Error sending password reset email'], 500);
  }

  public function reset(Request $request)
  {
      $request->validate([
          'token' => 'required',
          'email' => 'required|email',
          'password' => 'required|min:8|confirmed',
      ]);

      $response = Password::reset(
          $request->only('email', 'password', 'password_confirmation', 'token'),
          function ($user, $password) {
              $user->forceFill([
                  'password' => Hash::make($password),
                  'remember_token' => Str::random(60),
              ])->save();
          }
      );

      return $response === Password::PASSWORD_RESET
          ? response()->json(['status' => 'success', 'message' => 'Your password have been changed', 'detail' => $response])
          : response()->json(['status' => 'failed', 'message' => $response], 500);
  }
}
