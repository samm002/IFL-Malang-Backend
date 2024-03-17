<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Transaction;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $donation = Donation::all();
      try {
        return response()->json([
            'status' => 'success',
            'message' => 'Get all donation success',
            'data' => $donation,
          ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Get all donation failed',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function donate(Request $request, $id)
    {
      $user = auth()->user();

      $campaign = Campaign::find($id);
      if (!$campaign) {
        return response()->json([
          'status' => 'error',
          'message' => 'Campaign not found with the given ID',
        ], 404);
      }

      $data = $request->only('name', 'anonim', 'donation_amount', 'donation_message', 'status', 'user_id', 'campaign_id');
      $data['campaign_id'] = $campaign->id;
      $data['user_id'] = $user->id;
      $data['status'] = 'unpaid';
      
      if($user) {
        $data['email'] = $user->email;
      }

      $validator = Validator::make($data, [
        'name' => ['required', 'string', 'max:255'],
        'anonim' => ['required', 'numeric'],
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
        $donation = Donation::create($data);

        $transaction = Transaction::create([
          'donation_id' => $donation->id,
          'user_id' => $user->id ?? null,
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
            'name' => $campaign->name,
            'category'=> $campaign->type,
          )
        );

        $customer_details = array(
          'first_name' => $donation->name ?? 'anonim',
          'email' => $donation->email,
        );

        $transaction_data = array(
          'transaction_details' => $transaction_details,
          'item_details' => $campaign_details,
          'customer_details' => $customer_details,
        );

        $snapToken = Snap::getSnapToken($transaction_data);
        $paymentUrl = Snap::createTransaction($transaction_data)->redirect_url;

        return response()->json([
          'status' => 'success',
          'message' => 'succesfully create donation',
          'snap_token' => $snapToken,
          'payment_url' => $paymentUrl,
        ], 201);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Failed to create donation',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $donation = Donation::find($id);
      if (!$donation) {
        return response()->json([
          'status' => 'error',
          'message' => 'Donation not found with the given ID',
        ], 404);
      }
      try {
        return response()->json([
          'status' => 'success',
          'message' => 'Get donation by id success',
          'data' => $donation,
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Get donation by id failed',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      $donation = Donation::find($id);
      if (!$donation) {
        return response()->json([
          'status' => 'error',
          'message' => 'Donation not found with the given ID',
        ], 404);
      }

      try {
        $donation->delete();
  
        return response()->json([
          'status' => 'success',
          'message' => 'Donation deleted successfully',
          'data' => $donation,
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Error deleting donation',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    public function deleteAll()
    {
      try {
        $totalDonation = Donation::count();

        Donation::truncate();
  
        return response()->json([
          'status' => 'success',
          'message' => 'Delete all donation successfully',
          'data' => ['Total donation deleted' => $totalDonation]
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Delete all donation failed',
          'error' => $e->getMessage(),
        ], 500);
      }
    }
}
