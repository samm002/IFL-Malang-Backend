<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Jobs\SendResetPasswordLink;

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
      SendResetPasswordLink::dispatch($request->email);
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

    return redirect("http://localhost:5173/reset-password?mail=$email&token=$token");
  }

  public function reset(Request $request)
  {
    try {
      $user_oldPassword = Password::broker()->getUser($request->only('email', 'token'))->password;
      // dd($user_oldPassword)
      $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => [
          'required',
          'min:8',
          'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[\W]).*$/',
          'confirmed'
        ],
      ], [
        'password.regex' => 'Passwords must contain at least: 1 lowercase letter, 1 uppercase letter, 1 number, and 1 symbol (such as !, @, $, #, ^, etc.)'
      ]);

      if ($user_oldPassword && Hash::check($request->input('password'), $user_oldPassword)) {
        return response()->json([
          'status' => 'error',
          'message' => 'Your new password is the same as the current password!',
        ], 400);
      }

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
        ? response()->json(['status' => 'success', 'message' => trans($response)], 200)
        : response()->json(['status' => 'failed', 'message' => trans($this->getErrorMessage($response))], 400);
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => $e->errors()
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage()
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
