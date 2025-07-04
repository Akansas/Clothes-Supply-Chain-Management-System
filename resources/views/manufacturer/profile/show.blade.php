@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Manufacturer Profile</h2>
    <p><strong>Company Name:</strong> {{ $manufacturer->company_name }}</p>
    <p><strong>Address:</strong> {{ $manufacturer->address }}</p>
    <p><strong>Phone:</strong> {{ $manufacturer->phone }}</p>
    <p><strong>Email:</strong> {{ $manufacturer->email }}</p>
    <a href="{{ route('manufacturer.profile.edit') }}" class="btn btn-primary">Edit Profile</a>
</div>
@endsection 