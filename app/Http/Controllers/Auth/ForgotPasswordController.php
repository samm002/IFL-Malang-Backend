<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Events\ForgotPassword;
use App\Models\User;

class ForgotPasswordController extends Controller
{
  public function sendResetLinkEmail(Request $request)
  {
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      throw ValidationException::withMessages([
        'credentials' => [trans('auth.failed')],
      ])->status(404);
    } else {
      event(new ForgotPassword($user->email));
      return response()->json([
        'status' => 'success',
        'message' => __('passwords.sent'),
      ]);
    }
  }

  public function showResetForm(Request $request)
  {
    $token = $request->route()->parameter('token');
    $email = $request->query('email');

    // return redirect("http://127.0.0.1:5173/reset-password?mail=$email&token=$token");

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
        ? response()->json(['status' => 'success', 'message' => 'Your password have been changed', 'detail' => $response], 200)
        : response()->json(['status' => 'failed', 'message' => $response], 500);
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Validation error',
        'error' => $e->errors(),
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
