<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'role', 'skills', 'availability', 'manufacturer_id'
    ];

    public function assignments()
    {
        return $this->hasMany(WorkerAssignment::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
