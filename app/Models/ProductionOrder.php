<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'design_id',
        'product_id',
        'quantity',
        'size_breakdown', // JSON with size quantities
        'color_breakdown', // JSON with color quantities
        'fabric_requirements',
        'accessories_requirements',
        'production_line',
        'priority', // low, medium, high, urgent
        'status', // planned, in_production, completed, cancelled
        'start_date',
        'due_date',
        'completion_date',
        'estimated_cost',
        'actual_cost',
        'quality_score',
        'defect_rate',
        'production_notes',
        'quality_notes',
        'assigned_to',
        'supervisor_id',
        'is_rush_order',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'size_breakdown' => 'array',
        'color_breakdown' => 'array',
        'fabric_requirements' => 'array',
        'accessories_requirements' => 'array',
        'start_date' => 'date',
        'due_date' => 'date',
        'completion_date' => 'date',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'quality_score' => 'integer',
        'defect_rate' => 'decimal:2',
        'is_rush_order' => 'boolean',
    ];

    // Relationships
    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function productionStages()
    {
        return $this->hasMany(ProductionStage::class);
    }

    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeInProduction($query)
    {
        return $query->where('status', 'in_production');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('status', '!=', 'completed');
    }

    public function scopeRushOrders($query)
    {
        return $query->where('is_rush_order', true);
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
        if ($this->start_date && $this->completion_date) {
            return $this->start_date->diffInDays($this->completion_date);
        }
        return null;
    }

    public function getProgressPercentage()
    {
        if ($this->status === 'completed') return 100;
        if ($this->status === 'planned') return 0;
        
        // Calculate based on production stages
        $totalStages = $this->productionStages()->count();
        $completedStages = $this->productionStages()->where('status', 'completed')->count();
        
        return $totalStages > 0 ? ($completedStages / $totalStages) * 100 : 0;
    }

    public function getTotalQuantity()
    {
        return $this->quantity;
    }

    public function getDefectiveQuantity()
    {
        return round($this->quantity * ($this->defect_rate / 100));
    }

    public function getGoodQuantity()
    {
        return $this->quantity - $this->getDefectiveQuantity();
    }

    public function getCostVariance()
    {
        return $this->actual_cost - $this->estimated_cost;
    }

    public function getCostVariancePercentage()
    {
        if ($this->estimated_cost > 0) {
            return (($this->actual_cost - $this->estimated_cost) / $this->estimated_cost) * 100;
        }
        return 0;
    }

    public function startProduction()
    {
        $this->status = 'in_production';
        $this->start_date = now();
        $this->save();
    }

    public function completeProduction()
    {
        $this->status = 'completed';
        $this->completion_date = now();
        $this->save();
    }

    public function cancelProduction($reason = null)
    {
        $this->status = 'cancelled';
        $this->notes = $reason ? $this->notes . "\nCancelled: " . $reason : $this->notes;
        $this->save();
    }
} 