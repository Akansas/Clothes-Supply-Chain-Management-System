@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Complete Your Vendor Profile</h2>
    <form action="{{ route('vendor.profile.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Vendor Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="contact_person" class="form-label">Contact Person</label>
            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
            @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" required>{{ old('address') }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="business_type" class="form-label">Business Type</label>
            <select class="form-select @error('business_type') is-invalid @enderror" id="business_type" name="business_type" required>
                <option value="">Select type</option>
                <option value="Manufacturer" {{ old('business_type') == 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                <option value="Retailer" {{ old('business_type') == 'Retailer' ? 'selected' : '' }}>Retailer</option>
                <option value="Raw Material Supplier" {{ old('business_type') == 'Raw Material Supplier' ? 'selected' : '' }}>Raw Material Supplier</option>
                <option value="Component Supplier" {{ old('business_type') == 'Component Supplier' ? 'selected' : '' }}>Component Supplier</option>
            </select>
            @error('business_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-success">Save Profile</button>
    </form>
</div>
@endsection 