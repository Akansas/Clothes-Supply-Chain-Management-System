<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'inspector_id',
        'inspector_name',
        'scheduled_date',
        'actual_visit_date',
        'status',
        'visit_notes',
        'inspection_results',
        'passed_inspection',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'actual_visit_date' => 'datetime',
        'inspection_results' => 'array',
        'passed_inspection' => 'boolean',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // Methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function getVisitDate()
    {
        return $this->actual_visit_date ?? $this->scheduled_date;
    }
}
