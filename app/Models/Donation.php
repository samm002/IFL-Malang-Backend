<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Donation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
      'name',
      'email',
      'anonim',
      'donation_amount',
      'donation_message',
      'donation_time',
      'status',
      'campaign_id'
      'user_id'
    ];

    public function campaign()
    {
      return $this->belongsTo(Campaign::class);
    }

    public function transaction()
    {
      return $this->hasOne(Transaction::class);
    }

    public function getPaymentStatusAttribute()
    {
        // Get the latest transaction associated with the donation
        $latestTransaction = $this->transactions()->latest()->first();

        // Return the payment status if a transaction exists
        return $latestTransaction ? $latestTransaction->payment_status : 'pending';
    }
}
