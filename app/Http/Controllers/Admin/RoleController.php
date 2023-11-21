<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $roles = Role::all();

      if ($roles->isEmpty()) {
        return response()->json([
          'status' => 'success',
          'message' => 'No roles found to display',
          'data' => [],
        ], 200);
      }

      return response()->json([
        'status' => 'success',
        'message' => 'Roles retrieved successfully',
        'data' =>  $roles,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error retrieving roles',
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
    try {
      $request->validate([
        'name' => 'string|required',
        'description' => 'string|nullable',
      ]);

      $role = new Role;

      $role->name = $request->input('name');
      $role->description = $request->input('description');

      $role->save();

      return response()->json([
        'status' => 'success',
        'message' => 'Role added successfully',
        'data' => $role,
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error adding role',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function show(Role $role)
  {
    try {
      if (!$role) {
        return response()->json([
          'status' => 'error',
          'message' => 'Role not found with the given ID',
          'error' => 'Not Found',
        ], 404);
      }

      return response()->json([
        'status' => 'success',
        'message' => 'Role details retrieved successfully',
        'data' => $role,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error retrieving role details',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function edit(Role $role)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Role $role)
  {
    try {
      $request->validate([
        'name' => 'string|required',
        'description' => 'string|nullable',
      ]);

      if (!$role) {
        return response()->json([
          'status' => 'error',
          'message' => 'Role not found with the given ID',
          'error' => 'Not Found',
        ], 404);
      }

      $role->name = $request->input('name');
      $role->description = $request->input('description');
      $role->save();

      return response()->json([
        'status' => 'success',
        'message' => 'Role updated successfully',
        'data' => $role,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error updating role',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function destroy(Role $role)
  {
    try {
      if (!$role) {
        return response()->json([
          'status' => 'error',
          'message' => 'Role not found with the given ID',
          'error' => 'Not Found',
        ], 404);
      }

      $role->delete();

      return response()->json([
        'status' => 'success',
        'message' => 'Role deleted successfully',
        'data' => $role,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error deleting role',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
