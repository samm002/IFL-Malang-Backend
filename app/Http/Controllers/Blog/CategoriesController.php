<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Categories;
use Illuminate\Http\Request;


class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategories()
    {
        try {
            $categories = Categories::all();
      
            return response()->json([
              'status' => 'success',
              'message' => 'Get all categories success',
              'data' => $categories,
            ], 200);
          } catch (\Exception $e) {
            return response()->json([
              'status' => 'error',
              'message' => 'Get all categories failed',
              'error' => $e->getMessage(),
            ], 500);
          }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setCategories(Request $request)
    {
        try {
            $request->validate([
              'categories' => 'string|required',
              'qty' => 'integer|nullable',
            ]);
      
            $categories = new Categories;
            
            $categories->categories = $request->input('categories');
            $categories->qty = $request->input('qty');
      
            $categories->save();
      
            return response()->json([
              'status' => 'success',
              'message' => 'Categorie added successfully',
              'data' => $categories,
            ], 201);
          } catch (\Exception $e) {
            return response()->json([
              'status' => 'error',
              'message' => 'Error adding categorie',
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
    public function show($id)
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
    public function update(Request $request, $id)
    {
        //
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
