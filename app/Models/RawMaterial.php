<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'quantity', 'reorder_level', 'unit', 'supplier_id', 'status'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function incomingShipments()
    {
        return $this->hasMany(IncomingShipment::class);
    }
}
