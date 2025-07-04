@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Edit Manufacturer Profile</h2>
    <form method="POST" action="{{ route('manufacturer.profile.update') }}">
        @csrf
        <div class="mb-3">
            <label>Company Name</label>
            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $manufacturer->company_name) }}" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $manufacturer->address) }}">
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $manufacturer->phone) }}">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $manufacturer->email) }}" required>
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>
</div>
@endsection 