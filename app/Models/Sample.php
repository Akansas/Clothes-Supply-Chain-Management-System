<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_id',
        'sample_code',
        'sample_type', // prototype, fit_sample, production_sample, photo_sample
        'size',
        'color',
        'fabric_used',
        'quantity',
        'status', // requested, in_production, completed, approved, rejected
        'requested_by',
        'assigned_to',
        'request_date',
        'due_date',
        'completion_date',
        'cost',
        'quality_score',
        'fit_notes',
        'design_notes',
        'production_notes',
        'image_urls',
        'is_approved',
        'approval_date',
        'approved_by',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'due_date' => 'date',
        'completion_date' => 'date',
        'approval_date' => 'date',
        'cost' => 'decimal:2',
        'quality_score' => 'integer',
        'quantity' => 'integer',
        'image_urls' => 'array',
        'is_approved' => 'boolean',
    ];

    // Relationships
    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('sample_type', $type);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'in_production');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('status', '!=', 'completed');
    }

    // Methods
    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now() && $this->status !== 'completed';
    }

    public function getDaysRemaining()
    {
        if (!$this->due_date) return null;
        
        $days = now()->diffInDays($this->due_date, false);
        return $days > 0 ? $days : 0;
    }

    public function getProductionTime()
    {
        if ($this->request_date && $this->completion_date) {
            return $this->request_date->diffInDays($this->completion_date);
        }
        return null;
    }

    public function approve($approvedBy)
    {
        $this->is_approved = true;
        $this->status = 'approved';
        $this->approval_date = now();
        $this->approved_by = $approvedBy;
        $this->save();
    }

    public function reject($reason, $rejectedBy)
    {
        $this->is_approved = false;
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->approved_by = $rejectedBy;
        $this->save();
    }

    public function markCompleted()
    {
        $this->status = 'completed';
        $this->completion_date = now();
        $this->save();
    }
} 