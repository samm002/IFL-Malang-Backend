<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    try {
      $user = User::create([
        'name' => $request->input("name"),
        'email' => $request->input("email"),
        'password' => Hash::make($request->input("password")),
      ]);

      $user->roles()->attach(Role::where('name', 'user')->first());

      $data["user"] = $user;

      return response()->json([
        'status' => 'success',
        'message' => 'User registered successfully',
        'data' => $data,
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'error' => 'Failed to register user',
        'detail' => $e->getMessage(),
      ], 500);
    }
  }

  public function login(Request $request)
  {
    try {
      $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
      ]);

      $credentials = request(['email', 'password']);

      if (!$token = auth()->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
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
        'error' => 'Failed to register user',
        'detail' => $e->getMessage(),
      ], $e instanceof AuthenticationException ? 401 : 500);
    }
  }

  public function logout()
  {
    auth()->logout();
    return response()->json(['message' => 'Successfully logged out'], 200);
  }

  public function me()
  {
    $data['user'] = auth()->user();
    return response()->json([
      'status' => 'success',
      'message' => 'Get profile',
      'data' => $data,
    ], 200);
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
