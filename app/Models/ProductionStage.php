<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'stage_name', // cutting, sewing, finishing, quality_check, packaging
        'stage_order',
        'estimated_duration_hours',
        'actual_duration_hours',
        'start_time',
        'end_time',
        'status', // pending, in_progress, completed, delayed
        'assigned_to',
        'supervisor_id',
        'quality_score',
        'defect_count',
        'notes',
        'is_critical_path',
    ];

    protected $casts = [
        'estimated_duration_hours' => 'decimal:2',
        'actual_duration_hours' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'quality_score' => 'integer',
        'defect_count' => 'integer',
        'is_critical_path' => 'boolean',
    ];

    // Relationships
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
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

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeDelayed($query)
    {
        return $query->where('status', 'delayed');
    }

    public function scopeCriticalPath($query)
    {
        return $query->where('is_critical_path', true);
    }

    // Methods
    public function startStage()
    {
        $this->status = 'in_progress';
        $this->start_time = now();
        $this->save();
    }

    public function completeStage()
    {
        $this->status = 'completed';
        $this->end_time = now();
        $this->actual_duration_hours = $this->start_time ? 
            $this->start_time->diffInHours($this->end_time) : 0;
        $this->save();
    }

    public function markDelayed($reason = null)
    {
        $this->status = 'delayed';
        $this->notes = $reason ? $this->notes . "\nDelayed: " . $reason : $this->notes;
        $this->save();
    }

    public function getDurationVariance()
    {
        return $this->actual_duration_hours - $this->estimated_duration_hours;
    }

    public function isOnTime()
    {
        return $this->actual_duration_hours <= $this->estimated_duration_hours;
    }

    public function getEfficiency()
    {
        if ($this->actual_duration_hours > 0) {
            return ($this->estimated_duration_hours / $this->actual_duration_hours) * 100;
        }
        return 0;
    }
} 