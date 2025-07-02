<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Show the form for creating a new supplier profile.
     */
    public function create()
    {
        return view('supplier.profile.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'material_types' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        \App\Models\RawMaterialSupplier::create([
            'user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['contact_person'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            // 'specializations' => explode(',', $validated['material_types']), // removed, column does not exist
        ]);

        return redirect()->route('supplier.dashboard')->with('success', 'Profile created successfully!');
    }

    public function edit()
    {
        $user = auth()->user();
        $supplierProfile = \App\Models\RawMaterialSupplier::where('user_id', $user->id)->firstOrFail();
        return view('supplier.profile.edit', compact('supplierProfile'));
    }

    public function destroy()
    {
        $user = auth()->user();
        $supplierProfile = \App\Models\RawMaterialSupplier::where('user_id', $user->id)->first();
        if ($supplierProfile) {
            $supplierProfile->delete();
            return redirect()->route('profile.edit')->with('success', 'Supplier profile deleted successfully.');
        }
        return redirect()->route('profile.edit')->with('error', 'Supplier profile not found.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $supplierProfile = \App\Models\RawMaterialSupplier::where('user_id', $user->id)->firstOrFail();
        $supplierProfile->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Supplier profile updated successfully.');
    }
} 