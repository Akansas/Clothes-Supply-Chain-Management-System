@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Admin Dashboard</h2>
    <div class="mb-4">
        <h5>User Summary</h5>
        <div class="row">
            @foreach($roleCounts as $role => $count)
                <div class="col-md-2 mb-2">
                    <div class="card text-center">
                        <div class="card-body p-2">
                            <div class="fw-bold text-capitalize">{{ $role }}</div>
                            <div class="display-6">{{ $count }}</div>
                        </div>
                    </div>
                </div>
                        @endforeach
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Users</span>
            <form class="d-flex" method="GET" action="{{ route('admin.dashboard') }}">
                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search users..." value="{{ request('search') }}">
                <button class="btn btn-outline-primary btn-sm" type="submit">Search</button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-capitalize">{{ $user->role->display_name ?? $user->role->name ?? '-' }}</td>
                            <td>{{ $user->status ?? ($user->vendor->status ?? '-') }}</td>
                            <td>
                                <form action="{{ route('admin.impersonate') }}" method="POST" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Impersonate</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 