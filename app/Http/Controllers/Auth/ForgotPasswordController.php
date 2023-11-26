<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
  public function sendResetLinkEmail(Request $request)
  {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
      $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {
      return response()->json([
        'status' => 'success',
        'message' => __('passwords.sent'),
      ]);
    } else {
      $errorMessage = $this->getErrorMessage($status);
      return response()->json([
        'status' => 'failed',
        'message' => $errorMessage,
      ], 500);
    }
  }

  public function showResetForm(Request $request)
  {
    $token = $request->route()->parameter('token');
    $email = $request->query('email');

    return response()->json([
      'status' => 'success',
      'message' => 'tampil reset form success',
      'email' => $email,
      'token' => $token,
    ]);
    // return view('auth.passwords.reset')->with(
    //     ['token' => $token, 'email' => $request->email]
    // );
  }

  public function reset(Request $request)
  {
    try {
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
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Validation error',
        'errors' => $e->errors(),
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error resetting password',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  protected function getErrorMessage($status)
  {
    switch ($status) {
      case Password::INVALID_USER:
        return __('passwords.user');
      case Password::INVALID_TOKEN:
        return __('passwords.token');
      case Password::RESET_THROTTLED:
        $throttleTime = $this->getThrottleTime();
        return __('passwords.throttled', ['minutes' => $throttleTime]);
      default:
        return __('passwords.user');
    }
  }

  protected function getThrottleTime()
  {
    $throttleTimeSeconds = config('auth.passwords.users.throttle', 60);

    $throttleTimeMinutes = ceil($throttleTimeSeconds / 60);

    return $throttleTimeMinutes;
  }
}
