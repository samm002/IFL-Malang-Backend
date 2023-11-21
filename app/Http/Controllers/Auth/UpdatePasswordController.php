<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordController extends Controller
{
  public function updatePassword(Request $request)
  {
    $request->validate([
      'current_password' => 'required|min:8',
      'new_password' => 'required|min:8|confirmed'
    ]);

    $user = auth()->user();

    if (!Hash::check($request->input('current_password'), $user->password)) {
      return response()->json([
        'status' => 'error',
        'message' => 'Current password is incorrect.',
      ], 401);
    }

    if (!Hash::check($request->input('new_password'), $user->password)) {
      return response()->json([
        'status' => 'error',
        'message' => 'Your new password is the same as the current password',
      ], 401);
    }

    $user->password = Hash::make($request->input('new_password'));
    $user->save();

    return response()->json([
      'status' => 'success',
      'message' => 'Password changed successfully.',
    ], 200);
  }
}
