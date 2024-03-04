<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
  public function getAllUser()
  {
    try {
      $users = User::all();

      foreach ($users as $user) {
        $roleNames = $user->roles->pluck('name')->toArray();
        $user->role = $roleNames[0];
        unset($user->roles);
      }

      return response()->json([
        'status' => 'success',
        'message' => 'Get all user success',
        'data' => $users,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Get all users failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function getUserByEmail($email)
  {
    try {
      $user = User::where('email', $email)->firstOrFail();

      return response()->json([
        'status' => 'success',
        'message' => 'Get user by email success',
        'email' => $email,
        'data' => $user,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Get user by email failed',
        'error' => 'User with email : ' . $email . ' not found',
      ], 500);
    }
  }

  public function getAllVerifiedUser()
  {
    try {
      $users = User::whereNotNull('email_verified_at')->get();

      return response()->json([
        'status' => 'success',
        'message' => 'Get all verified user success',
        'verified' => true,
        'data' => $users,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Get all verified user failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function getAllNotVerifiedUser()
  {
    try {
      $users = User::whereNull('email_verified_at')->get();

      return response()->json([
        'status' => 'success',
        'message' => 'Get all not verified user success',
        'verified' => false,
        'data' => $users,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Get all not verified user failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
