<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Comment_Blog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'comment_blog';

    protected $fillable = [
        'blog_id',
        'comment_id',
    ];
}
