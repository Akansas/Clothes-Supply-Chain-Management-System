<!DOCTYPE html>
<html>
<head>
    <title>Debug Suppliers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .user { margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 3px; }
        .role { margin: 5px 0; padding: 5px; background: #e9e9e9; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Debug Suppliers</h1>
    
    <div class="section">
        <h2>Current User</h2>
        <div class="user">
            <strong>ID:</strong> {{ auth()->user()->id }}<br>
            <strong>Name:</strong> {{ auth()->user()->name }}<br>
            <strong>Email:</strong> {{ auth()->user()->email }}<br>
            <strong>Role ID:</strong> {{ auth()->user()->role_id }}<br>
            <strong>Role Name:</strong> {{ auth()->user()->role ? auth()->user()->role->name : 'NO ROLE' }}<br>
            <strong>Has Role 'manufacturer':</strong> {{ auth()->user()->hasRole('manufacturer') ? 'YES' : 'NO' }}<br>
            <strong>Has Role 'supplier':</strong> {{ auth()->user()->hasRole('supplier') ? 'YES' : 'NO' }}
        </div>
    </div>

    <div class="section">
        <h2>All Roles</h2>
        @foreach(\App\Models\Role::all() as $role)
            <div class="role">
                <strong>ID:</strong> {{ $role->id }} | 
                <strong>Name:</strong> {{ $role->name }} | 
                <strong>Display Name:</strong> {{ $role->display_name ?? 'N/A' }}
            </div>
        @endforeach
    </div>

    <div class="section">
        <h2>All Users</h2>
        @foreach(\App\Models\User::with('role')->get() as $user)
            <div class="user">
                <strong>ID:</strong> {{ $user->id }} | 
                <strong>Name:</strong> {{ $user->name }} | 
                <strong>Email:</strong> {{ $user->email }} | 
                <strong>Role ID:</strong> {{ $user->role_id }} | 
                <strong>Role Name:</strong> {{ $user->role ? $user->role->name : 'NO ROLE' }}
            </div>
        @endforeach
    </div>

    <div class="section">
        <h2>Supplier Users (Query Result)</h2>
        @php
            $suppliers = \App\Models\User::with('role')->whereHas('role', function($q) { 
                $q->where('name', 'supplier'); 
            })->get();
        @endphp
        @if($suppliers->count() > 0)
            @foreach($suppliers as $supplier)
                <div class="user">
                    <strong>ID:</strong> {{ $supplier->id }} | 
                    <strong>Name:</strong> {{ $supplier->name }} | 
                    <strong>Email:</strong> {{ $supplier->email }} | 
                    <strong>Role ID:</strong> {{ $supplier->role_id }} | 
                    <strong>Role Name:</strong> {{ $supplier->role ? $supplier->role->name : 'NO ROLE' }}
                </div>
            @endforeach
        @else
            <p><strong>No suppliers found!</strong></p>
        @endif
    </div>

    <div class="section">
        <h2>Manufacturer Users (Query Result)</h2>
        @php
            $manufacturers = \App\Models\User::with('role')->whereHas('role', function($q) { 
                $q->where('name', 'manufacturer'); 
            })->get();
        @endphp
        @if($manufacturers->count() > 0)
            @foreach($manufacturers as $manufacturer)
                <div class="user">
                    <strong>ID:</strong> {{ $manufacturer->id }} | 
                    <strong>Name:</strong> {{ $manufacturer->name }} | 
                    <strong>Email:</strong> {{ $manufacturer->email }} | 
                    <strong>Role ID:</strong> {{ $manufacturer->role_id }} | 
                    <strong>Role Name:</strong> {{ $manufacturer->role ? $manufacturer->role->name : 'NO ROLE' }}
                </div>
            @endforeach
        @else
            <p><strong>No manufacturers found!</strong></p>
        @endif
    </div>
</body>
</html> 