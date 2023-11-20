<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;

class RegisterController extends Controller
{
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
}
