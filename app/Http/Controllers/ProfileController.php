<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
  public function showProfile()
  {
    $user = auth()->user()->toArray();

    $user['password'] = auth()->user()->password;

    return response()->json([
      'status' => 'success',
      'message' => 'Get profile',
      'data' => [
        'user' => $user,
      ],
    ], 200);
  }
}
