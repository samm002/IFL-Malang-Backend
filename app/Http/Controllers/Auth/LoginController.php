<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class LoginController extends Controller
{
  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');

    $validator = Validator::make($credentials, [
      'email' => 'required|email',
      'password' => 'required|string|min:8'
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->messages()], 200);
    }

    try {
      if (!$token = JWTAuth::attempt($credentials)) {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
          throw ValidationException::withMessages([
            'credentials' => [trans('auth.failed')],
          ]);
        } elseif (!Hash::check($request->password, optional($user)->getAuthPassword())) {
          throw ValidationException::withMessages([
            'credentials' => [trans('auth.password')],
          ]);
        } else {
          throw ValidationException::withMessages([
            'credentials' => ['Invalid credentials'],
          ]);
        }
      }

      if (!$request->user()->hasVerifiedEmail()) {
        throw new AuthenticationException('User email not verified.');
      }

      $userId = auth()->user()->id;

      $data['token'] = $this->respondWithToken($userId, $token);

      return response()->json([
        'status' => 'success',
        'message' => 'Login success',
        'data' => $data,
      ], 200);
    } catch (\Exception $e) {
      $errorType = get_class($e);
      return response()->json([
        'status' => 'error',
        'message' => 'Could not create token.',
        'error_type' => $errorType,
        'error_detail' => $e->getMessage(),
      ], 500);
    }
  }

  public function logout(Request $request)
  {
    try {
      JWTAuth::invalidate($request->token);

      return response()->json([
        'status' => 'success',
        'message' => 'User has been logged out'
      ]);
    } catch (\Exception $e) {
      $errorType = get_class($e);
      return response()->json([
        'status' => 'error',
        'message' => 'Log out failed',
        'error_type' => $errorType,
        'error_detail' => $e->getMessage(),
      ], 500);
    }
  }

  protected function respondWithToken($userId, $token)
  {
    return [
      'user' => $userId,
      'token' => [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60
      ]
    ];
  }
}
