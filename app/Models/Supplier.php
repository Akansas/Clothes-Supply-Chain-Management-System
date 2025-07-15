<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact_info', 'address', 'email', 'phone'
    ];

    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function incomingShipments()
    {
        return $this->hasMany(IncomingShipment::class);
    }
}
