<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryFeedback extends Model
{
    use HasFactory;

    protected $table = 'delivery_feedback';
    protected $fillable = [
        'delivery_id', 'order_id', 'customer_id', 'rating', 'comment'
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
} 