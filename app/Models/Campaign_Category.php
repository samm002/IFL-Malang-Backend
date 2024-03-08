<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Campaign_Category extends Pivot
{
    use HasFactory, HasUuids;

    protected $fillable = [
      'campaign_id',
      'category_id',
    ];
}
