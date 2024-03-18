<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Transaction;
use Carbon\Carbon;
use Midtrans\Transaction as MidtransTransaction;

class TransactionController extends Controller
{
  private function updateSuccessTransactionStatus($transaction, $expiryTime, $transactionTime, $midtrans_transaction_id, $donation, $status, $paymentMethod, $paymentProvider, $vaNumber)
  {
    if ($paymentMethod == 'echannel') {
      $paymentMethod = 'bank_transfer';
      $paymentProvider = 'mandiri';
    }

    $transaction->update([
      'midtrans_transaction_id' => $midtrans_transaction_id,
      'transaction_expiry_time' => $expiryTime,
      'transaction_success_time' => $transactionTime,
      'payment_method' => $paymentMethod,
      'payment_provider' => $paymentProvider,
      'va_number' => $vaNumber,
    ]);
    
    $donation->update(['status' => $status]);
  }

  private function updateTransactionStatus($transaction, $expiryTime, $midtrans_transaction_id, $donation, $status, $paymentMethod, $paymentProvider, $vaNumber)
  {
    if ($paymentMethod == 'echannel') {
      $paymentMethod = 'bank_transfer';
      $paymentProvider = 'mandiri';
    }

    $transaction->update([
      'midtrans_transaction_id' => $midtrans_transaction_id,
      'transaction_expiry_time' => $expiryTime,
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

  public function paymentCallback(Request $request)
  {
    $status_code = $request->status_code;
    $fraud = $request->fraud_status;
    $transaction_status = $request->transaction_status;
    $biller_code = $request->biller_code;
    $bill_key = $request->bill_key;

    if($biller_code && $bill_key) {
      $va_number = $biller_code . " - " . $bill_key;
    }

    $serverKey = config('midtrans.server_key');
    $hashed = hash('sha512', $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
    
    $transaction_expiry_time = $request->expiry_time;
    $transaction_success_time = $request->settlement_time ?? now();
    $midtrans_transaction_id = $request->transaction_id;
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

      switch ($transaction_status) {
        case 'capture':
          if ($request->payment_type === 'credit_card' && $request->fraud_status === 'accept') {
              $this->updateSuccessTransactionStatus($transaction, $transaction_expiry_time, $transaction_success_time, $midtrans_transaction_id, $donation, 'paid', $payment_method, $payment_provider, $va_number ?? null);
          }
          $this->updateCurrenDonation($campaign, $donation_amount);
          break;

        case 'settlement':
          $this->updateSuccessTransactionStatus($transaction, $transaction_expiry_time, $transaction_success_time, $midtrans_transaction_id, $donation, 'paid', $payment_method, $payment_provider, $va_number ?? null);
          $this->updateCurrenDonation($campaign, $donation_amount);
          break;

        case 'pending':
          $this->updateTransactionStatus($transaction, $transaction_expiry_time, $midtrans_transaction_id, $donation, 'pending', $payment_method, $payment_provider, $va_number ?? null);
          break;

        case 'deny':
          $this->updateTransactionStatus($transaction, $transaction_expiry_time, $midtrans_transaction_id, $donation, 'denied', $payment_method, $payment_provider, $va_number ?? null);
          break;

        case 'expire':
          $this->updateTransactionStatus($transaction, $transaction_expiry_time, $midtrans_transaction_id, $donation, 'expired', $payment_method, $payment_provider, $va_number ?? null);
          break;

        case 'cancel':
          $this->updateTransactionStatus($transaction, $transaction_expiry_time, $midtrans_transaction_id, $donation, 'canceled', $payment_method, $payment_provider, $va_number ?? null);
          break;

        default:
          return response()->json(['error' => 'Invalid transaction status'], 400);
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
        ],
        'request' => $request,
      ]
    ], 200);
  }
  
  public function invoice(Request $request) 
  {
    $transaction_id = $request->query('order_id');

    $transaction = Transaction::find($transaction_id);
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

  public function index() 
  {
    $transaction = Transaction::all();
    try {
      return response()->json([
          'status' => 'success',
          'message' => 'Get all transaction success',
          'data' => $transaction,
        ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Get all transaction failed',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
