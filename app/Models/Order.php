<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delivery;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'design_id',
        'product_id',
        'quantity',
        'size_breakdown',
        'color_breakdown',
        'fabric_requirements',
        'accessories_requirements',
        'production_line',
        'priority',
        'status',
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
        'user_id',
        'retailer_id',
        'manufacturer_id',
        'supplier_id',
        'total_amount',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function retailStore()
    {
        return $this->belongsTo(RetailStore::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'order_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(User::class, 'manufacturer_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Methods
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-primary',
            'rejected' => 'badge-danger',
            'delivered' => 'badge-success',
            default => 'badge-secondary',
        };
    }

    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'delivered' => 'Delivered',
            default => ucfirst($this->status ?? 'Unknown'),
        };
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function getTotalItems()
    {
        return $this->orderItems->sum('quantity');
    }

    public function getEstimatedDeliveryDate()
    {
        if ($this->shipped_at) {
            return $this->shipped_at->addDays(3); // 3 days delivery estimate
        }
        return null;
    }

    public function isOverdue()
    {
        if ($this->status === 'shipped' && $this->shipped_at) {
            return $this->shipped_at->addDays(5)->isPast(); // 5 days overdue
        }
        return false;
    }
    public function supplier()
    {
        return $this->belongsTo(\App\Models\RawMaterialSupplier::class, 'supplier_id');
    }

}
