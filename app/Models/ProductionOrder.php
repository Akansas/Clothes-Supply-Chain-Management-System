<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $fillable = [
        'order_number',
        'manufacturer_id',
        'retailer_id',
        'product_id',
        'quantity',
        'due_date',
        'status',
        'notes',
        'completed_at',
        // Add other fields as needed
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stages()
    {
        return $this->hasMany(ProductionStage::class);
    }

    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }

    public function retailer()
    {
        return $this->belongsTo(\App\Models\User::class, 'retailer_id');
    }

    public function startProduction()
    {
        $this->status = 'in_production';
        $this->save();
    }

    public function completeProduction()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }
} 