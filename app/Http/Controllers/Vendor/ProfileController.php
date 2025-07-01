<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Show the form for creating a new vendor profile.
     */
    public function create()
    {
        return view('vendor.profile-create');
    }
} 