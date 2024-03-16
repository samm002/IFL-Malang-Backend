<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Blog_Comment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'blog_comment';

    protected $fillable = [
        'blog_id',
        'comment_id',
    ];
}
