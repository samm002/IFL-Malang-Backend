<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->middleware(['jwt.verify', 'verified'], ['except' => ['login', 'register', 'verify', 'notice']]);
  }

  public function verify($id, Request $request)
  {
    if (!$request->hasValidSignature()) {
      return response()->json([
        'status' => 'error',
        'message' => 'Email verification failed'
      ], 400);
    }

    $user = User::find($id);

    if (!$user->hasVerifiedEmail()) {
      $user->markEmailAsVerified();
    }

    // return redirect()->route('login)

    // sementara direct ke home karena gapunya view login
    return redirect()->to('/');

  }

  public function resend()
  {
    if (auth()->user()->hasVerifiedEmail()) {
      return response()->json([
        'status' => 'success',
        'message' => 'Email have been verified, no verification action needed'
      ], 200);
    }

    auth()->user()->sendEmailVerificationNotification();

    return response()->json([
      'status' => 'success',
      'message' => 'Verification link has been sent to your email'
    ], 200);
  }

  public function notice()
  {
    return response()->json([
      'status' => 'error',
      'message' => 'Anda belum verifikasi email'
    ]);
  }
}
