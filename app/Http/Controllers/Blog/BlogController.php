<?php

namespace App\Http\Controllers\Blog;

use App\Models\Blog\Like;
use App\Models\Blog\Blog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function likeBlog(Request $request)
    {
        $userId = auth()->id();
        $blogId = $request->blog_id;

        // Cek apakah pengguna telah melakukan like pada blog ini sebelumnya
        $existingLike = Like::where('user_id', $userId)
                            ->where('blog_id', $blogId)
                            ->first();

        if ($existingLike) {
            // Jika sudah melakukan like, maka unlike
            $existingLike->delete();
            return response()->json(['message' => 'Blog unliked successfully']);
        } else {
            // Jika belum melakukan like, maka like
            Like::create(['user_id' => $userId, 'blog_id' => $blogId]);
            return response()->json(['message' => 'Blog liked successfully']);
        }
    }

}
