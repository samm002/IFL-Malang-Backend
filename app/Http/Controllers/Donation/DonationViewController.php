<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Transaction;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;

class DonationViewController extends Controller
{
  public function donationForm()
  {
    $campaign = Campaign::all();

    return view('donation', compact('campaign'));
  }

  public function donate(Request $request, $id) {
    $user = auth()->user();

    $campaign = Campaign::find($id);
    if (!$campaign) {
      return response()->json([
        'status' => 'error',
        'message' => 'Campaign not found with the given ID',
      ], 404);
    }

    $data = $request->only('name', 'email', 'donation_amount', 'donation_message', 'status', 'user_id', 'campaign_id');
    $data['status'] = 'unpaid';
    $data['user_id'] = $user->id ?? null;

    if($user) {
      $data['email'] = $user->email;
    }

    if($request->input('anonim') == 1) {
      $data['name'] = 'anonim';
    }

    $validator = Validator::make($data, [
      'email' => ['required', 'string', 'email', 'max:255'],
      'anonim' => ['nullable', 'numeric'],
      'donation_amount' => ['required', 'numeric'],
      'donation_message' => ['nullable', 'string'],
      'status' => ['required', 'string', 'in:unpaid,pending,paid,denied,expired,canceled'],
      'user_id' => ['nullable', 'uuid'],
      'campaign_id' => ['required', 'uuid'],
    ]);

    if ($validator->fails()) {
      $errors = $validator->messages();

      return response()->json(['error' => $errors], 400);
    }
    try {
      DB::beginTransaction();
      $donation = Donation::create($data);


      $transaction = Transaction::create([
        'donation_id' => $donation->id,
        'user_id' => $donation->user_id,
      ]);

      Config::$serverKey = config('midtrans.server_key');
      Config::$isProduction = config('midtrans.isProduction');
      Config::$isSanitized = config('midtrans.isSanitized');
      Config::$is3ds = config('midtrans.is3ds');

      $transaction_details = array(
        'order_id' => $transaction->id,
        'gross_amount' => $donation->donation_amount,
      );

      $campaign_details = array(
        array(
          'id' => $campaign->id,
          'price' => $donation->donation_amount,
          'quantity' => 1,
          'name' => $campaign->title,
        )
      );

      $customer_details = array(
        'first_name' => $donation->name  ?? 'anonim',
        'email' => $donation->email,
      );

      $transaction_data = array(
        'transaction_details' => $transaction_details,
        'item_details' => $campaign_details,
        'customer_details' => $customer_details,
      );
      
      $snapToken = Snap::getSnapToken($transaction_data);

      $transaction->update(['snap_token' => $snapToken]);

      DB::commit();
      
      return view('checkout', compact('snapToken', 'donation', 'campaign', 'transaction'));
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to create donation',
        'error' => $e->getMessage(),
      ], 500);
    }    
  }

  public function invoice(Request $request, $id) 
  {
    $transaction = Transaction::find($id);
    $donation = Donation::find($transaction->donation_id);

    $transaction_success_time = Carbon::parse($transaction->transaction_success_time);

    $invoice = [
      'donation_id' => $donation->id,
      'date' => $transaction_success_time->toDateString(),
      'time' => $transaction_success_time->toTimeString(),
      'payment_method' => $transaction->payment_provider,
      'donation_amount' => $donation->donation_amount,
    ];

    return response()->json(['invoice' => $invoice], 200);
  }
}
