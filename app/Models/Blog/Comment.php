<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'comment';

    protected $fillable = [
      'author',
      'content',
      'like',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_likes');
    }

    public function like()
    {
        $this->likes()->attach(auth()->id());
        $this->increment('likes_count');
    }

    public function unlike()
    {
        $this->likes()->detach(auth()->id());
        $this->decrement('likes_count');
    }
}
