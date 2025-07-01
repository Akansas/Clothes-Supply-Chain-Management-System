<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'design_code',
        'description',
        'designer_id',
        'collection_id',
        'season',
        'year',
        'category', // tops, bottoms, dresses, outerwear, accessories
        'subcategory', // t-shirts, jeans, dresses, jackets, etc.
        'target_gender', // men, women, unisex, kids
        'target_age_group', // teens, young_adults, adults, seniors
        'style_tags', // casual, formal, streetwear, vintage, etc.
        'fabric_requirements',
        'color_palette',
        'size_range',
        'technical_specs',
        'sample_status', // concept, prototype, approved, rejected
        'production_status', // not_started, in_production, completed
        'cost_estimate',
        'retail_price_target',
        'sustainability_score',
        'image_urls',
        'design_files',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'style_tags' => 'array',
        'fabric_requirements' => 'array',
        'color_palette' => 'array',
        'size_range' => 'array',
        'technical_specs' => 'array',
        'image_urls' => 'array',
        'design_files' => 'array',
        'cost_estimate' => 'decimal:2',
        'retail_price_target' => 'decimal:2',
        'sustainability_score' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function samples()
    {
        return $this->hasMany(Sample::class);
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
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

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDesigner($query, $designerId)
    {
        return $query->where('designer_id', $designerId);
    }

    public function scopeApproved($query)
    {
        return $query->where('sample_status', 'approved');
    }

    // Methods
    public function getTotalProductionQuantity()
    {
        return $this->productionOrders()->sum('quantity');
    }

    public function getTotalRevenue()
    {
        return $this->products()->with('orderItems')->get()->sum(function($product) {
            return $product->orderItems->sum(function($item) {
                return $item->quantity * $item->unit_price;
            });
        });
    }

    public function getAverageRating()
    {
        // This would be calculated from customer reviews
        return 0;
    }

    public function isInProduction()
    {
        return $this->production_status === 'in_production';
    }

    public function isCompleted()
    {
        return $this->production_status === 'completed';
    }
} 