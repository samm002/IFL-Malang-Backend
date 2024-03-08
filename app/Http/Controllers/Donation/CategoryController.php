<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $category = Category::all();
      try {
        return response()->json([
            'status' => 'success',
            'message' => 'Get all category success',
            'data' => $category,
          ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Get all category failed',
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
          'name' => 'string|required|unique:categories',
          'description' => 'string|nullable',
        ]);
  
        $category = new Category;
  
        $category->name = $request->input('name');
        $category->description = $request->input('description');
  
        $category->save();
  
        return response()->json([
          'status' => 'success',
          'message' => 'Category added successfully',
          'data' => $category,
        ], 201);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Error adding category',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
      $category = Category::find($id);
      if (!$category) {
        return response()->json([
          'status' => 'error',
          'message' => 'Category not found with the given ID',
        ], 404);
      }
      try {
  
        return response()->json([
          'status' => 'success',
          'message' => 'Get category by id success',
          'data' => $category,
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Get category by id failed',
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
    public function update(Request $request, string $id)
    {
      $category = Category::find($id);
      if (!$category) {
        return response()->json([
          'status' => 'error',
          'message' => 'Category not found with the given ID',
        ], 404);
      }
      try {
        $request->validate([
          'name' => 'string|nullable|unique:categories',
          'description' => 'string|nullable',
        ]);
  
        $category->update([
          'name' => $request->input('name') ?? $category->name,
          'description' => $request->input('description') ?? $category->description,
        ]);
  
        return response()->json([
          'status' => 'success',
          'message' => 'category updated successfully',
          'data' => $category,
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Error updating category',
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
    public function destroy(string $id)
    {
      $category = Category::find($id);
    try {
      if (!$category) {
        return response()->json([
          'status' => 'error',
          'message' => 'Category not found with the given ID',
        ], 404);
      }

      $category->delete();

      return response()->json([
        'status' => 'success',
        'message' => 'category deleted successfully',
        'data' => $category,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error deleting category',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
