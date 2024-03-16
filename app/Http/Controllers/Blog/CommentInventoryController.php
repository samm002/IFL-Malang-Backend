<?php

namespace App\Http\Controllers\Blog;

use App\Models\Blog\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getComments()
    {
        try {
            $comments = Comment::all();
      
            return response()->json([
              'status' => 'success',
              'message' => 'Get all comments success',
              'data' => $comments,
            ], 200);
          } catch (\Exception $e) {
            return response()->json([
              'status' => 'error',
              'message' => 'Get all comments failed',
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
    public function addComment(Request $request)
    {
        try {
            $request->validate([
              'blog' => 'string|required', //id blog
              'content' => 'string|required',
              'like' => 'integer|nullable',
            ]);
      
            $comment = new Comment;
            
            $comment->blog = $request->input('blog');
            $comment->content = $request->input('content');
            $comment->like = $request->input('like');
            $userId = auth()->user()->id;

            $comment->author = $userId;
      
            $comment->save();
      
            return response()->json([
              'status' => 'success',
              'message' => 'Comment added successfully',
              'data' => $comment,
            ], 201);
          } catch (\Exception $e) {
            return response()->json([
              'status' => 'error',
              'message' => 'Error adding comment',
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
