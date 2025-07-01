<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delivery;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'retail_store_id',
        'order_number',
        'source',
        'status',
        'total_amount',
        'tax_amount',
        'shipping_amount',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'billing_address',
        'payment_method',
        'payment_status',
        'notes',
        'order_date',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'supplier_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'order_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relationships
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
            'confirmed' => 'badge-info',
            'processing' => 'badge-primary',
            'shipped' => 'badge-secondary',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-danger',
            'returned' => 'badge-dark',
            default => 'badge-light'
        };
    }

    public function getStatusText()
    {
        return ucfirst($this->status);
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
}
