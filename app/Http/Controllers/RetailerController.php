<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RetailerController extends Controller
{
    public function dashboard()
    {
        // You can pass data to the view here later
        return view('retailer.dashboard');
    }
} 