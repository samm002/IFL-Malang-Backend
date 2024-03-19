<?php

namespace App\Http\Controllers\Blog;

use App\Models\Blog\Blog;
use App\Models\Blog\Like;
use App\Models\Blog\Categories;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BlogInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBlogs()
    {
        try {
            $blogs = Blog::all();

            return response()->json([
              'status' => 'success',
              'message' => 'Get all blogs success',
              'data' => $blogs,
            ], 200);
          } catch (\Exception $e) {
            return response()->json([
              'status' => 'error',
              'message' => 'Get all blogs failed',
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
    public function addBlog(Request $request)
    {
      $user = auth()->user();
        $request->validate([
          'title' => 'string|required',
          'content' => 'string|required',
          'like' => 'integer|nullable',
          'categories' => 'string|required', // id category
          'comments' => 'string|nullable', // id comment
          'image' => 'image|mimes:jpg,jpeg,png,webp|max:16384',
        ]);
      try { // ini syntax biar gaada data error yang kecreate, pakai ini untuk create/update many to many. coba search laravel transaction ntar

        $data = $request->all();
        $data['author'] = $user->id;
        $blog = new Blog;
        $blog->fill($data);

        $blog->save();

            $imagePaths = [];
            if ($request->hasFile('image')) {
              foreach ($request->file('image') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path("/img/blog"), $imageName);
                $imagePaths[] = '/image/blog/' . $imageName;
            }
            }
            // $image = [];
            // if ($request->hasFile('image')) {
            //     foreach ($request->file('image') as $image) {
            //         $path = public_path("/img/blog");
            //         $image[] = $path;
            //     }
            // }
            $blog->image = json_encode($imagePaths);
            $blog->save();
          
            if ($request->has('categories')) {
              $categoryIds = $request->categories;
              $uniqueCategoryIds = array_unique($categoryIds);
              $existingCategories = Categories::whereIn('id', $categoryIds)->get();
    
              if (count($uniqueCategoryIds) !== count($categoryIds)) {
                return response()->json([
                  'status' => 'error',
                  'message' => 'Duplicate category IDs found in the input',
                ], 422);
              }
    
              if ($existingCategories->count() !== count($categoryIds)) {
                return response()->json([
                  'status' => 'error',
                  'message' => 'One or more categories not found with the given ID',
                ], 422);
              }
    
              $blog->categories()->attach($request->categories);
    
              $blog->categories->transform(function ($category) {
                return $category->categories;
              });
            }
      
            return response()->json([
              'status' => 'success',
              'message' => 'Blog added successfully',
              'data' => $blog,
            ], 201);
          } catch (\Exception $e) {
            return response()->json([
              'status' => 'error',
              'message' => 'Error adding blog',
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
    public function editBlog(Request $request, $id)
    {
      $blog = Blog::findOrFail($id);

      try {
      $request->validate([
        'content' => 'required',
        'title' => 'required',
        'categories' => 'required',
    ]);

      $blog->content = $request->input('content');
      $blog->title = $request->input('title');
      $blog->categories = $request->input('categories');

      $blog->save();

      return response()->json([
        'status' => 'success',
        'message' => 'Blog added successfully',
        'data' => $blog,
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error adding blog',
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
      $hapus = Blog::where('id', $id)->delete();

        if($hapus) {
            return response()->json([
                'status' => 0,
                'message' => 'Sukses menghapus data',
                'data' => $hapus
            ]);
        }

        else {
            return response()->json([
                'status' => 1,
                'message' => 'Sukses menghapus data',
                'data' => $hapus
            ]);
        }
    }


    public function searchByAuthor(Request $request)
    {
        $authorId = $request->input('author');

        $blogs = Blog::whereHas('author', function ($query) use ($request) {
          $query->where('username', 'LIKE', '%' . $request->author . '%');
        })->get();

        if ($blogs->isEmpty()) {
          return response()->json(['message' => 'No blogs found for the specified author'], 404);
        }

        // Jika blog ditemukan, kembalikan respons dengan data blog
        return response()->json(['data' => $blogs]);
    }

    public function searchByCategorie(Request $request)
    {
        $categoriesId = $request->input('categories');

        $blogs = Blog::whereHas('categories', function ($query) use ($request) {
          $query->where('categories', 'LIKE', '%' . $request->categories . '%');
        })->get();

        if ($blogs->isEmpty()) {
          return response()->json(['message' => 'No blogs found for the specified categorie'], 404);
        }

        // Jika blog ditemukan, kembalikan respons dengan data blog
        return response()->json(['data' => $blogs]);
    }
}
