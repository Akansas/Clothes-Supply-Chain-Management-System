<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type', 'item_id', 'action', 'quantity', 'user_id', 'reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
