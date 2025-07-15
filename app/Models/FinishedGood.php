<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 'quantity', 'location', 'status', 'ready_for_shipment', 'damaged', 'returned'
    ];
}
