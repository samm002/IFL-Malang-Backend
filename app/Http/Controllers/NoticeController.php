<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NoticeController extends Controller
{
  // Rencana nanti responsenya diganti view
  public function emailNotVerifiedNotice(Request $request)
  {
    if (!$request->bearerToken()) {
      return response()->json([
        'status' => 'error',
        'message' => 'Authorization Token not found'
      ], 401);
    } else {
      return response()->json([
        'status' => 'error',
        'message' => 'Cannot perform request, your email has not been verified'
      ], 401);
    }
  }

  public function userNotAdminNotice(Request $request)
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
