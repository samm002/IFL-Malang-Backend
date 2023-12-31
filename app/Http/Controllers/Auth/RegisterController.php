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
    $data = $request->only('username', 'email', 'password', 'password_confirmation');
    $validator = Validator::make($data, [
      'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/\w*$/'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => [
        'required',
        'min:8',
        'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[\W]).*$/',
        'confirmed'
      ],
    ]);

    if ($validator->fails()) {
      $errors = $validator->messages();

      if ($errors->has('password')) {
        $errors->add('detail', 'Password harus berisi setidaknya : 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 simbol (seperti !, @, $, #, ^, dll)');
      }

      return response()->json(['error' => $errors], 400);
    }

    try {
      $user = User::create([
        'username' => $request->input("username"),
        'email' => $request->input("email"),
        'password' => Hash::make($request->input("password")),
      ]);

      $role = Role::where('name', 'user')->first();

      $user->roles()->attach($role);

      $hasRole = $user->roles()->pluck('name')->first();

      $user->sendEmailVerificationNotification();

      $data["user"] = $user;
      $data["user"]["role"] = $hasRole;

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
