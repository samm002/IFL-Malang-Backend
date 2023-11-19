<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->middleware(['jwt.verify', 'verified'], ['except' => ['login', 'register', 'verify', 'notice']]);
  }
  public function register(Request $request)
  {
    $data = $request->only('name', 'email', 'password', 'password_confirmation');
    $validator = Validator::make($data, [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->messages()], 200);
    }

    try {
      $user = User::create([
        'name' => $request->input("name"),
        'email' => $request->input("email"),
        'password' => Hash::make($request->input("password")),
      ]);

      $user->roles()->attach(Role::where('name', 'user')->first());

      $user->sendEmailVerificationNotification();

      $data["user"] = $user;

      return response()->json([
        'status' => 'success',
        'message' => 'User registered successfully, please check your email for verification.',
        'data' => $data,
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to register user',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

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
      return response()->json([
        'status' => 'error',
        'message' => 'Could not create token.',
        'error' => $e->getMessage(),
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
      return response()->json([
        'status' => 'error',
        'message' => 'Log out failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function verify($id, Request $request)
  {
    if (!$request->hasValidSignature()) {
      return response()->json([
        'status' => 'error',
        'message' => 'Email verification failed'
      ], 400);
    }

    $user = User::find($id);

    if (!$user->hasVerifiedEmail()) {
      $user->markEmailAsVerified();
    }

    // return redirect()->route('login)

    // sementara direct ke home karena gapunya view login
    return redirect()->to('/');

  }

  public function resend()
  {
    if (auth()->user()->hasVerifiedEmail()) {
      return response()->json([
        'status' => 'success',
        'message' => 'Email have been verified before'
      ], 200);
    }

    auth()->user()->sendEmailVerificationNotification();

    return response()->json([
      'status' => 'success',
      'message' => 'Verification link has been sent to your email'
    ], 200);
  }

  public function notice()
  {
    return response()->json([
      'status' => 'error',
      'message' => 'Anda belum verifikasi email'
    ]);
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
