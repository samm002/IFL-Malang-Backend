<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
  public function notAdmin(Request $request)
  {
    if (!$request->bearerToken()) {
      return response()->json([
        'status' => 'error',
        'message' => 'Authorization Token not found'
      ], 401);
    } else {
      return response()->json([
        'status' => 'error',
        'message' => 'You are not authorized to access this page',
      ], 403);
    }
  }
}
