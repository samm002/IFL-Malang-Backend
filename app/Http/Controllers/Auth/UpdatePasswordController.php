<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdatePasswordController extends Controller
{
  public function updatePassword(Request $request)
  {
    try {
      $data = $request->only('current_password', 'new_password', 'new_password_confirmation');
      $validator = Validator::make($data, [
        'current_password' => 'required|min:8',
        'new_password' => [
          'required',
          'min:8',
          'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[\W]).*$/',
          'confirmed'
        ],        
      ]);

      if ($validator->fails()) {
        $errors = $validator->messages();
  
        if ($errors->has('new_password')) {

          $errors->add('detail', 'Password harus berisi setidaknya : 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 simbol (seperti !, @, $, #, ^, dll)');
        }
  
        return response()->json(['error' => $errors], 400);
      }

      $user = auth()->user();

      if (!Hash::check($request->input('current_password'), $user->password)) {
        return response()->json([
          'status' => 'error',
          'message' => 'Current password is incorrect.',
        ], 401);
      }

      if (Hash::check($request->input('new_password'), $user->password)) {
        return response()->json([
          'status' => 'error',
          'message' => 'Your new password is the same as the current password',
        ], 400);
      }

      $user->password = Hash::make($request->input('new_password'));
      $user->save();

      return response()->json([
        'status' => 'success',
        'message' => 'Password changed successfully.',
      ], 200);
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Validation error',
        'errors' => $e->errors(),
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error changing password',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
