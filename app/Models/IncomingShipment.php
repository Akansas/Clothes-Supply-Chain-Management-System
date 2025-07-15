<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id', 'supplier_id', 'quantity', 'expected_date', 'received_date', 'status'
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
