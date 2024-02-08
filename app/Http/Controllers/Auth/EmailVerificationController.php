<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class EmailVerificationController extends Controller
{
  public function verify($id, Request $request)
  {
    try {
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
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Email has already been verified',
        ], 400);
      }

      // Continue with your success response
      // return redirect("http://127.0.0.1:5173/verify?mail=$user->email");
      return response()->json([
        'status' => 'success',
        'message' => 'Email verified successfully, directing to login page'
      ], 200);
    } catch (AuthorizationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Authorization error',
        'error' => $e->getMessage(),
      ], 403);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error verifying email',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function resend(Request $request)
  {
    try {

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
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error resending verification email',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function checkEmailVerified(Request $request)
  {
    try {
      $email = $request->input('email');
      $user = User::where('email', $email)->first();

      if ($user->email_verified_at) {
        return response()->json([
          'status' => 'success',
          'message' => 'This email has been verified'
        ], 200);
      } else {
        return response()->json([
          'status' => 'success',
          'message' => 'This email has not been verified, please verify using the link provided',
          'verify link' => route('verification.resend'),
        ], 200);
      }
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error resending verification email',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
