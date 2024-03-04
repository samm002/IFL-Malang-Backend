<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Campaign extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
      'name',
      'type',
      'current_donation',
      'target_donation',
      'start_date',
      'end_date',
      'description',
      'photo',
    ];

    public function donations()
    {
      return $this->hasMany(Donation::class);
    }
}
