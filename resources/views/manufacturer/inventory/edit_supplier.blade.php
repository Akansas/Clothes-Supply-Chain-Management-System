@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Edit Supplier</h2>
    <form action="{{ route('inventory.suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $supplier->name }}" required>
        </div>
        <div class="mb-3">
            <label for="contact_info" class="form-label">Contact Info</label>
            <input type="text" name="contact_info" id="contact_info" class="form-control" value="{{ $supplier->contact_info }}">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ $supplier->address }}">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $supplier->email }}">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $supplier->phone }}">
        </div>
        <button type="submit" class="btn btn-success">Update Supplier</button>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 