<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id', 'supplier_id', 'driver_id', 'status', 'tracking_number'
    ];

    /**
     * Get the order associated with this delivery.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the driver assigned to this delivery.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the warehouse from which this delivery originates.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
