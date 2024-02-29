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
              'author' => 'string|required',
              'title' => 'string|required',
              'content' => 'string|required',
              'like' => 'integer',
              'categories' => 'string|required',
              'comments' => 'string|nullable',
              'image' => 'string|nullable',
            ]);
      
            $blog = new Blog;
            
            $userId = auth()->user()->id;
            $blog->author = $userId;
            $blog->title = $request->input('title');
            $blog->content = $request->input('content');
            $blog->like = $request->input('like');
            $categories = $request->input('categories');
            $blog->categories()->attach($categories);
            $blog->comments = $request->input('comments');
            $blog->image = $request->input('image');
      
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

    public function like(Request $request, Blog $blog)
    {
        $user = $request->user();
        
        if (!$user->likes()->where('blog_id', $blog->id)->exists()) {
            $like = new Like();
            $like->user_id = $user->id;
            $like->blog_id = $blog->id;
            $like->save();
        }

        return response()->json(['message' => 'Liked successfully']);
    }
}
