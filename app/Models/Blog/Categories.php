<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'categories';

    protected $fillable = [
      'categories',
      'qty',
    ];

    public function increaseQty()
    {
        $this->qty++;
        $this->save();
    }
    
    public function blogs()
    {
        return $this->belongsToMany(Blog::class);
    }
}
