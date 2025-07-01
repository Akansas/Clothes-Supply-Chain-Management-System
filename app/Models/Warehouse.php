<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'manager_id',
        'status',
        'capacity',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Get the inventories stored in this warehouse.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get the manager of the warehouse.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the deliveries originating from this warehouse.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get the product offers from manufacturers/vendors.
     */
    public function productOffers()
    {
        return $this->hasMany(Product::class, 'warehouse_id');
    }

    /**
     * Get the retailer orders fulfilled by this warehouse.
     */
    public function retailerOrders()
    {
        return $this->hasMany(Order::class, 'warehouse_id');
    }

    /**
     * Get the delivery assignments from this warehouse.
     */
    public function deliveryAssignments()
    {
        return $this->hasMany(Delivery::class, 'warehouse_id');
    }

    /**
     * Get the conversations with vendors/retailers/delivery personnel.
     */
    public function conversations()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'warehouse_id');
    }
}
