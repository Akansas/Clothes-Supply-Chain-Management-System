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

    // Auto-generate order_number if not provided
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = 'PO-' . time() . '-' . strtoupper(uniqid());
            }
        });
    }

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