<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class RegisterController extends Controller
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

      $role = Role::where('name', 'user')->first();

      $user->roles()->attach($role, ['created_at' => now(), 'updated_at' => now()]);

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
}
