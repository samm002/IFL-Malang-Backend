<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Traits\TokenResponse;

class LoginController extends Controller
{
  use TokenResponse;
  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');

    $validator = Validator::make($credentials, [
      'email' => 'required|email',
      'password' => 'required|string|min:8'
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->messages()], 400);
    }

    $user = User::where('email', $credentials['email'])->first();

    try {
      if (!$token = JWTAuth::attempt($credentials)) {

        if (!$user) {
          throw ValidationException::withMessages([
            'credentials' => [trans('auth.failed')],
          ])->status(404);
        } elseif (!Hash::check($request->password, optional($user)->getAuthPassword())) {
          throw ValidationException::withMessages([
            'credentials' => [trans('auth.password')],
          ])->status(401);
        } else {
          throw ValidationException::withMessages([
            'credentials' => ['Invalid credentials'],
          ])->status(401);
        }
      }

      if (!$request->user()->hasVerifiedEmail()) {
        throw new AuthenticationException('User email not verified.');
      }

      $userId = auth()->user()->id;

      $data['token'] = $this->respondWithToken($userId, $token);

      if ($user->hasRole(User::ROLE_ADMIN)) {
        // Sementara response json
        return response()->json([
          'status' => 'success',
          'message' => 'Admin Login success',
          'data' => $data,
        ], 200);

        // kalau udah ada view dashboard, direct ke admin dashboard
      }

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
      ], 200);
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

  public function refreshToken()
  {
    $token = JWTAuth::getToken();
    
    if(!$token) { 
      return response()->json(['message' => 'Token not provided'], 401);
    }

    try {
      $newToken = JWTAuth::refresh($token);
      return response()->json([
        'status' => 'success',
        'message' => 'Session has been extended successfully',
        'token' => $newToken,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Token refresh failed',
        'error_detail' => $e->getMessage(),
      ], 500);
    }
  }

  public function checkTokenDuration()
  {
    try {
      $expiration = JWTAuth::getPayload()->get('exp');
      $remainingTime = $expiration - time();

      return response()->json([
        'status' => 'success',
        'message' => 'Token TTL retrieved successfully',
        'ttl' => $remainingTime,
      ], 200);
    } catch (TokenExpiredException $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Token has already expired',
        'error' => $e->getMessage(),
      ], 401);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error retrieving token TTL',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
