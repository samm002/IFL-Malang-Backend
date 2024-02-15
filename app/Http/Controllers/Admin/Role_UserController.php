<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role_User;
use App\Models\User;
use App\Models\Role;

class Role_UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {

      $role_users = Role_User::all();

      $data = $role_users->map(function ($role_user) {
        $pivotId = $role_user->id;
        $user = User::find($role_user->user_id);
        $role = Role::find($role_user->role_id);

        $userData = [
          'pivot_id' => $pivotId,
          'id' => $user->id,
          'username' => $user->username,
          'name' => $user->name,
          'email' => $user->email,
          'role' => $role->name,
          'updated_at' => $role_user->updated_at,
        ];
        return $userData;
      });

      return response()->json([
        'status' => 'success',
        'message' => 'Get all role_user data success',
        'data' => $data,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Get role_user data error',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */

  public function showByPivotID($role_user_id)
  {
    $role_user = Role_User::where('id', $role_user_id)->first();
    try {
      if (!$role_user) {
        return response()->json([
          'status' => 'error',
          'message' => 'role_user not found with the given ID',
          'error' => 'Not Found',
        ], 404);
      }

      $user = User::find($role_user->user_id);
      $user['pass'] = $user->password;
      $role = Role::find($role_user->role_id);

      return response()->json([
        'status' => 'success',
        'message' => 'Role_user details retrieved successfully',
        'data' => [
          'pivot_id' => $role_user->id,
          'user' => $user,
          'role' => $role,
        ]
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error retrieving role_user details',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function showByUserId($user_id)
  {
    try {
      $user = User::where('id', $user_id)->first();
      if (!$user) {
        return response()->json([
          'status' => 'error',
          'message' => 'User not found with the given id',
        ], 404);
      }
      $role_user = Role_User::where('user_id', $user->id)->first();
      $user['pass'] = $user->password;
      $role = Role::find($role_user->role_id);

      return response()->json([
        'status' => 'success',
        'message' => 'Role_user details by user id retrieved successfully',
        'data' => [
          'pivot_id' => $role_user->id,
          'role' => $role,
          'user' => $user,
        ]
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error retrieving role_user details',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function showByRoleId($role_id)
  {
    try {
      $role = Role::find($role_id);
      if (!$role) {
        return response()->json([
          'status' => 'error',
          'message' => 'User not found with the given id',
        ], 404);
      }
      $role_users = Role_User::where('role_id', $role_id)->get();

      $roleUsersData = [];

      foreach ($role_users as $role_user) {
        $data = [
          'pivot_id' => $role_user->id,
          'user' => User::find($role_user->user_id),
        ];
        $roleUsersData[] = $data;
      }

      if ($role_users->isEmpty()) {
        return response()->json([
          'status' => 'error',
          'message' => 'No users found with role id' . $role->id,
        ], 404);
      }

      return response()->json([
        'status' => 'success',
        'message' => 'Getting users from role_id success',
        'data' => [
          'role' => $role,
          'users' => $roleUsersData,
        ],
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error retrieving role_user details',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function updateByPivotId(Request $request, $role_user_id)
  {
    try {
      $request->validate([
        'role_id' => 'uuid|required',
      ]);

      $role_user = Role_User::where('id', $role_user_id)->first();

      if (!$role_user) {
        return response()->json([
          'status' => 'error',
          'message' => 'role_user not found with the given ID',
        ], 404);
      }

      $role_user->role_id = $request->input('role_id');
      $role_user->save();

      $role_user->touch();

      return response()->json([
        'status' => 'success',
        'message' => 'Role_user updated successfully',
        'data' => $role_user,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error updating role_user',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function updateByUserId(Request $request, $user_id)
  {
    try {
      $request->validate([
        'role_id' => 'uuid|nullable',
      ]);
      
      $user = User::where('id', $user_id)->first();

      if (!$user) {
        return response()->json([
          'status' => 'error',
          'message' => 'User not found with the given id',
        ], 404);
      }

      $role_user = Role_User::where('user_id', $user->id)->first();

      if (!$role_user) {
        return response()->json([
          'status' => 'error',
          'message' => 'Role_User not found for the given user ID',
        ], 404);
      }

      $role_user->role_id = $request->input('role_id');
      $role_user->save();

      $role_user->touch();

      return response()->json([
        'status' => 'success',
        'message' => 'Role_user updated successfully',
        'data' => $role_user,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error updating role_user',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
