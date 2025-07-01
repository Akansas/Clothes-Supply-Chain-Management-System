<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'location_type',
        'location_id',
        'quantity',
        'reserved_quantity',
        'available_quantity',
        'location',
        'batch_number',
        'expiry_date',
        'condition', // new, damaged, returned
        'cost_per_unit',
        'last_restocked_at',
        'reorder_point',
        'max_stock',
        'status', // active, inactive, quarantined
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'available_quantity' => 'integer',
        'cost_per_unit' => 'decimal:2',
        'expiry_date' => 'date',
        'last_restocked_at' => 'datetime',
        'reorder_point' => 'integer',
        'max_stock' => 'integer',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->where('available_quantity', '<=', 'reorder_point');
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    // Methods
    public function updateAvailableQuantity()
    {
        $this->available_quantity = $this->quantity - $this->reserved_quantity;
        $this->save();
    }

    public function reserveQuantity($amount)
    {
        if ($this->available_quantity >= $amount) {
            $this->reserved_quantity += $amount;
            $this->updateAvailableQuantity();
            return true;
        }
        return false;
    }

    public function releaseReservedQuantity($amount)
    {
        if ($this->reserved_quantity >= $amount) {
            $this->reserved_quantity -= $amount;
            $this->updateAvailableQuantity();
            return true;
        }
        return false;
    }

    public function addStock($amount, $costPerUnit = null)
    {
        $this->quantity += $amount;
        if ($costPerUnit) {
            $this->cost_per_unit = $costPerUnit;
        }
        $this->last_restocked_at = now();
        $this->updateAvailableQuantity();
        $this->save();
    }

    public function removeStock($amount)
    {
        if ($this->available_quantity >= $amount) {
            $this->quantity -= $amount;
            $this->updateAvailableQuantity();
            $this->save();
            return true;
        }
        return false;
    }

    public function isLowStock()
    {
        return $this->available_quantity <= $this->reorder_point;
    }

    public function getStockValue()
    {
        return $this->quantity * $this->cost_per_unit;
    }

    public function getStockTurnoverRate()
    {
        // This would need to be calculated based on sales data
        // For now, returning a placeholder
        return 0;
    }
}
