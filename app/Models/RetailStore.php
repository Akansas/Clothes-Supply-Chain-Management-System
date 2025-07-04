<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailStore extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'manager_id',
        'status',
        'opening_hours',
        'description',
        'contact_person',
        'opening_time',
        'closing_time',
    ];

    protected $casts = [
        'opening_hours' => 'array',
    ];

    /**
     * Get the manager of the retail store.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the inventories for this retail store.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get the orders placed at this retail store.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
