<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
      'snap_token',
      'donation_id',
      'status',
      'payment_method',
      'bank',
      'transaction_success_time',
    ];

    public function donation()
    {
      return $this->belongsTo(Donation::class);
    }
}
