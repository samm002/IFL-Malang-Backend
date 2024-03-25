<?php

namespace App\Http\Controllers\Blog;

use App\Models\Blog\Blog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function likeBlog(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->increment('like');
        $blog->save();

        return response()->json(['message' => 'Blog liked successfully'], 200);
    }

    public function dislikeBlog(Request $request, $blogId)
    {
        $blog = Blog::findOrFail($blogId);
        $blog->decrement('like');
        $blog->save();

        return response()->json(['message' => 'Blog disliked successfully'], 200);
    }

}
