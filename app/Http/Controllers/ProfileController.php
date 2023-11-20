<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{
  public function showProfile()
  {
    // $user = auth()->user();
    $user = Auth::user();
    $role = $user->roles()->pluck('name')->first();

    $user['password'] = auth()->user()->password;
    $user['role'] = $role;

    return response()->json([
      'status' => 'success',
      'message' => 'Get profile',
      'data' => $user,
    ], 200);
  }

  public function updateProfile(Request $request)
    {
        $request->validate([
          'username' => 'string|nullable',
          'address' => 'string|nullable',
          'phone_number' => 'numeric|nullable',
          'about_me' => 'string|nullable',
          'profile_picture' => 'mimes:jpg,jpeg,png,webp|max:16384|nullable',
        ]);

        $user = auth()->user();

        if($request->has('profile_picture')) {
          $path = public_path("/img/user/profile_picture");
          
          if($user->profile_picture && $user->profile_picture !== 'default.png') {
            File::delete($path . '/' . $user->profile_picture);
          }

          $posterImage = $user->id . "_profile_" . time() . '.' . $request->profile_picture->extension();
          $request->profile_picture->move($path, $posterImage);
          
          $user->profile_picture = $posterImage;
      }

        $user->update([
          'username' => $request->input('username'),
          'address' => $request->input('address'),
          'phone_number' => $request->input('phone_number'),
          'about_me' => $request->input('about_me'),
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
    }
}
