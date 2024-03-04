<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Blog\Categories;
use App\Models\Blog\Comment;

class Blog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'blog';
    protected $fillable = [
        'author', 
        'categories', 
        'title', 
        'content', 
        'image', 
        'like', 
        'comments',
    ];

    // Relasi dengan model User untuk Author
    public function author()
    {
        return $this->belongsTo(User::class, 'author');
    }

    // Relasi dengan model Categories untuk kategori
    public function categories()
    {
        return $this->belongsToMany(Categories::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

}
