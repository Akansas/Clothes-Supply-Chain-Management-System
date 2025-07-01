<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use App\Models\QualityCheck;
use App\Models\FacilityVisit;
use App\Models\ProductionOrder;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get quality checks assigned to this inspector
        $qualityChecks = QualityCheck::where('inspector_id', $user->id)
            ->with(['productionOrder.product'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Get facility visits assigned to this inspector
        $facilityVisits = FacilityVisit::where('inspector_id', $user->id)
            ->orderBy('scheduled_date', 'desc')
            ->take(5)
            ->get();
            
        // Get pending quality checks
        $pendingChecks = QualityCheck::where('inspector_id', $user->id)
            ->where('pass_fail', 'pending')
            ->count();
            
        // Get completed checks this month
        $completedThisMonth = QualityCheck::where('inspector_id', $user->id)
            ->where('pass_fail', 'pass')
            ->whereMonth('check_date', now()->month)
            ->count();
            
        // Get failed checks that need recheck
        $failedChecks = QualityCheck::where('inspector_id', $user->id)
            ->where('pass_fail', 'fail')
            ->where('recheck_required', true)
            ->count();
            
        // Get production orders awaiting inspection
        $awaitingInspection = ProductionOrder::whereHas('qualityChecks', function($query) use ($user) {
            $query->where('inspector_id', $user->id)
                  ->where('pass_fail', 'pending');
        })->count();

        return view('inspector.dashboard', compact(
            'qualityChecks',
            'facilityVisits',
            'pendingChecks',
            'completedThisMonth',
            'failedChecks',
            'awaitingInspection'
        ));
    }
    
    public function qualityChecks()
    {
        $user = auth()->user();
        $qualityChecks = QualityCheck::where('inspector_id', $user->id)
            ->with(['productionOrder.product'])
            ->orderBy('check_date', 'desc')
            ->paginate(20);
            
        return view('inspector.quality-checks', compact('qualityChecks'));
    }
    
    public function facilityVisits()
    {
        $user = auth()->user();
        $facilityVisits = FacilityVisit::where('inspector_id', $user->id)
            ->orderBy('scheduled_date', 'desc')
            ->paginate(20);
            
        return view('inspector.facility-visits', compact('facilityVisits'));
    }
    
    public function reports()
    {
        $user = auth()->user();
        
        // Monthly quality check statistics
        $monthlyStats = QualityCheck::where('inspector_id', $user->id)
            ->selectRaw('MONTH(check_date) as month, COUNT(*) as total, 
                        SUM(CASE WHEN pass_fail = "pass" THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN pass_fail = "fail" THEN 1 ELSE 0 END) as failed')
            ->whereYear('check_date', now()->year)
            ->groupBy('month')
            ->get();
            
        return view('inspector.reports', compact('monthlyStats'));
    }

    public function createQualityCheck()
    {
        $user = auth()->user();
        $productionOrders = \App\Models\ProductionOrder::all();
        $vendors = \App\Models\Vendor::all();
        return view('inspector.quality-checks-create', compact('productionOrders', 'vendors'));
    }

    public function storeQualityCheck(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'check_type' => 'required|string',
            'check_point' => 'required|string',
            'sample_size' => 'required|integer|min:1',
            'defects_found' => 'required|integer|min:0',
            'defect_types' => 'nullable|array',
            'quality_score' => 'required|integer|min:0|max:100',
            'pass_fail' => 'required|string',
            'check_date' => 'required|date',
            'notes' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'recheck_required' => 'nullable|boolean',
            'recheck_date' => 'nullable|date',
            'is_critical' => 'nullable|boolean',
            'vendor_id' => 'required|exists:vendors,id',
        ]);
        $validated['inspector_id'] = $user->id;
        $validated['defect_types'] = json_encode($validated['defect_types'] ?? []);
        \App\Models\QualityCheck::create($validated);
        return redirect()->route('inspector.quality-checks')->with('success', 'Quality check created successfully.');
    }

    public function showQualityCheck($id)
    {
        $user = auth()->user();
        $qualityCheck = \App\Models\QualityCheck::where('inspector_id', $user->id)->with(['productionOrder.product', 'inspector'])->findOrFail($id);
        return view('inspector.quality-checks-show', compact('qualityCheck'));
    }

    public function editQualityCheck($id)
    {
        $user = auth()->user();
        $qualityCheck = \App\Models\QualityCheck::where('inspector_id', $user->id)->findOrFail($id);
        $productionOrders = \App\Models\ProductionOrder::all();
        $vendors = \App\Models\Vendor::all();
        $qualityCheck->defect_types = json_decode($qualityCheck->defect_types, true);
        return view('inspector.quality-checks-edit', compact('qualityCheck', 'productionOrders', 'vendors'));
    }

    public function updateQualityCheck(\Illuminate\Http\Request $request, $id)
    {
        $user = auth()->user();
        $qualityCheck = \App\Models\QualityCheck::where('inspector_id', $user->id)->findOrFail($id);
        $validated = $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'check_type' => 'required|string',
            'check_point' => 'required|string',
            'sample_size' => 'required|integer|min:1',
            'defects_found' => 'required|integer|min:0',
            'defect_types' => 'nullable|array',
            'quality_score' => 'required|integer|min:0|max:100',
            'pass_fail' => 'required|string',
            'check_date' => 'required|date',
            'notes' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'recheck_required' => 'nullable|boolean',
            'recheck_date' => 'nullable|date',
            'is_critical' => 'nullable|boolean',
            'vendor_id' => 'required|exists:vendors,id',
        ]);
        $validated['defect_types'] = json_encode($validated['defect_types'] ?? []);
        $qualityCheck->update($validated);
        return redirect()->route('inspector.quality-checks')->with('success', 'Quality check updated successfully.');
    }

    public function createFacilityVisit()
    {
        $user = auth()->user();
        $vendors = \App\Models\Vendor::all();
        return view('inspector.facility-visits-create', compact('vendors'));
    }

    public function storeFacilityVisit(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'scheduled_date' => 'required|date',
            'actual_visit_date' => 'nullable|date',
            'status' => 'required|string',
            'visit_notes' => 'nullable|string',
            'inspection_results' => 'nullable|array',
            'passed_inspection' => 'nullable|boolean',
        ]);
        $validated['inspector_id'] = $user->id;
        $validated['inspector_name'] = $user->name;
        $validated['inspection_results'] = json_encode($validated['inspection_results'] ?? []);
        \App\Models\FacilityVisit::create($validated);
        return redirect()->route('inspector.facility-visits')->with('success', 'Facility visit created successfully.');
    }

    public function showFacilityVisit($id)
    {
        $user = auth()->user();
        $facilityVisit = \App\Models\FacilityVisit::where('inspector_id', $user->id)->with(['vendor', 'inspector'])->findOrFail($id);
        return view('inspector.facility-visits-show', compact('facilityVisit'));
    }

    public function editFacilityVisit($id)
    {
        $user = auth()->user();
        $facilityVisit = \App\Models\FacilityVisit::where('inspector_id', $user->id)->findOrFail($id);
        $vendors = \App\Models\Vendor::all();
        $facilityVisit->inspection_results = json_decode($facilityVisit->inspection_results, true);
        return view('inspector.facility-visits-edit', compact('facilityVisit', 'vendors'));
    }

    public function updateFacilityVisit(\Illuminate\Http\Request $request, $id)
    {
        $user = auth()->user();
        $facilityVisit = \App\Models\FacilityVisit::where('inspector_id', $user->id)->findOrFail($id);
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'scheduled_date' => 'required|date',
            'actual_visit_date' => 'nullable|date',
            'status' => 'required|string',
            'visit_notes' => 'nullable|string',
            'inspection_results' => 'nullable|array',
            'passed_inspection' => 'nullable|boolean',
        ]);
        $validated['inspection_results'] = json_encode($validated['inspection_results'] ?? []);
        $facilityVisit->update($validated);
        return redirect()->route('inspector.facility-visits')->with('success', 'Facility visit updated successfully.');
    }
} 