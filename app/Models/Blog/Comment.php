<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Blog\Blog;
use App\Models\User;

class Comment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'comment';

    protected $fillable = [
      'blog',
      'author',
      'content',
      'like',
    ];

    public function blog()
    {
      return $this->belongsTo(Blog::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
