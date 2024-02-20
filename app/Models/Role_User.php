<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Role_User extends Pivot
{
    use HasFactory, HasUuids;

    protected $table = 'role_user';

    protected $fillable = [
      'user_id',
      'role_id',
    ];
}
