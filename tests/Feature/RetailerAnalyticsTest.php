<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RetailerAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected $retailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->retailer = User::factory()->create(['role_id' => 4]); // Assume 4 = retailer
        $this->actingAs($this->retailer, 'sanctum');
    }

    public function test_sales_insights_endpoint()
    {
        $response = $this->getJson('/api/retailer/analytics/sales-insights');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_sales' => ['daily', 'weekly'],
                'top_selling_skus',
                'sales_by_channel' => ['in_store', 'online'],
                'avg_transaction_value',
            ]);
    }

    public function test_inventory_intelligence_endpoint()
    {
        $response = $this->getJson('/api/retailer/analytics/inventory-intelligence');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'stock_levels_per_location',
                'inventory_turnover_ratio',
                'aging_stock_report',
                'reorder_point_prediction',
            ]);
    }

    public function test_customer_behavior_endpoint()
    {
        $response = $this->getJson('/api/retailer/analytics/customer-behavior');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'customer_lifetime_value',
                'purchase_frequency',
                'product_preferences',
                'return_rate_by_segment',
            ]);
    }

    public function test_pricing_promotion_endpoint()
    {
        $response = $this->getJson('/api/retailer/analytics/pricing-promotion');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'markdown_impact_on_sales',
                'campaign_roi',
                'elasticity_analysis',
                'seasonal_performance',
            ]);
    }

    public function test_omnichannel_engagement_endpoint()
    {
        $response = $this->getJson('/api/retailer/analytics/omnichannel-engagement');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'cart_abandonment_rate',
                'store_foot_traffic',
                'social_media_mentions',
                'return_rate_online_vs_store' => ['online', 'store'],
            ]);
    }

    public function test_actionable_alerts_endpoint()
    {
        $response = $this->getJson('/api/retailer/analytics/actionable-alerts');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'low_stock_alerts',
                'product_bundling_suggestions',
                'new_trend_alerts',
                'reorder_automation_triggers',
            ]);
    }

    public function test_market_trends_endpoint()
    {
        $response = $this->getJson('/api/retailer/analytics/market-trends');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'trending_products',
                'market_growth_rate',
                'seasonal_trends',
                'competitive_benchmarking',
            ]);
    }

    public function test_unauthorized_access()
    {
        $this->withHeaders(['Authorization' => ''])->getJson('/api/retailer/analytics/sales-insights')
            ->assertStatus(401);
    }
} 