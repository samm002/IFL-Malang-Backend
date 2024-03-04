<?php

namespace App\Http\Controllers\Blog;

use App\Models\Blog\Blog;
use App\Models\Blog\Like;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        try {
            $request->validate([
              'author' => 'string|required', // id author
              'title' => 'string|required',
              'content' => 'string|required',
              'like' => 'integer|nullable',
              'categories' => 'string|required', // id category
              'comments' => 'string|nullable', // id comment
              'image' => 'string|nullable',
            ]);
      
            $blog = new Blog;
            
            $userId = auth()->user()->id;

            $blog->author = $userId;
            $blog->title = $request->input('title');
            $blog->content = $request->input('content');
            $blog->like = $request->input('like');
            $categories = $request->input('categories');
            $blog->categories = $categories;
            $blog->comments = $request->input('comments');
            $blog->image = $request->input('image');
            
            $blog->save();
            
            $blog->categories()->attach($categories);
      
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
     * @param  \App\Models\Blog\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function editBlog(Request $request, Blog $blog)
    {
      try {
        $request->validate([
          'author' => 'string|required', // id author
          'title' => 'string|required',
          'content' => 'string|required',
          'like' => 'integer|nullable',
          'categories' => 'string|required', // id category
          'comments' => 'string|nullable', // id comment
          'image' => 'string|nullable',
        ]);
  
        if (!$blogs) {
          return response()->json([
            'status' => 'error',
            'message' => 'Blog not found with the given ID',
            'error' => 'Not Found',
          ], 404);
        }
  
        $blogs->categories = $request->input('categories');
        $blogs->title = $request->input('title');
        $blogs->author = $request->input('author');
        $blogs->content = $request->input('content');
        $blogs->like = $request->input('like');
        $blogs->comments = $request->input('comments');
        $blogs->image = $request->input('image');
        $blogs->save();
  
        return response()->json([
          'status' => 'success',
          'message' => 'Blog updated successfully',
          'data' => $blogs,
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Error updating blog',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
      try {
        if (!$blog) {
          return response()->json([
            'status' => 'error',
            'message' => 'Blog not found with the given ID',
            'error' => 'Not Found',
          ], 404);
        }
  
        $blog->delete();
  
        return response()->json([
          'status' => 'success',
          'message' => 'Blog deleted successfully',
          'data' => $blog,
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Error deleting blog',
          'error' => $e->getMessage(),
        ], 500);
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
