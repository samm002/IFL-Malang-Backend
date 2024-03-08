<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
      $request->validate([
        'title' => ['required', 'string', 'unique:campaigns'],
        'user_id' => ['required', 'uuid', 'exist:users'],
        'short_description' => ['required', 'string'],
        'body' => ['required', 'string'],
        'view_count' => ['nullable', 'integer', 'min:0'],
        'status' => ['required', 'in:active,closed,pending'],
        'current_donation' => ['nullable', 'numeric', 'min:0'],
        'target_donation' => ['required', 'numeric', 'min:0'],
        'publish_date' => ['required', 'date'],
        'end_date' => ['required', 'date', 'after_or_equal:publish_date'],
        'note' => ['nullable', 'string'],
        'receiver' => ['required', 'string'],
        'image' => ['nullable', 'mimes:png,jpg,jpeg,webp', 'max:16384'],
        'categories' => ['required'],
      ]);
      try {
        DB::beginTransaction();

        $data = $request->all();
        $campaign = new Campaign;
        $campaign->fill($data);
        
        $campaign->save();

        if ($request->has('image')) {
          $campaign_name = Str::slug($campaign->title);
          $campaign_image = $request->image->storeAs(
            'assets/image/campaign', $campaign_name . '.' . $request->image->extension() 
          );
          $campaign->image = basename($campaign_image);
          $campaign->save();
        }

        if ($request->has('categories')) {
          $campaign->categories()->attach($request->categories);
        }

        DB::commit();

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

      $request->validate([
        'title' => ['nullable', 'string', 'unique:campaigns,name,' . $campaign->id],
        'short_description' => ['nullable', 'string'],
        'body' => ['nullable', 'string'],
        'view_count' => ['nullable', 'integer', 'min:0'],
        'status' => ['nullable', 'in:active,closed,pending'],
        'current_donation' => ['nullable', 'numeric', 'min:0'],
        'target_donation' => ['nullable', 'numeric', 'min:0'],
        'publish_date' => ['nullable', 'date'],
        'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        'note' => ['nullable', 'string'],
        'receiver' => ['nullable', 'string'],
        'image' => ['nullable', 'mimes:png,jpg,jpeg,webp', 'max:16384'],
        'categories' => ['nullable', 'exist:categories'],
      ]);

      try {
        if ($request->hasFile('image')) {
          $path = public_path("assets/image/campaign");
          if ($campaign->image) {
            $old_image = $path . "/" . $campaign->image;
            Storage::delete($old_image);
          }
          $campaign_name = Str::slug($campaign->title);
          $campaign_image = $campaign_name . '.' . $request->image->extension();
          $request->file('image')->storeAs(
            '/assets/image/campaign', $campaign_name . '.' . $request->image->extension(), 'local'
          );
          $campaign->image = $campaign_image;
          $campaign->save();
      }
        if ($request->has('image')) {
          $path = public_path("assets/image/campaign");
  
          if ($campaign->image) {
            File::delete($path . '/' . $campaign->image);
          }
  
          $campaign_name = Str::slug($campaign->title);
          $campaign_image = $campaign_name . '.' . $request->image->extension();
          $request->image->move($path, $campaign_image);
        }

        $campaign->update([
          'title' => $request->input('title') ?? $campaign->title,
          'short_description' => $request->input('short_description') ?? $campaign->short_description,
          'body' => $request->input('body') ?? $campaign->short_description,
          'view_count' => $request->input('view_count') ?? $campaign->view_count,
          'status' => $request->input('status') ?? $campaign->status,
          'current_donation' => $request->input('current_donation') ?? $campaign->current_donation,
          'target_donation' => $request->input('target_donation') ?? $campaign->target_donation,
          'start_date' => $request->input('start_date') ?? $campaign->start_date,
          'end_date' => $request->input('end_date') ?? $campaign->end_date,
          'note' => $request->input('note') ?? $campaign->note,
          'receiver' => $request->input('receiver') ?? $campaign->receiver,
          'image' => $campaign_image ?? $campaign->image,
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
        if ($campaign->image) {
          $path = public_path("assets/image/campaign");
          File::delete($path . '/' . $campaign->image);
        }

        $campaign->categories()->detach();

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
