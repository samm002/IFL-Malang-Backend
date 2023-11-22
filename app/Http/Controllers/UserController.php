<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
  public function getAllUser()
  {
    $users = User::all();
    return response()->json($users);
  }

  public function getUserByEmail($email)
  {
    $user = User::where('email', $email)->first();
    return response()->json($user);
  }


  public function getAllVerifiedUser()
  {
    $users = User::whereNotNull('email_verified_at')->get();

    return response()->json($users);
  }

  public function getAllNotVerifiedUser()
  {
    $users = User::whereNull('email_verified_at')->get();

    return response()->json($users);
  }
}
