<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'business_type',
        'status',
        'financial_stability_score',
        'reputation_score',
        'compliance_score',
        'notes',
        'user_id',
        'specializations',
        'certifications',
    ];

    protected $casts = [
        'financial_stability_score' => 'decimal:2',
        'reputation_score' => 'decimal:2',
        'compliance_score' => 'decimal:2',
    ];

    /**
     * Get the user associated with this vendor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor applications for this vendor.
     */
    public function applications()
    {
        return $this->hasMany(VendorApplication::class);
    }

    /**
     * Get the facility visits for this vendor.
     */
    public function facilityVisits()
    {
        return $this->hasMany(FacilityVisit::class);
    }

    /**
     * Get the latest application.
     */
    public function latestApplication()
    {
        return $this->hasOne(VendorApplication::class)->latest();
    }

    /**
     * Get the latest facility visit.
     */
    public function latestFacilityVisit()
    {
        return $this->hasOne(FacilityVisit::class)->latest();
    }

    /**
     * Calculate overall score based on all criteria.
     */
    public function getOverallScore()
    {
        $scores = array_filter([
            $this->financial_stability_score,
            $this->reputation_score,
            $this->compliance_score
        ]);

        if (empty($scores)) {
            return 0;
        }

        return round(array_sum($scores) / count($scores), 2);
    }

    /**
     * Check if vendor is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if vendor is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Scope for approved vendors.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending vendors.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the product offers from manufacturers.
     */
    public function productOffers()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    /**
     * Get the retailer orders placed to this vendor.
     */
    public function retailerOrders()
    {
        return $this->hasMany(Order::class, 'vendor_id');
    }

    /**
     * Get the conversations with manufacturers/warehouses/retailers.
     */
    public function conversations()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'vendor_id');
    }
}
