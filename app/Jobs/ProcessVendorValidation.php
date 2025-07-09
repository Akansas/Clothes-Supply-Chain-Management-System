<?php

namespace App\Jobs;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ProcessVendorValidation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $applicationId;

    /**
     * Create a new job instance.
     */
    public function __construct($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $application = VendorApplication::find($this->applicationId);
        if (!$application) {
            Log::error('VendorApplication not found for validation: ' . $this->applicationId);
            return;
        }

        // Set status to validating
        $application->status = 'validating';
        $application->save();

        $pdfPath = storage_path('app/public/' . $application->pdf_path);

        // --- Real HTTP call to Java server (endpoint is a placeholder) ---
        try {
            $response = Http::attach(
                'pdf', fopen($pdfPath, 'r'), 'application.pdf'
            )->post('http://java-server/validate-vendor'); // TODO: Replace with real endpoint

            if ($response->successful()) {
                $result = $response->json();
                // Expected response: [
                //   'financial_stability' => float,
                //   'reputation' => float,
                //   'compliance' => float,
                //   'notes' => string,
                //   'status' => 'approved'|'rejected'
                // ]
                $application->validation_results = json_encode([
                    'financial_stability' => $result['financial_stability'] ?? null,
                    'reputation' => $result['reputation'] ?? null,
                    'compliance' => $result['compliance'] ?? null,
                ]);
                $application->validation_notes = $result['notes'] ?? null;
                $application->status = $result['status'] ?? 'rejected';
                $application->validated_at = Carbon::now();
                $application->save();
            } else {
                // Java server error
                $application->status = 'rejected';
                $application->validation_notes = 'Validation server error: ' . $response->body();
                $application->validated_at = Carbon::now();
                $application->save();
                Log::error('Java validation server error: ' . $response->body());
                return;
            }
        } catch (\Exception $e) {
            $application->status = 'rejected';
            $application->validation_notes = 'Validation server unreachable.';
            $application->validated_at = Carbon::now();
            $application->save();
            Log::error('Java validation server unreachable: ' . $e->getMessage());
            return;
        }

        // Auto-schedule facility visit if approved and no visit exists
        if ($application->status === 'approved') {
            $vendor = $application->vendor;
            $existingVisit = $vendor->facilityVisits()->where('status', 'scheduled')->first();
            if (!$existingVisit) {
                \App\Models\FacilityVisit::create([
                    'vendor_id' => $vendor->id,
                    'scheduled_date' => now()->addDays(7),
                    'status' => 'scheduled',
                    'inspector_id' => null,
                    'visit_notes' => 'Auto-scheduled after validation approval.',
                ]);
            }
        }
    }
} 