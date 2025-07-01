<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'check_type', // in_process, final, random, customer_return
        'check_point', // cutting, sewing, finishing, packaging
        'inspector_id',
        'sample_size',
        'defects_found',
        'defect_types', // JSON array of defect types and counts
        'quality_score',
        'pass_fail', // pass, fail, conditional_pass
        'check_date',
        'notes',
        'corrective_actions',
        'recheck_required',
        'recheck_date',
        'recheck_inspector_id',
        'recheck_result',
        'is_critical',
    ];

    protected $casts = [
        'sample_size' => 'integer',
        'defects_found' => 'integer',
        'defect_types' => 'array',
        'quality_score' => 'integer',
        'check_date' => 'datetime',
        'recheck_date' => 'datetime',
        'recheck_required' => 'boolean',
        'is_critical' => 'boolean',
    ];

    // Relationships
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function recheckInspector()
    {
        return $this->belongsTo(User::class, 'recheck_inspector_id');
    }

    public function product()
    {
        // Assumes QualityCheck belongs to ProductionOrder, which belongs to Product
        return $this->hasOneThrough(
            Product::class,
            ProductionOrder::class,
            'id', // Foreign key on ProductionOrder table...
            'id', // Foreign key on Product table...
            'production_order_id', // Local key on QualityCheck table...
            'product_id' // Local key on ProductionOrder table...
        );
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('check_type', $type);
    }

    public function scopeByResult($query, $result)
    {
        return $query->where('pass_fail', $result);
    }

    public function scopePassed($query)
    {
        return $query->where('pass_fail', 'pass');
    }

    public function scopeFailed($query)
    {
        return $query->where('pass_fail', 'fail');
    }

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeRecheckRequired($query)
    {
        return $query->where('recheck_required', true);
    }

    // Methods
    public function getDefectRate()
    {
        return $this->sample_size > 0 ? ($this->defects_found / $this->sample_size) * 100 : 0;
    }

    public function isPassed()
    {
        return in_array($this->pass_fail, ['pass', 'conditional_pass']);
    }

    public function isFailed()
    {
        return $this->pass_fail === 'fail';
    }

    public function requiresRecheck()
    {
        return $this->recheck_required;
    }

    public function getQualityGrade()
    {
        if ($this->quality_score >= 95) return 'A';
        if ($this->quality_score >= 85) return 'B';
        if ($this->quality_score >= 75) return 'C';
        if ($this->quality_score >= 65) return 'D';
        return 'F';
    }

    public function scheduleRecheck($inspectorId, $date = null)
    {
        $this->recheck_required = true;
        $this->recheck_inspector_id = $inspectorId;
        $this->recheck_date = $date ?? now()->addDays(1);
        $this->save();
    }

    public function completeRecheck($result, $notes = null)
    {
        $this->recheck_result = $result;
        $this->recheck_required = false;
        if ($notes) {
            $this->notes = $this->notes . "\nRecheck: " . $notes;
        }
        $this->save();
    }

    public function getDefectTypeCount($defectType)
    {
        return $this->defect_types[$defectType] ?? 0;
    }

    public function getMostCommonDefect()
    {
        if (empty($this->defect_types)) return null;
        
        $maxCount = max($this->defect_types);
        $defectTypes = array_keys($this->defect_types, $maxCount);
        
        return $defectTypes[0] ?? null;
    }
} 