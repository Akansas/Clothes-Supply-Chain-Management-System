@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">{{ ucfirst($role->name) }} Users</h1>
            <p class="text-muted">Manage users with {{ $role->display_name ?? $role->name }} role</p>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        @if($role->name !== 'manufacturer')
                                            <th>Phone</th>
                                        @endif
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Impersonate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <span class="text-white fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        @if($role->name !== 'manufacturer')
                                            <td>
                                                @if($role->name === 'vendor')
                                                    @if($user->vendor && $user->vendor->phone)
                                                        <span class="text-success">{{ $user->vendor->phone }}</span>
                                                    @elseif($user->phone)
                                                        <span class="text-info">{{ $user->phone }}</span>
                                                    @else
                                                        <span class="text-muted">No phone</span>
                                                    @endif
                                                @elseif($role->name === 'raw_material_supplier')
                                                    @if($user->rawMaterialSupplier && $user->rawMaterialSupplier->phone)
                                                        <span class="text-success">{{ $user->rawMaterialSupplier->phone }}</span>
                                                    @elseif($user->phone)
                                                        <span class="text-info">{{ $user->phone }}</span>
                                                    @else
                                                        <span class="text-muted">No phone</span>
                                                    @endif
                                                @elseif($role->name === 'retailer')
                                                    @if($user->managedRetailStore && $user->managedRetailStore->phone)
                                                        <span class="text-success">{{ $user->managedRetailStore->phone }}</span>
                                                    @elseif($user->phone)
                                                        <span class="text-info">{{ $user->phone }}</span>
                                                    @else
                                                        <span class="text-muted">No phone</span>
                                                    @endif
                                                @else
                                                    {{ $user->phone ?? 'N/A' }}
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if($role->name === 'vendor')
                                                @if($user->vendor)
                                                    @php
                                                        $latestApplication = $user->vendor->latestApplication;
                                                        $status = $latestApplication ? $latestApplication->status : 'no_application';
                                                    @endphp
                                                                                                    @if($status === 'approved')
                                                    <span class="badge bg-success text-white">Validated</span>
                                                @elseif($status === 'pending')
                                                    <span class="badge bg-warning text-dark">Pending Validation</span>
                                                @elseif($status === 'validating')
                                                    <span class="badge bg-info text-white">Validating</span>
                                                @elseif($status === 'rejected')
                                                    <span class="badge bg-danger text-white">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">No Application</span>
                                                @endif
                                                @else
                                                    <span class="badge bg-secondary text-white">No Profile</span>
                                                @endif
                                            @else
                                                @if($user->is_active)
                                                    <span class="badge bg-success text-white">Active</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Inactive</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.impersonate') }}" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-info" title="Impersonate User">
                                                    <i class="fas fa-user-secret"></i> Impersonate
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No users found</h5>
                            <p class="text-muted">There are no users with the {{ $role->display_name ?? $role->name }} role.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.text-primary { color: #4e73df !important; }
.text-success { color: #1cc88a !important; }
.text-info { color: #36b9cc !important; }

/* Ensure badges are always visible */
.badge {
    display: inline-block !important;
    padding: 0.35em 0.65em !important;
    font-size: 0.75em !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    text-align: center !important;
    white-space: nowrap !important;
    vertical-align: baseline !important;
    border-radius: 0.375rem !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Bootstrap 5 badge colors */
.bg-success { background-color: #198754 !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-info { background-color: #0dcaf0 !important; }
.bg-danger { background-color: #dc3545 !important; }
.bg-secondary { background-color: #6c757d !important; }
.text-white { color: #fff !important; }
.text-dark { color: #212529 !important; }
.text-warning { color: #f6c23e !important; }
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
@endsection 