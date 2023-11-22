<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use App\Models\User;

class EmailVerificationController extends Controller
{
  public function verify($id, Request $request)
  {
    $user = User::find($id);

    if (!hash_equals((string) $request->route('id'), (string) $id)) {
      throw new AuthorizationException;
    }

    if (!hash_equals((string) $request->route('hash'), sha1($user->email))) {
      throw new AuthorizationException;
    }

    if (!$request->hasValidSignature()) {
      return response()->json([
        'status' => 'error',
        'message' => 'Email verification failed'
      ], 400);
    }


    if (!$user->hasVerifiedEmail()) {
      $user->markEmailAsVerified();
    }

    // return redirect()->route('login)

    // sementara direct ke home karena gapunya view login
    // return redirect()->to('/');

    // daripada direct mending return response biar jelas
    return response()->json([
      'status' => 'success',
      'message' => 'Email verified successfully, directing to login page'
    ], 200);
  }

  public function resend(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 'error',
        'message' => 'Validation error',
        'error' => $validator->errors(),
      ], 400);
    }

    $user = User::where('email', $request->email)->first();

    if ($user->hasVerifiedEmail()) {
      return response()->json([
        'status' => 'success',
        'message' => 'Email have been verified, no verification action needed'
      ], 200);
    }

    $user->sendEmailVerificationNotification();

    return response()->json([
      'status' => 'success',
      'message' => 'Verification link has been sent to your email'
    ], 200);
  }
}
