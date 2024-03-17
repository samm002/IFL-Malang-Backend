<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionController extends Controller
{
  public function paymentCallback(Request $request)
  {
    $status_code = $request->status_code;
    $fraud = $request->fraud_status;
    $transaction_status = $request->transaction_status;
    $serverKey = config('midtrans.server_key');
    $hashed = hash('sha512', $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
    
    $transaction_id = $request->order_id;
    $payment_method = $request->payment_type;
    $donation_amount = $request->gross_amount;
    $va_numbers = $request->va_numbers;

    if ($va_numbers) {
      $payment_provider = $request->va_numbers[0]['bank'];
      $va_number = $request->va_numbers[0]['va_number'];
    } else {
      $payment_provider = $request->issuer;
    }

    if($hashed != $request->signature_key) {
      return response()->json(['error' => 'Invalid signature'], 400);
    } else {
      $transaction = Transaction::find($transaction_id);
      $donation = Donation::find($transaction->donation_id);
      $campaign = Campaign::find($donation->campaign_id);

      switch ($request->transaction_status) {
        case 'capture':
          if ($request->payment_type === 'credit_card' && $request->fraud_status === 'accept') {
              $this->updateTransactionStatus($transaction, $donation, 'paid', $payment_method, $payment_provider, $va_number ?? null);
          }
          $this->updateCurrenDonation($campaign, $donation_amount);
          break;

        case 'settlement':
          $this->updateTransactionStatus($transaction, $donation, 'paid', $payment_method, $payment_provider, $va_number ?? null);
          $this->updateCurrenDonation($campaign, $donation_amount);
          break;

        case 'pending':
          $this->updateTransactionStatus($transaction, $donation, 'pending', $payment_method, $payment_provider, $va_number ?? null);
          break;

        case 'deny':
          $this->updateTransactionStatus($transaction, $donation, 'denied', $payment_method, $payment_provider, $va_number ?? null);
          break;

        case 'expire':
          $this->updateTransactionStatus($transaction, $donation, 'expired', $payment_method, $payment_provider, $va_number ?? null);
          break;

        case 'cancel':
          $this->updateTransactionStatus($transaction, $donation, 'canceled', $payment_method, $payment_provider, $va_number ?? null);
          break;

        default:
          return response()->json(['error' => 'Invalid transaction status'], 400);
          break;  
      }
    }

    return response()->json([
      'status' => 'success',
      'message' => 'Succes calling payment callback',
      'data' => [
        'transaction_data' => $transaction,
        'donation_status' => $donation->status,
        'campaign_status' => [
          'campaign_name' => $campaign->title,
          'current_donation' => $campaign->current_donation,
          'target_donation' => $campaign->target_donation,
        ]
      ]
    ], 200);
  }
    
  private function updateTransactionStatus($transaction, $donation, $status, $paymentMethod, $paymentProvider, $vaNumber)
  {
    $transaction->update([
      'transaction_success_time' => now(),
      'payment_method' => $paymentMethod,
      'payment_provider' => $paymentProvider,
      'va_number' => $vaNumber,
    ]);
    
    $donation->update(['status' => $status]);
  }

  private function updateCurrenDonation($campaign, $donation_amount)
  {
    $campaign->update(['current_donation' => $campaign->current_donation + $donation_amount]);
  }
    
}
