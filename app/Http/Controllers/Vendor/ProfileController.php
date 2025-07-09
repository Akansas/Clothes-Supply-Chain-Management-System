<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class ProfileController extends Controller
{
    /**
     * Show the form for creating a new vendor profile.
     */
    public function create()
    {
        return view('vendor.profile-create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'business_type' => 'required|string|max:255',
        ]);
        Vendor::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'business_type' => $request->business_type,
            'status' => 'pending',
        ]);
        return redirect()->route('vendor.dashboard')->with('success', 'Vendor profile created successfully!');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $vendor = Vendor::where('user_id', $user->id)->firstOrFail();
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'business_type' => 'required|string|max:255',
        ]);
        $vendor->update([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'business_type' => $request->business_type,
        ]);
        return redirect()->route('vendor.dashboard')->with('success', 'Vendor profile updated successfully!');
    }
} 