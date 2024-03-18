<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign_Category;
use App\Models\Campaign;
use App\Models\Category;

class Campaign_CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $campaign_category = Campaign_Category::all();

      $data = $campaign_category->map(function ($campaign_category) {
        $pivotId = $campaign_category->id;
        $campaign = Campaign::find($campaign_category->campaign_id);
        $category = Category::find($campaign_category->category_id);

        $campaign_category_data = [
          'id' => $pivotId,
          'category_name' => $category->name,
          'campaign_id' => $campaign->id,
          'category_id' => $category->id,
          'created_at' => $campaign_category->created_at,
          'updated_at' => $campaign_category->updated_at,
        ];
        return $campaign_category_data;
      });

      try {
        return response()->json([
            'status' => 'success',
            'message' => 'Get all campaign_category success',
            'data' => $data,
          ], 200);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Get all campaign_category failed',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
      $campaign_category = Campaign_Category::find($id);
      if (!$campaign_category) {
        return response()->json([
          'status' => 'error',
          'message' => 'Campaign_category delete failed',
          'detail' => 'Campaign_category not found with the given ID',
        ], 404);
    }
}
