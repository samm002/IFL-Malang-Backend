<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Jobs\SendActivationEmail;

class RegisterController extends Controller
{
  public function register(Request $request)
  {
    $data = $request->only('username', 'email', 'password', 'password_confirmation');
    $validator = Validator::make($data, [
      'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\S*$/'],
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
        $errors->add('detail', 'Passwords must contain at least: 1 lowercase letter, 1 uppercase letter, 1 number, and 1 symbol (such as !, @, $, #, ^, etc.)');
      }

      return response()->json(['error' => $errors], 400);
    }

    try {
      DB::beginTransaction();
      $user = User::create([
        'username' => $request->input("username"),
        'email' => $request->input("email"),
        'password' => Hash::make($request->input("password")),
        'role_id' => Role::where('name', 'user')->first()->id,
      ]);

      $role = Role::find($user->role_id);
      $user->role = $role->name;

      SendActivationEmail::dispatch($user);

      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => 'User registered successfully, please check your email for verification.',
        'data' => $user,
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to register user',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
