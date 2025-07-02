@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Create Supplier Profile</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('supplier.profile.store') }}">
        @csrf
        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" required>
        </div>
        <div class="mb-3">
            <label for="contact_person" class="form-label">Contact Person</label>
            <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required>
        </div>
        <div class="mb-3">
            <label for="material_types" class="form-label">Material Types Supplied</label>
            <input type="text" class="form-control" id="material_types" name="material_types" placeholder="e.g. Cotton, Polyester, Zippers" value="{{ old('material_types') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
</div>
@endsection 