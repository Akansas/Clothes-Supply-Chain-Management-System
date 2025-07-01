<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'material',
        'size',
        'color',
        'price',
        'cost',
        'category',
        'unit',
        'min_stock_level',
        'max_stock_level',
        'is_active',
        'supplier_id',
        'manufacturer_id',
        'design_id',
        'vendor_id',
        'season',
        'collection',
        'fabric_type',
        'care_instructions',
        'sustainability_rating',
        'lead_time_days',
        'moq', // Minimum Order Quantity
        'weight_kg',
        'dimensions',
        'barcode',
        'image_url',
        'stock_quantity',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
        'sustainability_rating' => 'integer',
        'lead_time_days' => 'integer',
        'moq' => 'integer',
        'weight_kg' => 'decimal:2',
        'dimensions' => 'array',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(RawMaterialSupplier::class, 'supplier_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(User::class, 'manufacturer_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function design()
    {
        return $this->belongsTo(Design::class, 'design_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function demandPredictions()
    {
        return $this->hasMany(DemandPrediction::class);
    }

    public function inventory()
    {
        return $this->hasOne(\App\Models\Inventory::class, 'product_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySeason($query, $season)
    {
        return $query->where('season', $season);
    }

    public function scopeByCollection($query, $collection)
    {
        return $query->where('collection', $collection);
    }

    // Methods
    public function getCurrentStock()
    {
        return $this->inventories()->sum('quantity');
    }

    public function isLowStock()
    {
        return $this->getCurrentStock() <= $this->min_stock_level;
    }

    public function getProfitMargin()
    {
        if ($this->cost > 0) {
            return (($this->price - $this->cost) / $this->cost) * 100;
        }
        return 0;
    }

    public function getTotalValue()
    {
        return $this->getCurrentStock() * $this->cost;
    }
}
