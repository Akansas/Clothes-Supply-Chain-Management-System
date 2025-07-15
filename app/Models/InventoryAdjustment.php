<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'finished_good_id',
        'user_id',
        'adjustment_type',
        'quantity',
        'reason',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function finishedGood()
    {
        return $this->belongsTo(FinishedGood::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
