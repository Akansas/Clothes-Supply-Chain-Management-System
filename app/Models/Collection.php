<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'collection_code',
        'description',
        'season',
        'year',
        'theme',
        'inspiration',
        'target_market',
        'launch_date',
        'end_date',
        'status', // planning, in_design, in_production, launched, archived
        'budget',
        'expected_revenue',
        'designer_id',
        'image_url',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'launch_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'expected_revenue' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Design::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySeason($query, $season)
    {
        return $query->where('season', $season);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeLaunched($query)
    {
        return $query->where('status', 'launched');
    }

    // Methods
    public function getTotalDesigns()
    {
        return $this->designs()->count();
    }

    public function getTotalProducts()
    {
        return $this->products()->count();
    }

    public function getTotalRevenue()
    {
        return $this->products()->with('orderItems')->get()->sum(function($product) {
            return $product->orderItems->sum(function($item) {
                return $item->quantity * $item->unit_price;
            });
        });
    }

    public function getProfitMargin()
    {
        if ($this->budget > 0) {
            return (($this->getTotalRevenue() - $this->budget) / $this->budget) * 100;
        }
        return 0;
    }

    public function isLaunched()
    {
        return $this->status === 'launched';
    }

    public function isArchived()
    {
        return $this->status === 'archived';
    }
} 