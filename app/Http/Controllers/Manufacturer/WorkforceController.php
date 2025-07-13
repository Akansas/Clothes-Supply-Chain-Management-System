<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\Shift;
use App\Models\WorkerAssignment;

class WorkforceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manufacturerId = auth()->user()->manufacturer_id;
        $workers = Worker::where('manufacturer_id', $manufacturerId)->get();
        $shifts = Shift::where('manufacturer_id', $manufacturerId)->get();
        $assignments = WorkerAssignment::whereIn('worker_id', $workers->pluck('id'))->get();
        return view('manufacturer.workforce.index', compact('workers', 'shifts', 'assignments'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
