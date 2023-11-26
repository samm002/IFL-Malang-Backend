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

        if ($user->username) {
          $userData = [
            'pivot_id' => $pivotId,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $role->name,
            'updated_at' => $role_user->updated_at,
          ];
        } elseif ($user->name) {
          $userData = [
            'pivot_id' => $pivotId,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $role->name,
            'updated_at' => $role_user->updated_at,
          ];
        } else {
          $userData = [
            'pivot_id' => $pivotId,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $role->name,
            'updated_at' => $role_user->updated_at,
          ];
        }

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

  public function show(Role_User $role_user)
  {
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

  public function showByUserId(User $user)
  {
    try {
      if (!$user) {
        return response()->json([
          'status' => 'error',
          'message' => 'user not found with the given ID',
          'error' => 'Not Found',
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

  public function showByRoleId(Role $role)
  {
    try {
      if (!$role) {
        return response()->json([
          'status' => 'error',
          'message' => 'user not found with the given ID',
          'error' => 'Not Found',
        ], 404);
      }

      $role_users = Role_User::where('role_id', $role->id)->get();

      $roleUsersData = [];

      foreach ($role_users as $role_user) {
        $data = [
          'pivot_id' => $role_user->id,
          'user' => User::find($role_user->user_id),
        ];

        $roleUsersData[] = $data;
      }

      if (!$role_user) {
        return response()->json([
          'status' => 'error',
          'message' => 'user with role' . $role->name . 'not found',
          'error' => 'Not Found',
        ], 404);
      }

      // $user['pass'] = $user->password;
      $role = Role::find($role->id);

      return response()->json([
        'status' => 'success',
        'message' => 'Role_user details by user id retrieved successfully',
        'data' => [
          'role' => $role,
          'user' => $roleUsersData
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

  public function showByRoleId2(Role $role)
  {
    try {
      if (!$role) {
        return response()->json([
          'status' => 'error',
          'message' => 'Role not found with the given ID',
          'error' => 'Not Found',
        ], 404);
      }

      // Assuming you have a pivot table named role_user
      $users = $role->users;

      // If you want to include additional information, you can customize the response
      $responseData = $users->map(function ($user) use ($role) {
        $user['pass'] = $user->password;
        return [
          'pivot_id' => $user->pivot->id,
          'user' => $user,
          'role' => $role,
        ];
      });

      return response()->json([
        'status' => 'success',
        'message' => 'Users with the given role ID retrieved successfully',
        'data' => $responseData,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error retrieving user details by role ID',
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
  public function update(Request $request, Role_User $role_user)
  {
    $user_id = $role_user->user_id;
    try {
      $request->validate([
        'role_id' => 'uuid|required',
      ]);

      if (!$role_user) {
        return response()->json([
          'status' => 'error',
          'message' => 'Role_User not found with the given ID',
          'error' => 'Not Found',
        ], 404);
      }
      $role_user->user_id = $user_id;
      $role_user->role_id = $request->input('role_id');
      $role_user->save();

      $role_user->touch();

      //atau
      // $role_user->updated_at = now();

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

  public function updateByUserId(Request $request, User $user)
  {
    try {
      $request->validate([
        'role_id' => 'uuid|nullable',
      ]);

      if (!$user) {
        return response()->json([
          'status' => 'error',
          'message' => 'User not found with the given ID',
          'error' => 'Not Found',
        ], 404);
      }

      $role_user = Role_User::where('user_id', $user->id)->first();

      if (!$role_user) {
        return response()->json([
          'status' => 'error',
          'message' => 'Role_User not found for the given user ID',
          'error' => 'Not Found',
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
