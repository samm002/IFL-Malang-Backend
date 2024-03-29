<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Role_User;


class ProfileController extends Controller
{
  public function showProfile()
  {
    try {
      $user = auth()->user();

      $hasRole = $user->role->name;
      unset($user->role_id, $user->role);
      $user->role = $hasRole;

      // $user['pass'] = auth()->user()->password;

      return response()->json([
        'status' => 'success',
        'message' => 'Get profile success',
        'data' => $user,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'get profile failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function updateProfile(Request $request)
  {
    try {
      $request->validate([
        'name' => 'string|nullable',
        'username' => 'string|nullable|unique:users,id',
        'address' => 'string|nullable',
        'phone_number' => 'numeric|nullable',
        'about_me' => 'string|nullable',
        'profile_picture' => 'mimes:jpg,jpeg,png,webp|max:16384|nullable',
        'background_picture' => 'mimes:jpg,jpeg,png,webp|max:16384|nullable',
      ]);

      $user = auth()->user();

      $timestamp = date('d-m-Y_H-i-s');

      if ($request->has('profile_picture')) {
        $path = public_path("/assets/image/user/profile_picture");

        if ($user->profile_picture && $user->profile_picture !== 'default.png') {
          File::delete($path . '/' . $user->profile_picture);
        }

        $profilePicture = $user->username . "-profile-" . $timestamp . '.' . $request->profile_picture->extension();
        $request->profile_picture->move($path, $profilePicture);
      }

      if ($request->has('background_picture')) {
        $path = public_path("/assets/image/user/background_picture");

        if ($user->background_picture && $user->background_picture !== 'default.png') {
          File::delete($path . '/' . $user->background_picture);
        }

        $backgroundPicture = $user->username . "-background-" . $timestamp . '.' . $request->background_picture->extension();
        $request->background_picture->move($path, $backgroundPicture);
      }

      $user->update([
        'name' => $request->input('name') ?? $user->name,
        'address' => $request->input('address') ?? $user->address,
        'phone_number' => $request->input('phone_number') ?? $user->phone_number,
        'about_me' => $request->input('about_me') ?? $user->about_me,
        'profile_picture' => $profilePicture ?? $user->profile_picture,
        'background_picture' => $backgroundPicture ?? $user->background_picture,
      ]);


      /* Kalau view udah ada, bisa pake :
            - return back()->with('message', 'Your profile has been completed');
            - return redirect()->route('showProfile')->with('status', 'profile updated');
          */

      return response()->json([
        'status' => 'success',
        'message' => 'Update profile success',
        'data' => $user,
      ], 200);
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Validation error',
        'error' => $e->errors(),
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Update profile failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
