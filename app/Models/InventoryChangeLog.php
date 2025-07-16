<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryChangeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'item_type',
        'change_type',
        'old_quantity',
        'new_quantity',
        'user_id',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 