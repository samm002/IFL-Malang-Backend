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
    $roles = Role::all();
    try {
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
        'name' => 'string|required|unique:roles',
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
  public function show(string $id)
  {
    $role = Role::find($id);
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
        'message' => 'Get role by id success',
        'data' => $role,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Get role by id failed',
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
  public function update(Request $request, string $id)
  {
    $role = Role::find($id);
    if (!$role) {
      return response()->json([
        'status' => 'error',
        'message' => 'Role not found with the given ID',
      ], 404);
    }
    try {
      $request->validate([
        'name' => 'string|nullable|unique:roles',
        'description' => 'string|nullable',
      ]);

      $role->update([
        'name' => $request->input('name') ?? $role->name,
        'description' => $request->input('description') ?? $role->description,
      ]);

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
  public function destroy(string $id)
  {
    $role = Role::find($id);
    try {
      if (!$role) {
        return response()->json([
          'status' => 'error',
          'message' => 'Role not found with the given ID',
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
