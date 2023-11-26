<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
      return response()->json([
        'status' => 'success',
        'message' => 'You are now viewing admin dashboard page'
      ]);

      // Kalau udah ada view return view
      // return view('adminPage');
    }
}
