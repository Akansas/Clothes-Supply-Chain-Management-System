@extends('layouts.app')
@section('title', 'Unauthorized')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1 text-danger">403</h1>
            <h2 class="mb-4">Unauthorized Access</h2>
            <p class="lead">Sorry, you do not have permission to access this page.</p>
            <a href="/" class="btn btn-primary mt-3">Go Home</a>
        </div>
    </div>
</div>
@endsection 