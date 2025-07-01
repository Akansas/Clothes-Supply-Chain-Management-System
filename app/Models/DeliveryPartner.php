<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'vehicle_type',
        'vehicle_number',
        'license_number',
        'service_areas',
        'availability',
        'experience_years',
        'rating',
        'total_deliveries',
        'completed_deliveries',
        'on_time_deliveries',
        'status',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'service_areas' => 'array',
    ];

    /**
     * Get the user associated with this delivery partner.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the deliveries for this delivery partner.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'driver_id', 'user_id');
    }

    /**
     * Get the delivery assignments for this delivery partner.
     */
    public function deliveryAssignments()
    {
        return $this->hasMany(Delivery::class, 'driver_id', 'user_id');
    }

    /**
     * Get the conversations with warehouses/vendors/retailers.
     */
    public function conversations()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'delivery_partner_id');
    }

    /**
     * Calculate on-time delivery rate.
     */
    public function getOnTimeRate()
    {
        if ($this->completed_deliveries == 0) {
            return 0;
        }
        return round(($this->on_time_deliveries / $this->completed_deliveries) * 100, 2);
    }

    /**
     * Check if delivery partner is available.
     */
    public function isAvailable()
    {
        return $this->availability === 'available' && $this->status === 'active';
    }

    /**
     * Scope for available delivery partners.
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available')->where('status', 'active');
    }

    /**
     * Scope for active delivery partners.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
