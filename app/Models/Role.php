<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the dashboard route for this role.
     */
    public function getDashboardRoute()
    {
        $routes = [
            'admin' => '/admin/dashboard',
            'vendor' => '/vendor/dashboard',
            'manufacturer' => '/manufacturer/dashboard',
            'warehouse_manager' => '/warehouse/dashboard',
            'warehouse' => '/warehouse/dashboard',
            'retailer' => '/retailer/dashboard',
            'delivery_personnel' => '/delivery/dashboard',
            'delivery' => '/delivery/dashboard',
            'customer' => '/customer/dashboard',
            'raw_material_supplier' => '/supplier/dashboard',
            'supplier' => '/supplier/dashboard',
            'inspector' => '/inspector/dashboard',
        ];

        return $routes[$this->name] ?? '/dashboard';
    }

    /**
     * Check if role can access a specific module.
     */
    public function canAccessModule($module)
    {
        $permissions = [
            'vendor' => ['vendor_management', 'chat', 'reports'],
            'manufacturer' => ['inventory_management', 'vendor_management', 'chat', 'analytics', 'reports'],
            'warehouse_manager' => ['inventory_management', 'order_management', 'chat', 'analytics', 'reports'],
            'retailer' => ['inventory_management', 'order_management', 'customer_management', 'chat', 'analytics', 'reports'],
            'delivery_personnel' => ['delivery_management', 'chat', 'reports'],
            'customer' => ['order_management', 'chat', 'reports'],
            'inspector' => ['quality_management', 'facility_management', 'chat', 'reports'],
        ];

        return in_array($module, $permissions[$this->name] ?? []);
    }
}
