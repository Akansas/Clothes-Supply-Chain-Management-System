<?php

$supplier=is_array($supplier ??null)? $supplier : [];
return [
'menu' => array_merge([
    // ... other menu items
    
    [
                'text' => 'Dashboard',
                'url'  => '/supplier/dashboard',
                'icon' => 'fas fa-tachometer-alt',
            ],
            [
                'text' => 'Orders',
                'url'  => '/supplier/orders',
                'icon' => 'fas fa-shopping-cart',
            ],
            [
                'text' => 'Shipments',
                'url'  => '/supplier/shipments',
                'icon' => 'fas fa-boxes',
            ],
            [
                'text' => 'Inventory',
                'url'  => '/supplier/inventory',
                'icon' => 'fas fa-warehouse',
            ],
            [
                'text' => 'Profile',
                'url'  => '/supplier/profile',
                'icon' => 'fas fa-user',
            ],
        ],$supplier),

    ];
