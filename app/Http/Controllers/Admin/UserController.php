<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
  public function getAllUser()
  {
    try {    
      $users = User::all();

      foreach ($users as $user) {
        $role = Role::find($user->role_id);
        $user->role = $role->name;
      }

      $users_total = $users->count(); 

      return response()->json([
        'status' => 'success',
        'message' => 'Get all user success',
        'total' => $users_total,
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

  public function getUserByEmail(string $email)
  {
    try {
      $user = User::where('email', $email)->firstOrFail();
      $role = Role::find($user->role_id);
      $user->role = $role->name;

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

  public function getUserByid(string $id)
  {
    try {
      $user = User::where('id', $id)->firstOrFail();
      $role = Role::find($user->role_id);
      $user->role = $role->name;

      return response()->json([
        'status' => 'success',
        'message' => 'Get user by id success',
        'id' => $id,
        'data' => $user,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Get user by id failed',
        'error' => 'User with id : ' . $id . ' not found',
      ], 500);
    }
  }

  public function getAllVerifiedUser()
  {
    try {
      $users = User::whereNotNull('email_verified_at')->get();

      foreach ($users as $user) {
        $role = Role::find($user->role_id);
        $user->role = $role->name;
      }

      $users_total = $users->count(); 

      return response()->json([
        'status' => 'success',
        'message' => 'Get all verified user success',
        'total' => $users_total,
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
      foreach ($users as $user) {
        $role = Role::find($user->role_id);
        $user->role = $role->name;
      }
      $users_total = $users->count(); 

      return response()->json([
        'status' => 'success',
        'message' => 'Get all not verified user success',
        'total' => $users_total,
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

  public function createUser(Request $request) 
  {
    try {
      $request->validate([
        'name' => 'string|nullable',
        'username' => 'string|nullable|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'phone_number' => 'numeric|nullable',
        'gender' => 'string|nullable|in:male,female,not specified',
        'birth_date' => 'date|nullable',
        'address' => 'string|nullable',
        'about_me' => 'string|nullable',
        'profile_picture' => 'mimes:jpg,jpeg,png,webp|max:16384|nullable',
        'background_picture' => 'mimes:jpg,jpeg,png,webp|max:16384|nullable',
        'role_id' => 'uuid|nullable|exists:roles,id',
      ]);
      
      $inputRole = $request->input('role_id');
      
      $user_role = Role::where('name', 'user')->first()->id;

      $data = $request->all();
      $data['password'] = Hash::make($request->input("password"));
      $data['role_id'] = $inputRole ?? $user_role;
      $data['email_verified_at'] = now();

      $user = new User;
      $user->fill($data);

      $user->save();

      if ($request->has('profile_picture')) {
        $path = public_path("assets/image/user/profile_picture");
        $timestamp = date('d-m-Y_H-i-s');
        $profilePicture = $user->username . "-profile-" . $timestamp . '.' . $request->profile_picture->extension();
        $request->photo->move($path, $profilePicture);
        $user->profile_picture = $profilePicture;
        $user->save();
      }

      if ($request->has('background_picture')) {
        $path = public_path("assets/image/user/background_picture");
        $timestamp = date('d-m-Y_H-i-s');
        $backgroundPicture = $user->username . "-background-" . $timestamp . '.' . $request->background_picture->extension();
        $request->photo->move($path, $backgroundPicture);
        $user->background_picture = $backgroundPicture;
        $user->save();
      }

      $role = Role::find($user->role_id);
      $user->role = $role->name;

      return response()->json([
        'status' => 'success',
        'message' => 'User created successfully',
        'data' => $user,
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Create user failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function updateUser(Request $request, string $id) 
  {
    $user = User::find($id);
      if (!$user) {
        return response()->json([
          'status' => 'error',
          'message' => 'User update failed',
          'detail' => 'User not found with the given ID',
        ], 404);
      }
    try {
      $request->validate([
        'name' => 'string|nullable',
        'username' => 'string|nullable|unique:users,id',
        'password' => ['nullable', 'min:8', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[\W]).*$/', 'confirmed'],
        'phone_number' => 'numeric|nullable',
        'gender' => 'string|nullable|in:male,female,not specified',
        'birth_date' => 'date|nullable',
        'address' => 'string|nullable',
        'about_me' => 'string|nullable',
        'profile_picture' => 'mimes:jpg,jpeg,png,webp|max:16384|nullable',
        'background_picture' => 'mimes:jpg,jpeg,png,webp|max:16384|nullable',
        'role_id' => 'uuid|nullable',
      ]);

      if ($request->has('profile_picture')) {
        $path = public_path("assets/image/user/profile_picture");

        if ($user->profile_picture) {
          File::delete($path . '/' . $user->profile_picture);
        }

        $timestamp = date('d-m-Y_H-i-s');
        $profilePicture = $user->username . "-profile-" . $timestamp . '.' . $request->profile_picture->extension();
        $request->profile_picture->move($path, $profilePicture);
      }

      if ($request->has('background_picture')) {
        $path = public_path("assets/image/user/background_picture");

        if ($user->background_picture) {
          File::delete($path . '/' . $user->background_picture);
        }

        $timestamp = date('d-m-Y_H-i-s');
        $backgroundPicture = $user->username . "-profile-" . $timestamp . '.' . $request->background_picture->extension();
        $request->background_picture->move($path, $backgroundPicture);
      }

      $inputRole = $request->input('role');

      if($inputRole) {
        $role = Role::where('name', $inputRole)->first();

        if (!$role) {
          return response()->json([
            'status' => 'error',
            'message' => (string) $inputRole . 'not found',
          ], 404);
        }
      }

      $user->update([
        'name' => $request->input('name') ?? $user->name,
        'username' => $request->input('username') ?? $user->username,
        'phone_number' => $request->input('phone_number') ?? $user->phone_number,
        'gender' => $request->input('gender') ?? $user->gender,
        'birth_date' => $request->input('birth_date') ?? $user->birth_date,
        'address' => $request->input('address') ?? $user->address,
        'about_me' => $request->input('about_me') ?? $user->about_me,
        'profile_picture' => $profilePicture ?? $user->profile_picture,
        'background_picture' => $backgroundPicture ?? $user->background_picture,
        'role_id' => $role ?? $user->role_id,
      ]);

      $role = Role::find($user->role_id);
      $user->role = $role->name;

      return response()->json([
        'status' => 'success',
        'message' => 'Update user success',
        'data' => $user,
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Update user failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function deleteUser(string $id)
  {
    $user = User::find($id);
      if (!$user) {
        return response()->json([
          'status' => 'error',
          'message' => 'User delete failed',
          'detail' => 'User not found with the given ID',
        ], 404);
      }

    try {
      $user->delete();

      return response()->json([
        'status' => 'success',
        'message' => 'User deleted success',
        'data' => $user,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error deleting user',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
