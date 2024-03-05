<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use App\Models\Campaign;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $campaign = Campaign::all();
      try {
        return response()->json([
            'status' => 'success',
            'message' => 'Get all campaign success',
            'data' => $campaign,
          ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Get all campaign failed',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      try {
        $request->validate([
          'name' => ['required', 'string', 'unique:campaigns'],
          'type' => ['required', 'string', 'in:kemanusiaan,kesehatan,pendidikan,tanggap bencana'],
          'current_donation' => ['nullable', 'numeric', 'min:0'],
          'target_donation' => ['required', 'numeric', 'min:0'],
          'start_date' => ['required', 'date'],
          'end_date' => ['required', 'date', 'after_or_equal:start_date'],
          'description' => ['nullable', 'string'],
          'photo' => ['nullable', 'mimes:png,jpg,jpeg,webp', 'max:16384'],
        ]);
  
        $data = $request->all();
        $campaign = new Campaign;
        $campaign->fill($data);
        
        $campaign->save();
        
        if ($request->has('photo')) {
          $path = public_path("assets/image/campaign/". $campaign->type . "/");
          $campaign_name = str_replace(' ', '_', $campaign->name);
          $campaign_photo = $campaign_name . '.' . $request->photo->extension();
          $request->photo->move($path, $campaign_photo);
          $campaign->photo = $campaign_photo;
          $campaign->save();
        }
        
        return response()->json([
          'status' => 'success',
          'message' => 'Create campaign success',
          'data' => $campaign,
        ], 201);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Create campaign failed',
          'error' => $e->getMessage(),
        ], 500);
      }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $campaign = Campaign::find($id);
      if (!$campaign) {
        return response()->json([
          'status' => 'error',
          'message' => 'Campaign not found with the given ID',
        ], 404);
      }
      try {
        return response()->json([
          'status' => 'success',
          'message' => 'Get campaign by id success',
          'data' => $campaign,
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Get campaign by id failed',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      $campaign = Campaign::find($id);
      if (!$campaign) {
        return response()->json([
          'status' => 'error',
          'message' => 'Campaign not found with the given ID',
        ], 404);
      }
      try {
        $request->validate([
          'name' => ['nullable', 'string', 'unique:campaigns,name,' . $campaign->id],
          'type' => ['nullable', 'string', 'in:kemanusiaan,kesehatan,pendidikan,tanggap bencana'],
          'current_donation' => ['nullable', 'numeric', 'min:0'],
          'target_donation' => ['nullable', 'numeric', 'min:0'],
          'start_date' => ['nullable', 'date'],
          'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
          'description' => ['nullable', 'string'],
          'photo' => ['nullable', 'mimes:png,jpg,jpeg,webp', 'max:16384'],
        ]);

        if ($request->has('photo')) {
          $path = public_path("assets/img/campaign");
  
          if ($campaign->photo) {
            File::delete($path . '/' . $campaign->photo);
          }
  
          $campaign_name = str_replace(' ', '_', $campaign->name);
          $timestamp = date('d-m-Y_H-i-s');
          $campaign_photo = $campaign_name . '-'. $campaign->id . '-' . $timestamp . '.' . $request->photo->extension();
          $request->photo->move($path, $campaign_photo);
        }

        $campaign->update([
          'name' => $request->input('name') ?? $campaign->name,
          'type' => $request->input('type') ?? $campaign->type,
          'current_donation' => $request->input('current_donation') ?? $campaign->current_donation,
          'target_donation' => $request->input('target_donation') ?? $campaign->target_donation,
          'start_date' => $request->input('start_date') ?? $campaign->start_date,
          'end_date' => $request->input('end_date') ?? $campaign->end_date,
          'description' => $request->input('description') ?? $campaign->description,
          'photo' => $campaign_photo ?? $campaign->photo,
        ]);
        
        return response()->json([
          'status' => 'success',
          'message' => 'Update campaign by id success',
          'data' => $campaign,
        ], 201);
      } catch (ValidationException $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Validation error',
          'error' => $e->errors(),
        ], 422);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Update campaign by id failed',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      $campaign = Campaign::find($id);
      if (!$campaign) {
        return response()->json([
          'status' => 'error',
          'message' => 'Campaign delete failed',
          'detail' => 'Campaign not found with the given ID',
        ], 404);
      }

      try {
        $campaign->delete();
  
        return response()->json([
          'status' => 'success',
          'message' => 'Campaign deleted successfully',
          'data' => $campaign,
        ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Error deleting campaign',
          'error' => $e->getMessage(),
        ], 500);
      }
    }
}
