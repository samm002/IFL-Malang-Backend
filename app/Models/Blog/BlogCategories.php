<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BlogCategories extends Pivot
{
  use HasFactory, HasUuids;

  protected $table = 'blog_categories';

  protected $fillable = [
    'blog_id',
    'categories_id',
  ];
}
